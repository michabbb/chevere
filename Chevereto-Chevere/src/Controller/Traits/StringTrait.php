<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Controller\Traits;

trait StringTrait
{
    /** @var string */
    private $document;

    public function setDocument(string $content): void
    {
        $this->document = $content;
    }

    public function getContent(): string
    {
        return $this->document;
    }
}
