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

use LogicException;
use Chevere\Message;
use Chevere\Path;
use Chevere\Utility\Str;
use Chevere\Utility\Arr;
use Chevere\Route\Route;

/**
 * Interacts with routes that use wildcards.
 */
final class Wildcards
{
    /** @var string Key set representation */
    public $set;

    /** @var array */
    public $matches;

    /** @var array */
    public $wildcards;

    /** @var array An array containing all the key sets for the route (optionals combo) */
    public $powerSet;

    /** @var string */
    private $uri;

    /** @var array An array containing the optional wildcards */
    private $optionals;

    /** @var array An array indexing the optional wildcards */
    private $optionalsIndex;

    /** @var array An array indexing the mandatory wildcards */
    private $mandatoryIndex;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
        // $matches[0] => [{wildcard}, {wildcard?},...]
        // $matches[1] => [wildcard, wildcard?,...]
        if (!preg_match_all(Route::REGEX_WILDCARD_SEARCH, $this->uri, $matches)) {
            return;
        }
        $this->matches = $matches;
        $this->set = $uri;
        $this->optionals = [];
        $this->optionalsIndex = [];
        $this->handleMatches();
        $this->handleOptionals();
    }

    private function handleMatches()
    {
        foreach ($this->matches[0] as $k => $v) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->set)) {
                $this->set = Str::replaceFirst($v, "{{$k}}", $this->set);
            }
            $wildcard = $this->matches[1][$k];
            if (Str::endsWith('?', $wildcard)) {
                $wildcardTrim = Str::replaceLast('?', null, $wildcard);
                $this->optionals[] = $k;
                $this->optionalsIndex[$k] = $wildcardTrim;
            } else {
                $wildcardTrim = $wildcard;
            }
            if (in_array($wildcardTrim, $this->wildcards ?? [])) {
                throw new LogicException(
                    (new Message('Must declare one unique wildcard per capturing group, duplicated %s detected in route %r.'))
                        ->code('%s', $this->matches[0][$k])
                        ->code('%r', $this->uri)
                        ->toString()
                );
            }
            $this->wildcards[] = $wildcardTrim;
        }
    }

    private function handleOptionals()
    {
        if (!empty($this->optionals)) {
            $mandatoryDiff = array_diff($this->wildcards ?? [], $this->optionalsIndex);
            $this->mandatoryIndex = $this->getIndex($mandatoryDiff);
            // Generate the optionals power set, keeping its index keys in case of duplicated optionals
            $powerSet = Arr::powerSet($this->optionals, true);
            // Build the route set, it will contain all the possible route combinations
            $this->powerSet = $this->processPowerSet($powerSet);
        }
    }

    private function getIndex(array $diff): array
    {
        $index = [];
        foreach ($diff as $k => $v) {
            $index[$k] = null;
        }

        return $index;
    }

    private function processPowerSet(array $powerSet): array
    {
        $routeSet = [];
        foreach ($powerSet as $set) {
            $auxSet = $this->set;
            $auxWildcards = $this->mandatoryIndex;
            foreach ($set as $replaceKey => $replaceValue) {
                $search = $this->optionals[$replaceKey];
                if ($replaceValue !== null) {
                    $replaceValue = "{{$replaceValue}}";
                    $auxWildcards[$search] = null;
                }
                $auxSet = str_replace("{{$search}}", $replaceValue ?? '', $auxSet);
                $auxSet = Path::normalize($auxSet);
            }
            ksort($auxWildcards);
            /*
             * Maps expected regex indexed matches [0,1,2,] to registered wildcard index [index=>n].
             * For example, a set /test-{0}--{2} will capture 0->0 and 1->2. Storing the expected index allows\
             * to easily map matches => wildcards => values.
             */
            $routeSet[$auxSet] = array_keys($auxWildcards);
        }

        return $routeSet;
    }
}
