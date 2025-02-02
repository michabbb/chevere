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

namespace Chevere\Parameter\Traits;

use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeArray;

trait ArrayParameterTrait
{
    private ParametersInterface $items;

    private TypeInterface $type;

    private bool $isList = false;

    /**
     * @return array<mixed, mixed>
     */
    public function default(): ?array
    {
        return $this->default;
    }

    public function typeSchema(): string
    {
        $type = $this->type->primitive();
        $subType = $this->isList() ? 'list' : 'map';
        $type .= "#{$subType}";

        return $type;
    }

    public function schema(): array
    {
        $items = [];
        foreach ($this->items as $name => $parameter) {
            $items[$name] = [
                'required' => $this->items->isRequired($name),
            ] + $parameter->schema();
        }

        return [
            'type' => $this->typeSchema(),
            'description' => $this->description(),
            'default' => $this->default(),
            'items' => $items,
        ];
    }

    public function items(): ParametersInterface
    {
        return $this->items;
    }

    public function isList(): bool
    {
        return $this->isList;
    }

    public function isMap(): bool
    {
        return ! $this->isList();
    }

    abstract public function description(): string;

    private function getType(): TypeInterface
    {
        return typeArray();
    }
}
