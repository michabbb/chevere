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

namespace Chevere\HttpController\Traits;

trait ResponseHtmlTrait
{
    public static function responseHeaders(): array
    {
        $headers = parent::responseHeaders();
        $headers['Content-Type'] = 'text/html; charset=utf-8';

        return $headers;
    }
}
