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

namespace Chevere\Tests\DataStructure\_resources;

use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapToArrayTrait;

final class UsesMapToArrayTrait
{
    use MapToArrayTrait;

    public function __construct()
    {
        $this->map = new Map(...[
            0 => 'test',
        ]);
    }

    public function map(): Map
    {
        return $this->map;
    }
}
