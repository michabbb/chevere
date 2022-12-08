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

use Chevere\Parameter\IntegerParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use OverflowException;
use PHPUnit\Framework\TestCase;

final class IntegerParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new IntegerParameter('name');
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->minimum());
        $this->assertSame(null, $parameter->maximum());
        $this->assertSame(null, $parameter->value());
        $default = 1234;
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'integer',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
    }

    public function testWithMinimum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWithMinimum = $parameter->withMinimum(1);
        $this->assertNotSame($parameter, $parameterWithMinimum);
        $this->assertSame(1, $parameterWithMinimum->minimum());
        $parameterWithValue = $parameter->withValue(1);
        $this->expectException(OverflowException::class);
        $parameterWithValue->withMinimum(0);
    }

    public function testWithMaximum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWithMaximum = $parameter->withMaximum(1);
        $this->assertNotSame($parameter, $parameterWithMaximum);
        $this->assertSame(1, $parameterWithMaximum->maximum());
        $parameterWithValue = $parameter->withValue(1);
        $this->expectException(OverflowException::class);
        $parameterWithValue->withMaximum(0);
    }

    public function testWithMinimumMaximum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWith = $parameter
            ->withMinimum(1)
            ->withMaximum(2);
        $this->expectException(InvalidArgumentException::class);
        $parameterWith->withMinimum(2);
    }

    public function testWithMaximumMinimum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWith = $parameter
            ->withMinimum(1)
            ->withMaximum(2);
        $this->expectException(InvalidArgumentException::class);
        $parameterWith->withMaximum(1);
    }

    public function testNoMinimumOverflow(): void
    {
        $this->expectException(OverflowException::class);
        (new IntegerParameter())
            ->withMinimum(0)
            ->withValue(1);
    }

    public function testNoMaxOverflow(): void
    {
        $this->expectException(OverflowException::class);
        (new IntegerParameter())
            ->withMaximum(0)
            ->withValue(1);
    }

    public function testWithValue(): void
    {
        $parameter = new IntegerParameter();
        $parameterWithValue = $parameter->withValue(1);
        $this->assertNotSame($parameter, $parameterWithValue);
    }
}
