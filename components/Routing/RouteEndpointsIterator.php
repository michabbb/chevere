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

namespace Chevere\Components\Routing;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteEndpoints;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Routing\ExpectingControllerException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteEndpointsInterface;
use Chevere\Interfaces\Routing\RouteEndpointIteratorInterface;

/**
 * Iterates over the target dir for files matching `RouteEndpointInterface::KNOWN_METHODS` filenames.
 */
final class RouteEndpointsIterator implements RouteEndpointIteratorInterface
{
    private RouteEndpointsInterface $routeEndpoints;

    public function __construct(DirInterface $dir)
    {
        $this->routeEndpoints = new RouteEndpoints;
        $path = $dir->path();
        foreach (RouteEndpointInterface::KNOWN_METHODS as $name => $methodClass) {
            $controllerPath = $path->getChild($name . '.php');
            if (!$controllerPath->exists()) {
                continue;
            }
            $controller = $this->getController($controllerPath);
            $this->routeEndpoints = $this->routeEndpoints
                ->withPut(
                    new RouteEndpoint(new $methodClass, $controller)
                );
        }
    }

    public function routeEndpoints(): RouteEndpointsInterface
    {
        return $this->routeEndpoints;
    }

    private function getController(PathInterface $path): ControllerInterface
    {
        try {
            return (new FilePhpReturn(new FilePhp(new File($path))))
                ->withStrict(false)
                ->varType(new Type(ControllerInterface::class));
        } catch (FileReturnInvalidTypeException $e) {
            throw new ExpectingControllerException($e->message());
        }
    }
}
