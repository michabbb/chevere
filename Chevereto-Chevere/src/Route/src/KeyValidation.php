<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Route\src;

use InvalidArgumentException;
use Chevere\Message;
use Chevere\Utility\Str;

final class KeyValidation
{
    /** @var string */
    public $key;

    /** @var bool */
    public $hasHandlebars;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->hasHandlebars = $this->hasHandlebars($this->key);
        $this->handleValidateFormat();
        $this->handleWildcards();
    }

    private function handleValidateFormat()
    {
        if (!$this->validateFormat($this->key)) {
            throw new InvalidArgumentException(
                (new Message("String %s must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."))
                    ->code('%s', $this->key)
                    ->toString()
            );
        }
    }

    private function handleWildcards()
    {
        if ($this->hasHandlebars && !$this->validateWildcard($this->key)) {
            throw new InvalidArgumentException(
                (new Message('Wildcards in the form of %s are reserved.'))
                    ->code('%s', '/{n}')
                    ->toString()
            );
        }
    }

    private function validateFormat(string $key): bool
    {
        if ('/' == $key) {
            return true;
        }

        return strlen($key) > 0 && Str::startsWith('/', $key)
            && $this->validateFormatSlashes($key);
    }

    private function validateFormatSlashes(string $key): bool
    {
        return !Str::endsWith('/', $key)
            && !Str::contains('//', $key)
            && !Str::contains(' ', $key)
            && !Str::contains('\\', $key);
    }

    private function validateWildcard(string $key): bool
    {
        return preg_match_all('/{([0-9]+)}/', $key) === 0;
    }

    private function hasHandlebars(string $key): bool
    {
        return Str::contains('{', $key) || Str::contains('}', $key);
    }
}
