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

use Chevere\Parameter\GenericParameter;
use function Chevere\Parameter\integerp;
use Chevere\Parameter\Interfaces\GenericsInterface;
use function Chevere\Parameter\stringp;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GenericParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $value = stringp();
        $key = stringp();
        $description = 'test';
        $parameter = new GenericParameter(
            $value,
            $key,
            $description
        );
        $this->assertSame($value, $parameter->value());
        $this->assertSame($key, $parameter->key());
        $this->assertSame([], $parameter->default());
        $this->assertSame($description, $parameter->description());
        $parameters = $parameter->parameters();
        $this->assertSame($parameters, $parameter->parameters());
        $this->assertInstanceOf(GenericsInterface::class, $parameters);
        $genericParameter = $parameters->get(GenericsInterface::GENERIC_NAME);
        $this->assertEquals($genericParameter, $parameter);
    }

    public function testAssertCompatible(): void
    {
        $this->expectNotToPerformAssertions();
        $key = stringp();
        $value = integerp(description: 'compatible');
        $keyAlt = stringp(description: 'compatible');
        $valueAlt = integerp();
        $parameter = new GenericParameter($value, $key);
        $compatible = new GenericParameter($valueAlt, $keyAlt, 'compatible');
        $parameter->assertCompatible($compatible);
    }

    public function testAssertCompatibleConflictValue(): void
    {
        $key = stringp();
        $value = integerp();
        $valueAlt = integerp(minimum: 1);
        $parameter = new GenericParameter($value, $key);
        $notCompatible = new GenericParameter($valueAlt, $key);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleConflictKey(): void
    {
        $key = stringp();
        $value = integerp();
        $keyAlt = stringp('/^[a-z]+&/');
        $parameter = new GenericParameter($value, $key);
        $notCompatible = new GenericParameter($value, $keyAlt);
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }
}
