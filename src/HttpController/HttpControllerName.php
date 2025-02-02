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

namespace Chevere\HttpController;

use function Chevere\Common\assertClassName;
use Chevere\HttpController\Interfaces\HttpControllerInterface;
use Chevere\HttpController\Interfaces\HttpControllerNameInterface;

final class HttpControllerName implements HttpControllerNameInterface
{
    /**
     * @param class-string<HttpControllerInterface> $name
     */
    public function __construct(
        private string $name
    ) {
        assertClassName(HttpControllerInterface::class, $name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
