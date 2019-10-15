<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Middlewares;

use Chevere\Components\Interfaces\MiddlewareInterface;
use Chevere\Components\Http\Request\RequestException;
use Chevere\Contracts\App\MiddlewareHandlerContract;

class RoleAdmin implements MiddlewareInterface
{
    public function __construct(MiddlewareHandlerContract $handler)
    {
        $userRole = 'user';
        if ('admin' != $userRole) {
            return $handler->stop(
                new RequestException(401, sprintf('User must have the admin role, %s role found', $userRole))
            );
        }
        return $handler->handle();
    }
};
