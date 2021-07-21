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

namespace Chevere\Components\Cache;

use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Filesystem\FilePhpReturnInterface;

final class CacheItem implements CacheItemInterface
{
    public function __construct(
        private FilePhpReturnInterface $phpFileReturn
    ) {
    }

    public function raw()
    {
        try {
            return $this->phpFileReturn->raw();
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new RuntimeException(
                $e->message(),
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function var()
    {
        try {
            return $this->phpFileReturn->var();
        }
        // @codeCoverageIgnoreStart
        catch (Exception $e) {
            throw new RuntimeException(
                $e->message(),
            );
        }
        // @codeCoverageIgnoreEnd
    }
}
