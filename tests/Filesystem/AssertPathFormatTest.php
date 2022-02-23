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

namespace Chevere\Tests\Filesystem;

use Chevere\Filesystem\AssertPathFormat;
use Chevere\Filesystem\Exceptions\PathDotSlashException;
use Chevere\Filesystem\Exceptions\PathDoubleDotsDashException;
use Chevere\Filesystem\Exceptions\PathExtraSlashesException;
use Chevere\Filesystem\Exceptions\PathNotAbsoluteException;
use PHPUnit\Framework\TestCase;

final class AssertPathFormatTest extends TestCase
{
    public function testNoAbsolutePath(): void
    {
        $this->expectException(PathNotAbsoluteException::class);
        (new AssertPathFormat('path'));
    }

    public function testExtraSlashesPath(): void
    {
        $this->expectException(PathExtraSlashesException::class);
        new AssertPathFormat('/some//dir');
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new AssertPathFormat('/some/./dir');
    }

    public function testDotsSlashPath(): void
    {
        $this->expectException(PathDoubleDotsDashException::class);
        new AssertPathFormat('/some/../dir');
    }

    public function testConstruct(): void
    {
        $path = '/path';
        $assert = new AssertPathFormat($path);
        $this->assertSame($path, $assert->path());
    }

    public function testConstructWindows(): void
    {
        $paths = [
            '\Program Files\Custom Utilities' => '/Program Files/Custom Utilities',
            '\Program Files\Custom Utilities\\' => '/Program Files/Custom Utilities/',
            'C:\Documents\Newsletters' => 'C:/Documents/Newsletters',
            'C:\Documents\Newsletters\\' => 'C:/Documents/Newsletters/',
        ];
        foreach ($paths as $path => $expected) {
            $assert = new AssertPathFormat($path);
            $this->assertSame($expected, $assert->path());
        }
    }
}
