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

namespace App;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Wildcard;

return [
    (new Route(new PathUri('/test')))
        ->withAddedMethodController(
            new MethodControllerName(
                new Method('GET'),
                new ControllerName(Controllers\Home::class)
            )
        )
        ->withName('test'),
    (new Route(new PathUri('/test/{wildcard}')))
        ->withAddedMethodController(
            new MethodControllerName(
                new Method('GET'),
                new ControllerName(Controllers\Home::class)
            )
        )
        ->withAddedWildcard(new Wildcard('wildcard')),
];
