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

namespace Chevere\Components\Response;

use Chevere\Components\Response\Traits\ResponseTrait;
use Chevere\Interfaces\Response\ResponseProvisionalInterface;

final class ResponseProvisional implements ResponseProvisionalInterface
{
    use ResponseTrait;

    public function withData(array $data): ResponseProvisionalInterface
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }
}
