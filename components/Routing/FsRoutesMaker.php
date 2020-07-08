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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Str\Str;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Routing\ExpectingRouteNameException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Interfaces\Routing\FsRoutesInterface;
use Chevere\Interfaces\Routing\FsRoutesMakerInterface;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use function Chevere\Components\Filesystem\getDirFromString;

final class FsRoutesMaker implements FsRoutesMakerInterface
{
    private FsRoutesInterface $fsRoutes;

    public function __construct(DirInterface $dir)
    {
        $dirIterator = $this->getRecursiveDirectoryIterator($dir);
        $filterIterator = $this->getRecursiveFilterIterator($dirIterator);
        $iteratorIterator = new RecursiveIteratorIterator($filterIterator);
        $this->fsRoutes = new FsRoutes;
        $iteratorIterator->rewind();
        while ($iteratorIterator->valid()) {
            $pathName = $iteratorIterator->current()->getPathName();
            $routeName = $this->getVar($pathName);
            $current = dirname($pathName) . '/';
            $endpointsIterator = new RouteEndpointsIterator(
                new Dir(new Path($current))
            );
            $generator = $endpointsIterator->routeEndpoints()->getGenerator();
            /** @var RouteEndpointInterface $routeEndpoint */
            $routeEndpoint = $generator->current();
            $path = (new Str($current))
                ->withReplaceFirst(
                    rtrim($dir->path()->absolute(), '/'),
                    ''
                );
            foreach ($routeEndpoint->parameters() as $key => $param) {
                $regex = (new Regex($param['regex']))->toNoDelimitersNoAnchors();
                $path = $path->withReplaceAll("{$key}", "$key:$regex");
            }
            $path = $path->toString();
            $route = new Route(new RouteName('name'), new RoutePath($path));
            try {
                foreach ($generator as $key => $routeEndpoint) {
                    $route = $route->withAddedEndpoint($routeEndpoint);
                }
            }
            // @codeCoverageIgnoreStart
            catch (Exception $e) {
                throw new LogicException(
                    $e->message(),
                    $e->getCode(),
                    $e
                );
            }
            // @codeCoverageIgnoreEnd
            $this->fsRoutes = $this->fsRoutes->withDecorated(
                new FsRoute(
                    getDirFromString($current),
                    new RoutePath($path),
                    new RouteDecorator($routeName)
                )
            );
            $iteratorIterator->next();
        }
    }

    public function fsRoutes(): FsRoutesInterface
    {
        return $this->fsRoutes;
    }

    private function getVar(string $path): RouteNameInterface
    {
        try {
            return (new FilePhpReturn(new FilePhp(new File(new Path($path)))))
                ->withStrict(false)
                ->varType(new Type(RouteNameInterface::class));
        } catch (FileReturnInvalidTypeException $e) {
            throw new ExpectingRouteNameException($e->message());
        }
    }

    private function getRecursiveDirectoryIterator(DirInterface $dir): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
    }

    private function getRecursiveFilterIterator(RecursiveDirectoryIterator $recursiveDirectoryIterator): RecursiveFilterIterator
    {
        return new class($recursiveDirectoryIterator) extends RecursiveFilterIterator
        {
            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true;
                }

                return $this->current()->getFilename() === FsRoutesMaker::ROUTE_NAME_BASENAME;
            }
        };
    }
}
