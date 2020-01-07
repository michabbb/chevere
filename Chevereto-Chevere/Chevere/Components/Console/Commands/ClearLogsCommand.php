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

namespace Chevere\Components\Console\Commands;

use Chevere\Components\Console\Command;
use Chevere\Components\Dir\Dir;
use Chevere\Components\Path\PathApp;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuilderContract;

/**
 * The ClearLogsCommand removes app stored logs.
 */
final class ClearLogsCommand extends Command
{
    const NAME = 'clear-logs';
    const DESCRIPTION = 'Clear app stored logs';
    const HELP = 'This command clears logs stored by the app';

    public function callback(BuilderContract $builder): int
    {
        $delete = (new Dir(new PathApp(AppContract::PATH_LOGS)))->removeContents();
        $count = count($delete);
        $this->console()->style()->success(
            $count > 0 ? sprintf('App logs cleared (%s files)', $count) : 'No app logs to remove'
        );
        if ($count) {
            $this->console()->style()->listing($delete);
        }

        return 0;
    }
}
