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

namespace Chevere\Tests\Action\_resources\src;

use Chevere\Controller\Controller;
use Exception;

final class ActionRunnerTestControllerRunFail extends Controller
{
    public function run(): array
    {
        throw new Exception('Something went wrong');
    }
}
