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

namespace Chevere\Http\Interfaces;

use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;

/**
 * Describes the component in charge of defining a middleware.
 */
interface MiddlewareInterface extends ServerMiddlewareInterface
{
    /**
     * @return int<400, 599>
     */
    public static function statusError(): int;
}
