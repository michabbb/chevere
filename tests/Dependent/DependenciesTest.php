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

namespace Chevere\Tests\Dependent;

use Chevere\Components\Dependent\Dependencies;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use PHPUnit\Framework\TestCase;

final class DependenciesTest extends TestCase
{
    public function testConstruct(): void
    {
        $dependencies = new Dependencies();
        $key = 'key';
        $this->assertCount(0, $dependencies);
        $this->assertSame(null, $dependencies->getGenerator()->current());
        $this->assertSame([], $dependencies->keys());
        $this->assertFalse($dependencies->hasKey($key));
        $this->expectException(OutOfBoundsException::class);
        $dependencies->key($key);
    }

    public function testWithPut(): void
    {
        $dependencies = (new Dependencies())
            ->withPut(
                path: PathInterface::class,
                dir: DirInterface::class,
            );
        $this->assertCount(2, $dependencies);
        $this->assertSame(PathInterface::class, $dependencies->getGenerator()->current());
        $this->assertSame(['path', 'dir'], $dependencies->keys());
        $this->assertSame(PathInterface::class, $dependencies->key('path'));
        $this->assertSame(DirInterface::class, $dependencies->key('dir'));
    }

    public function testWithMerge(): void
    {
        $dependencies = (new Dependencies())
            ->withPut(path: PathInterface::class);
        $merge = (new Dependencies())
            ->withPut(dir: DirInterface::class);
        $merged = $dependencies->withMerge($merge);
        $this->assertSame(['path', 'dir'], $merged->keys());
        $this->assertSame(PathInterface::class, $merged->key('path'));
        $this->assertSame(DirInterface::class, $merged->key('dir'));
    }

    public function testWithMergeConflict(): void
    {
        $dependencies = (new Dependencies())
            ->withPut(path: PathInterface::class, );
        $merge = (new Dependencies())
            ->withPut(path: __CLASS__);
        $this->expectException(OverflowException::class);
        $dependencies->withMerge($merge);
    }
}
