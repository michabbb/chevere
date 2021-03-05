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

namespace Chevere\Interfaces\DataStructure;

/**
 * Describes the component in charge of providing an immutable Ds\Map interface.
 */
interface MapInterface extends MappedInterface
{
    public function __construct(mixed ...$namedArguments);

    public function withPut(mixed ...$namedValues): self;

    public function hasKey(string ...$keys): bool;

    public function assertHasKey(string ...$keys): void;

    public function getKey(string $key);
}
