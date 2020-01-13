<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Processors;

use Chevere\Components\VarDump\Interfaces\VarDumpInterface;

final class NullProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return VarDumpInterface::TYPE_NULL;
    }

    protected function process(): void
    {
        $this->val = '';
        $this->info = '';
    }
}
