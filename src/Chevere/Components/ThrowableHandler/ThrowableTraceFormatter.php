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

namespace Chevere\Components\ThrowableHandler;

use Chevere\Components\Str\Str;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerFormatterInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceEntryInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableTraceFormatterInterface;

final class ThrowableTraceFormatter implements ThrowableTraceFormatterInterface
{
    private array $array = [];

    private string $string = '';

    public function __construct(
        private array $trace,
        private ThrowableHandlerFormatterInterface $formatter
    ) {
        foreach ($this->trace as $pos => $entry) {
            $this->array[] = strtr(
                $this->formatter->getTraceEntryTemplate(),
                $this->getTrTable($pos, new ThrowableTraceEntry($entry))
            );
        }
        if ($this->array !== []) {
            $this->string = $this->wrapStringHr(
                $this->glueString($this->array)
            );
        }
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function toString(): string
    {
        return $this->string;
    }

    private function getTrTable(
        int $pos,
        ThrowableTraceEntryInterface $entry
    ): array {
        $function = $this->getFunctionWithArguments($entry);
        $trValues = [
            self::TAG_ENTRY_CSS_EVEN_CLASS => ($pos & 1) !== 0
                ? 'entry--even'
                : '',
            self::TAG_ENTRY_POS => $pos,
            self::TAG_ENTRY_FILE => $entry->file(),
            self::TAG_ENTRY_LINE => $entry->line(),
            self::TAG_ENTRY_FILE_LINE => $entry->fileLine(),
            self::TAG_ENTRY_CLASS => $entry->class(),
            self::TAG_ENTRY_TYPE => $entry->type(),
            self::TAG_ENTRY_FUNCTION => $function,
        ];
        $array = $trValues;
        foreach (self::HIGHLIGHT_TAGS as $tag => $key) {
            $val = $trValues[$tag];
            $array[$tag] = $this->formatter->varDumpFormatter()
                ->highlight($key, (string) $val);
        }
        
        return $array;
    }

    private function getFunctionWithArguments(
        ThrowableTraceEntryInterface $entry
    ): string {
        $return = '';
        foreach ($entry->args() as $argument) {
            $return .= gettype($argument) . ' ';
            $return .= match (true) {
                is_scalar($argument) => strval($argument),
                is_array($argument) => 'array(' . count($argument) . ')',
                is_object($argument) => get_class($argument) . '#'
                    . strval(spl_object_id($argument)),
                is_resource($argument) => get_resource_id($argument),
                default => '',
            };
            $return .= ', ';
        }
        $return = (new Str($return))
            ->withReplaceLast(', ', '')
            ->toString();

        return $entry->function() . '(' . trim($return) . ')';
    }

    private function wrapStringHr(string $text): string
    {
        return $this->formatter->getHr() . "\n" . $text . "\n" .
            $this->formatter->getHr();
    }

    private function glueString(array $array)
    {
        return implode("\n" . $this->formatter->getHr() . "\n", $array);
    }
}
