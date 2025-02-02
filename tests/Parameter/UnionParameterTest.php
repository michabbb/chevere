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

use function Chevere\Parameter\integer;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;
use Chevere\Parameter\UnionParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;

final class UnionParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new UnionParameter(
            parameters()
        );
        $this->assertSame(
            TypeInterface::UNION,
            $parameter->type()->primitive()
        );
        $this->assertCount(0, $parameter->items());
        $this->assertSame(TypeInterface::UNION, $parameter->typeSchema());
    }

    public function testWithAdded(): void
    {
        $parameter = new UnionParameter(
            parameters()
        );
        $one = string();
        $two = integer();
        $parameterWith = $parameter->withAdded(
            $one,
            $two
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->items());
        $this->assertSame($one, $parameterWith->items()->get('0'));
        $this->assertSame($two, $parameterWith->items()->get('1'));
    }

    public function testAssertCompatible(): void
    {
        $parameters = parameters(
            string(),
        );
        $parametersAlt = parameters(
            string(description: 'one'),
        );
        $parameter = new UnionParameter($parameters);
        $compatible = new UnionParameter($parametersAlt);
        $this->expectNotToPerformAssertions();
        $parameter->assertCompatible($compatible);
    }

    public function testAssertNotCompatible(): void
    {
        $parameters = parameters(
            string(),
        );
        $parametersAlt = parameters(
            integer(),
        );
        $parameter = new UnionParameter($parameters);
        $compatible = new UnionParameter($parametersAlt);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($compatible);
    }
}
