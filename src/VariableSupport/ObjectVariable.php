<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\VariableSupport;

use Chevere\Iterator\Breadcrumb;
use Chevere\Iterator\Interfaces\BreadcrumbInterface;
use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\VariableSupport\Exceptions\ObjectNotClonableException;
use Chevere\VariableSupport\Interfaces\ObjectVariableInterface;
use ReflectionNamedType;
use ReflectionObject;

final class ObjectVariable implements ObjectVariableInterface
{
    private BreadcrumbInterface $breadcrumb;

    public function __construct(
        private object $variable
    ) {
        $this->breadcrumb = new Breadcrumb();
    }

    public function variable(): object
    {
        return $this->variable;
    }

    /**
     * @throws ObjectNotClonableException
     */
    public function assertClonable(): void
    {
        $this->assertVariableClonable($this->variable);
    }

    private function assertVariableClonable(mixed $variable): void
    {
        if (is_object($variable)) {
            $this->breadcrumbObject($variable);
        } elseif (is_iterable($variable)) {
            $this->breadcrumbIterable($variable);
        }
    }

    /**
     * @param iterable<mixed, mixed> $variable
     * @throws ObjectNotClonableException
     * @throws OutOfBoundsException
     */
    private function breadcrumbIterable(iterable $variable): void
    {
        $this->breadcrumb = $this->breadcrumb->withAdded('(iterable)');
        $iterableKey = $this->breadcrumb->pos();
        foreach ($variable as $key => $val) {
            $key = strval($key);
            $this->breadcrumb = $this->breadcrumb
                ->withAdded('key: ' . $key);
            $memberKey = $this->breadcrumb->pos();
            $this->assertVariableClonable($val);
            $this->breadcrumb = $this->breadcrumb
                ->withRemoved($memberKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemoved($iterableKey);
    }

    private function breadcrumbObject(object $variable): void
    {
        $this->breadcrumb = $this->breadcrumb
            ->withAdded('object: ' . $variable::class);
        $objectKey = $this->breadcrumb->pos();
        $reflection = new ReflectionObject($variable);
        if (!$reflection->isCloneable()) {
            throw new ObjectNotClonableException(
                message: message('Object is not clonable at %at%')
                    ->withCode('%at%', $this->breadcrumb->__toString())
            );
        }
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            /** @var ?ReflectionNamedType $namedType */
            $namedType = $property->getType();
            $propertyType = $namedType !== null
                ? $namedType->getName() . ' '
                : '';
            $this->breadcrumb = $this->breadcrumb
                ->withAdded(
                    'property: '
                    . $propertyType
                    . '$' . $property->getName()
                );
            $propertyKey = $this->breadcrumb->pos();
            // @infection-ignore-all
            $property->setAccessible(true);
            if ($property->isInitialized($variable)) {
                $this->assertVariableClonable($property->getValue($variable));
            }
            $this->breadcrumb = $this->breadcrumb
                ->withRemoved($propertyKey);
        }
        $this->breadcrumb = $this->breadcrumb
            ->withRemoved($objectKey);
    }
}
