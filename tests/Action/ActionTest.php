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

namespace Chevere\Tests\Action;

use Chevere\Container\Container;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Tests\Action\_resources\src\ActionTestAction;
use Chevere\Tests\Action\_resources\src\ActionTestContainerAction;
use Chevere\Tests\Action\_resources\src\ActionTestMissingRunAction;
use Chevere\Tests\Action\_resources\src\ActionTestParamsAction;
use Chevere\Tests\Action\_resources\src\ActionTestParamsAttributesAction;
use Chevere\Throwable\Exceptions\LogicException;
use Ds\Vector;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new ActionTestAction();
        $this->assertSame('test', $action->description());
        $this->assertCount(0, $action->parameters());
        $this->assertCount(1, $action->responseParameters());
        $arguments = new Arguments($action->parameters());
        $action->run($arguments);
    }

    public function testActionMissingRun(): void
    {
        $this->expectException(LogicException::class);
        new ActionTestMissingRunAction();
    }

    public function testActionParams(): void
    {
        $defaults = [
            'intDefault' => 1,
            'stringDefault' => 'default',
            'boolDefault' => false,
            'floatDefault' => 0.0,
            'arrayDefault' => [],
            'objectDefault' => null,
        ];
        $types = [
            'int' => IntegerParameterInterface::class,
            'string' => StringParameterInterface::class,
            'bool' => BooleanParameterInterface::class,
            'float' => FloatParameterInterface::class,
            'array' => ArrayParameterInterface::class,
            'object' => ObjectParameterInterface::class,
            'file' => ObjectParameterInterface::class,
        ];
        $optional = new Vector(array_keys($defaults));
        $required = new Vector(array_keys($types));
        $action = new ActionTestParamsAction();
        $this->assertTrue($optional->contains(...$action->parameters()->optional()));
        $this->assertTrue($required->contains(...$action->parameters()->required()));
        foreach ($defaults as $parameter => $value) {
            $parameter = $action->parameters()->get($parameter);
            $this->assertSame($value, $parameter->default());
        }
        foreach ($types as $parameter => $class) {
            $this->assertInstanceOf($class, $action->parameters()->get($parameter));
        }
    }

    public function testActionParamsAttributes(): void
    {
        $action = new ActionTestParamsAttributesAction();
        $this->assertSame('An int', $action->parameters()->get('int')->description());
        /** @var StringParameterInterface $parameter */
        $parameter = $action->parameters()->get('name');
        $this->assertSame('The name', $parameter->description());
        $this->assertSame('/^[a-z]$/', $parameter->regex()->__toString());
    }

    public function testActionContainer(): void
    {
        $container = new Container();
        $container = $container->withPut('id', 123);
        $action = new ActionTestContainerAction();
        $action = $action->withContainer($container);
        $response = $action->runner();
        $this->assertSame(0, $response->code());
    }

    public function testActionContainerException(): void
    {
        $action = new ActionTestContainerAction();
        $this->expectException(LogicException::class);
        $action->runner();
    }
}
