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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParameterAssertArrayTypeTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class ArrayParameter implements ArrayParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;
    use ParameterAssertArrayTypeTrait;

    /**
     * @var array<mixed, mixed>
     */
    private ?array $default;

    public function setUp(): void
    {
        $this->parameters = new Parameters();
    }

    public function withDefault(array $value): static
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function withRequired(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->removeConflictKeys(...$parameter);
        $new->parameters = $new->parameters
            ->withAddedRequired(...$parameter);

        return $new;
    }

    public function withOptional(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->removeConflictKeys(...$parameter);
        $new->parameters = $new->parameters
            ->withAddedOptional(...$parameter);

        return $new;
    }

    public function withOut(string ...$name): static
    {
        $new = clone $this;
        $new->parameters = $new->parameters
            ->withOut(...$name);

        return $new;
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function assertCompatible(ArrayParameterInterface $parameter): void
    {
        $this->assertArrayType($parameter);
    }

    private function removeConflictKeys(ParameterInterface ...$parameter): void
    {
        $keys = array_keys($parameter);
        /** @var string[] $diff */
        $diff = array_intersect($keys, $this->parameters->keys());
        $this->parameters = $this->parameters->withOut(...$diff);
    }
}
