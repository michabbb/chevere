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

namespace Chevere\Tests\Routing;

use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Routing\RouteEndpointsIterator;
use Chevere\Exceptions\Routing\ExpectingControllerException;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\getDirFromString;

final class RouteEndpointsIteratorTest extends TestCase
{
    public function testObjects(): void
    {
        $dir = getDirFromString(__DIR__ . '/_resources/routes/articles/{id}/');
        $endpointIterator = new RouteEndpointsIterator($dir);
        $routeEndpoints = $endpointIterator->routeEndpoints();
        $this->assertCount(1, $routeEndpoints);
        /** @var string $key */
        foreach ($routeEndpoints->keys() as $key) {
            $this->assertInstanceOf(
                RouteEndpointInterface::class,
                $routeEndpoints->get($key)
            );
        }
    }

    public function testWrongObjects(): void
    {
        $dir = getDirFromString(__DIR__ . '/_resources/wrong-routes/articles/');
        $this->expectException(ExpectingControllerException::class);
        new RouteEndpointsIterator($dir);
    }
}
