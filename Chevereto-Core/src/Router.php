<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use LogicException;

/**
 * Routes takes a bunch of Routes and generates a routing table (php array).
 */
class Router
{
    const PRIORITY_ORDER = [Route::TYPE_STATIC, Route::TYPE_MIXED, Route::TYPE_DYNAMIC];

    const ID = 'id';
    const SET = 'set';

    /**
     * @var array An array containing Route members (objects, serialized)
     *            fileHandle => [id => @Route],
     */
    protected $routes;

    /** @var array An array containing the unique route keys (/api/endpoint, /blog) */
    protected $routeKeys;

    /** @var array An array containing the named routes [name => [id, fileHandle]] */
    protected $namedRoutes;

    /** @var array An array containing a mapped representation, used when resolving routing. */
    protected $routing;

    /** @var array Arguments taken from wildcard matches. */
    protected $arguments;

    public function addRoute(Route $route, string $basename)
    {
        $route->fill();
        $id = $route->getId();
        $key = $route->getKey();
        if ($keyedRoute = $this->getRouteKeys()[$key] ?? null) {
            throw new LogicException(
                (string) (new Message('Route key %s has been already declared by %r.'))
                    ->code('%s', $key)
                    ->code('%r', $keyedRoute[0].'@'.$keyedRoute[1])
            );
        }
        $pointer = [$route->getId(), $basename];
        if ($name = $route->getName()) {
            $namedRoute = $this->getNamedRoutes()[$name] ?? null;
            if (isset($namedRoute)) {
                throw new LogicException(
                    (string) (new Message('Route name %s has been already taken by %r.'))
                        ->code('%s', $name)
                        ->code('%r', $namedRoute[0].'@'.$namedRoute[1])
                );
            }
            $this->namedRoutes[$name] = $pointer;
        }

        $this->routes[$basename][$id] = $route;

        // Use $route->powerSet when needed
        if (null != $route->getPowerSet()) {
            foreach ($route->getPowerSet() as $k => $wildcardsIndex) {
                // n => .. => regex => [route, wildcards]
                $this->routing($pointer, $route, $k); // $route->regex($k)
            }
        } else {
            // n => .. => regex => route
            $this->routing($pointer, $route);
        }
        ksort($this->routing);
        $this->routeKeys[$key] = $pointer;
    }

    public function getRoutes(): array
    {
        return $this->routes ?? [];
    }

    public function getRouteKeys(): array
    {
        return $this->routeKeys ?? [];
    }

    public function getNamedRoutes(): array
    {
        return $this->namedRoutes ?? [];
    }

    public function getRouting(): array
    {
        return $this->routing ?? [];
    }

    public function getArguments(): array
    {
        return $this->arguments ?? [];
    }

    /**
     * Group a Route into the routing table.
     *
     * @param array  $pointer  Route pointer [id, handle]
     * @param Route  $route    route object
     * @param string $routeSet route set, used when dealing with a powerSet
     */
    protected function routing(array $pointer, Route $route, string $routeSet = null): void
    {
        if ($routeSet) {
            $routeSetHandle = $routeSet;
            $regex = $route->regex($routeSetHandle);
        } else {
            $routeSetHandle = $route->getSet() ?? $route->getKey();
            $regex = $route->regex();
        }
        // Determine grouping type (static, mixed, dynamic)
        if (null == $route->getSet()) {
            $type = Route::TYPE_STATIC;
        } else {
            if (null != $routeSetHandle) {
                $pregReplace = preg_replace('/{[0-9]+}/', '', $routeSetHandle);
                if (null != $pregReplace) {
                    $pregReplace = trim(Path::normalize($pregReplace), '/');
                }
            }
            $type = isset($pregReplace) ? Route::TYPE_MIXED : Route::TYPE_DYNAMIC;
        }
        if (null != $routeSetHandle) {
            $routeSetHandleTrim = ltrim($routeSetHandle, '/');
            $explode = explode('/', $routeSetHandleTrim);
            $count = $route->getKey() == '/' ? 0 : count($explode);
        } else {
            $count = 0;
        }
        $var = [static::ID => $pointer];
        if ($routeSet) {
            $var[static::SET] = $routeSetHandle;
        }
        $this->routing[$count][$type][$regex] = $var;
    }

    /**
     * Resolve routing for the given path info.
     *
     * @param string $pathInfo request path
     */
    public function resolve(string $pathInfo): ?Route
    {
        $requestTrim = ltrim($pathInfo, '/');
        $components = $requestTrim == null ? [] : explode('/', $requestTrim);
        $componentsCount = count($components);
        foreach (static::PRIORITY_ORDER as $type) {
            $routesTable = $this->getRouting()[$componentsCount][$type] ?? null;
            if ($routesTable === null) {
                continue;
            }
            foreach ($routesTable as $regex => $prop) {
                if (preg_match("#$regex#", $pathInfo, $matches)) {
                    array_shift($matches);
                    $this->arguments = $matches;
                    $pointer = $prop[static::ID];
                    $routeSome = $this->routes[$pointer[1]][$pointer[0]] ?? null;
                    if ($routeSome instanceof Route) {
                        return $routeSome;
                    }
                    if (is_string($routeSome)) {
                        $this->routes[$pointer[1]][$pointer[0]] = unserialize($routeSome, ['allowed_classes' => Route::class]);

                        return $this->routes[$pointer[1]][$pointer[0]];
                    } else {
                        throw new LogicException(
                            (string) (new Message('Unexpected type %t in routes table %h.'))
                                ->code('%t', gettype($routeSome))
                                ->code('%h', $pointer[0].'@'.$pointer[1])
                        );
                    }
                }
            }
        }

        return null;
    }
}
