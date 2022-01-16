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

namespace Chevere\Tests\Router;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Router\RouterIndex;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Router\RouteIdentifierInterface;
use Chevere\Tests\Router\_resources\src\TestController;
use PHPUnit\Framework\TestCase;

final class RouterIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $routerIndex = new RouterIndex();
        $this->assertSame([], $routerIndex->toArray());
    }

    public function testGetRouteIdentifier(): void
    {
        $routerIndex = new RouterIndex();
        $this->expectException(OutOfBoundsException::class);
        $routerIndex->getRouteIdentifier('not-found');
    }

    public function testGetGroupRouteNames(): void
    {
        $routerIndex = new RouterIndex();
        $this->expectException(OutOfBoundsException::class);
        $routerIndex->getGroupRouteNames('not-found');
    }

    public function testGetRouteGroup(): void
    {
        $routerIndex = new RouterIndex();
        $this->expectException(OutOfBoundsException::class);
        $routerIndex->getRouteGroup('not-found');
    }

    public function testWithAddedRouteInvalidGroup(): void
    {
        $route = new Route('test', new RoutePath('/'));
        $routerIndex = new RouterIndex();
        $this->expectException(InvalidArgumentException::class);
        $routerIndex->withAddedRoute($route, ' ');
    }

    public function testWithAddedRoute(): void
    {
        $groupName = 'some-group';
        $path = '/path';
        $route = new Route('test', new RoutePath($path));
        $routeWithAddedEndpoint = $route->withAddedEndpoint(
            new RouteEndpoint(new GetMethod(), new TestController())
        );
        $this->assertNotSame($route, $routeWithAddedEndpoint);
        $routerIndex = new RouterIndex();
        $routerIndexWithAddedRoute = $routerIndex
            ->withAddedRoute($routeWithAddedEndpoint, $groupName);
        $this->assertNotSame($routerIndex, $routerIndexWithAddedRoute);
        $this->assertTrue($routerIndexWithAddedRoute->hasRouteName($path));
        $this->assertInstanceOf(
            RouteIdentifierInterface::class,
            $routerIndexWithAddedRoute->getRouteIdentifier($path)
        );
        $this->assertTrue($routerIndexWithAddedRoute->hasGroup($groupName));
        $this->assertSame(
            [$path],
            $routerIndexWithAddedRoute->getGroupRouteNames($groupName)
        );
        $this->assertSame(
            $groupName,
            $routerIndexWithAddedRoute->getRouteGroup($path)
        );
        $this->assertSame([
            $path => [
                'group' => $groupName,
                'name' => $path,
            ],
        ], $routerIndexWithAddedRoute->toArray());
        $path2 = '/path-2';
        $route2 = new Route('test', new RoutePath($path2));
        $route2 = $route2->withAddedEndpoint(
            new RouteEndpoint(new GetMethod(), new TestController())
        );
        $withAnotherAddedRoute = $routerIndexWithAddedRoute->withAddedRoute($route2, $groupName);
        $this->assertSame(
            [$path, $path2],
            $withAnotherAddedRoute->getGroupRouteNames($groupName)
        );
        $this->assertCount(2, $withAnotherAddedRoute->toArray());
    }

    public function testWithAddedAlready(): void
    {
        $repo = 'repository';
        $route = (new Route('test', new RoutePath('/path')))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod(), new TestController())
            );
        $routerIndex = (new RouterIndex())->withAddedRoute($route, $repo);
        $this->expectException(OverflowException::class);
        $routerIndex->withAddedRoute($route, 'other-group');
    }
}
