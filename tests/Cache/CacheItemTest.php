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

namespace Chevere\Tests\Cache;

use Chevere\Components\Cache\CacheItem;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\VarSupport\VarStorable;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use PHPUnit\Framework\TestCase;
use function Safe\file_put_contents;

final class CacheItemTest extends TestCase
{
    private PathInterface $resourcesPath;

    protected function setUp(): void
    {
        $this->resourcesPath = (new Path(__DIR__))->getChild('_resources');
    }

    public function testNotSerialized(): void
    {
        $path = $this->resourcesPath->getChild('return.php');
        $cacheItem = $this->getCacheItem($path);
        $var = include $path->toString();
        $this->assertSame($var, $cacheItem->raw());
        $this->assertSame($var, $cacheItem->var());
    }

    public function testSerialized(): void
    {
        $path = $this->resourcesPath->getChild('return-serialized.php');
        $this->writeSerialized($path);
        $cacheItem = $this->getCacheItem($path);
        $var = include $path->toString();
        $this->assertSame($var, $cacheItem->raw());
        $this->assertEqualsCanonicalizing(
            unserialize($var),
            $cacheItem->var()
        );
        unlink($path->toString());
    }

    private function getCacheItem(PathInterface $path): CacheItemInterface
    {
        return new CacheItem(
            new FilePhpReturn(
                new FilePhp(
                    new File($path)
                )
            )
        );
    }

    private function writeSerialized(PathInterface $path): void
    {
        if (!$path->exists()) {
            file_put_contents($path->toString(), '');
        }
        $fileReturn = new FilePhpReturn(
            new FilePhp(
                new File($path)
            )
        );
        $fileReturn->put(
            new VarStorable($path)
        );
    }
}
