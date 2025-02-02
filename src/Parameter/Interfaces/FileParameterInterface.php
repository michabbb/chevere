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

namespace Chevere\Parameter\Interfaces;

/**
 * Describes the component in charge of defining a file parameter of type array.
 */
interface FileParameterInterface extends ArrayTypeParameterInterface
{
    /**
     * Provides access to the default value (if any).
     *
     * @return array<string, mixed>
     */
    public function default(): ?array;

    public function assertCompatible(self $parameter): void;
}
