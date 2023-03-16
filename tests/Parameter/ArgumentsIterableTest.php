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

use Chevere\Parameter\Arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\integerp;
use function Chevere\Parameter\parameters;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArgumentsIterableTest extends TestCase
{
    public function iterableProvider(): array
    {
        return [
            [
                [
                    'test' => [
                        'one' => 123,
                        'two' => 456,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider iterableProvider
     */
    public function testIterableArguments(array $args): void
    {
        $parameters = parameters(
            test: arrayp(
                one: integerp(),
                two: integerp(),
            )
        );
        $this->expectNotToPerformAssertions();
        $arguments = new Arguments($parameters, $args);
    }

    /**
     * @dataProvider iterableProvider
     */
    public function testIterableArgumentsConflict(array $args): void
    {
        $parameters = parameters(
            test: arrayp(
                one: integerp(maximum: 1),
                two: integerp(),
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $arguments = new Arguments($parameters, $args);
    }
}
