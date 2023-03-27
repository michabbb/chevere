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

namespace Chevere\Tests\Parameter;

use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\assertInteger;
use function Chevere\Parameter\integerp;
use PHPUnit\Framework\TestCase;

final class FunctionsIntegerTest extends TestCase
{
    public function testIntegerp(): void
    {
        $parameter = integerp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(0, $parameter->default());
        $this->assertSame(PHP_INT_MIN, $parameter->minimum());
        $this->assertSame(PHP_INT_MAX, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
    }

    public function testIntegerpOptions(): void
    {
        $description = 'test';
        $default = 5;
        $parameter = integerp(
            description: $description,
            default: $default,
            minimum: -100,
            maximum: 100,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame(-100, $parameter->minimum());
        $this->assertSame(100, $parameter->maximum());
        $parameter = integerp(accept: [0, 1]);
        $this->assertSame([0, 1], $parameter->accept());
    }

    public function testAssertInteger(): void
    {
        $parameter = integerp();
        assertInteger($parameter, 0);
        assertArgument($parameter, 0);
        $this->expectNotToPerformAssertions();
    }
}
