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

namespace Chevere\Components\Filesystem\Interfaces;

use Chevere\Components\Filesystem\Exceptions\FileNotPhpException;

interface FilePhpInterface
{
    /**
     * @throws FileNotPhpException if $file doesn't represent a PHP filepath.
     */
    public function __construct(FileInterface $file);

    /**
     * Provides access to the FileInterface instance.
     */
    public function file(): FileInterface;

    /**
     * Applies OPCache.
     *
     * @throws RuntimeException If unable to cache file.
     */
    public function cache(): void;
}
