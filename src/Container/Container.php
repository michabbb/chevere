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

namespace Chevere\Container;

use Chevere\Container\Exceptions\ContainerException;
use Chevere\Container\Exceptions\ContainerNotFoundException;
use Chevere\Container\Interfaces\ContainerInterface;
use Chevere\DataStructure\Traits\MapTrait;
use Throwable;

final class Container implements ContainerInterface
{
    use MapTrait;

    public function withPut(string $id, mixed $service): ContainerInterface
    {
        $new = clone $this;
        $new->map = $this->map->withPut($id, $service);

        return $new;
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException();
        }

        try {
            return $this->map->get($id);
        }
        // @codeCoverageIgnoreStart
        catch (Throwable $e) {
            throw new ContainerException();
        }
        // @codeCoverageIgnoreEnd
    }

    public function has(string $id): bool
    {
        return $this->map->has($id);
    }
}
