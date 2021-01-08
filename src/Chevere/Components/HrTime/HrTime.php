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

namespace Chevere\Components\HrTime;

use Chevere\Interfaces\HrTime\HrTimeInterface;

final class HrTime implements HrTimeInterface
{
    /**
     * @var int High-resolution time
     */
    private int $hrTime;

    /**
     * @var string Readable time, in ms with its unit like `100 ms`
     */
    private string $hrTimeReadMs;

    /**
     * @param int $hrTime High-resolution time.
     */
    public function __construct(int $hrTime)
    {
        $this->hrTime = $hrTime;
    }

    public function toReadMs(): string
    {
        return $this->hrTimeReadMs
            ??= number_format($this->hrTime / 1e+6, 2) . ' ms';
    }
}
