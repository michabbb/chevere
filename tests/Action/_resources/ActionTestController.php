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

namespace Chevere\Tests\Action\_resources;

use Chevere\Attribute\StringAttribute;
use Chevere\Controller\Controller;
use function Chevere\Parameter\arrayp;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use function Chevere\Parameter\string;

final class ActionTestController extends Controller
{
    public static function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayp(user: string());
    }

    public function run(
        #[StringAttribute(description: 'The username.', regex: '/^[a-zA-Z]+$/')]
        string $name
    ): array {
        return [
            'user' => 'PeoplesHernandez',
        ];
    }
}
