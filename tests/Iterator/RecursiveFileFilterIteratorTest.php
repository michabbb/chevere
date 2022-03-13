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

namespace Chevere\Tests\Iterator;

use Chevere\Iterator\RecursiveFileFilterIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use SplFileInfo;

final class RecursiveFileFilterIteratorTest extends TestCase
{
    public function testConstruct(): void
    {
        $dirItr = new RecursiveDirectoryIterator(__DIR__);
        $iterator = new RecursiveFileFilterIterator($dirItr, 'Test.php');
        /** @var SplFileInfo $wea */
        foreach ($iterator as $wea) {
            if ($wea->isDir()) {
                continue;
            }
            $this->assertStringEndsWith('Test.php', $wea->getFilename());
        }
        $iterator->getChildren();
    }
}
