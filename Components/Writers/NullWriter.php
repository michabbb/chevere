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

namespace Chevere\Components\Writers;

use Chevere\Interfaces\Writers\WriterInterface;

final class NullWriter implements WriterInterface
{
    public function __construct()
    {
    }

    public function write(string $string): void
    {
    }

    public function toString(): string
    {
        return '';
    }
}
