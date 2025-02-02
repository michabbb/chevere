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

namespace Chevere\Parameter;

use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ArrayStringParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Throwable;

function boolean(
    string $description = '',
    bool $default = false,
): BooleanParameterInterface {
    $parameter = new BooleanParameter($description);

    return $parameter->withDefault($default);
}

function null(
    string $description = '',
): NullParameterInterface {
    return new NullParameter($description);
}

function object(
    string $className,
    string $description = '',
): ObjectParameterInterface {
    $parameter = new ObjectParameter($description);

    return $parameter->withClassName($className);
}

function union(
    ParameterInterface ...$parameter
): UnionParameterInterface {
    $parameters = parameters(...$parameter);

    return new UnionParameter($parameters);
}

function parameters(
    ParameterInterface ...$required,
): ParametersInterface {
    return new Parameters(...$required);
}

/**
 * @param array<int|string, mixed> $arguments
 */
function arguments(
    ParametersInterface $parameters,
    array $arguments
): ArgumentsInterface {
    return new Arguments($parameters, $arguments);
}

function assertBoolean(
    BooleanParameterInterface $parameter,
    bool $argument
): bool {
    return $argument;
}

function assertNull(NullParameterInterface $parameter, mixed $argument): mixed
{
    if ($argument === null) {
        return $argument;
    }

    throw new TypeError(
        message('Argument value provided is not of type null')
    );
}

function assertObject(
    ObjectParameterInterface $parameter,
    object $argument
): object {
    if ($parameter->type()->validate($argument)) {
        return $argument;
    }

    throw new InvalidArgumentException(
        message('Argument value provided is not of type %type%')
            ->withCode('%type%', $parameter->className())
    );
}

function assertUnion(
    UnionParameterInterface $parameter,
    mixed $argument,
): mixed {
    $types = [];
    foreach ($parameter->items() as $parameter) {
        try {
            assertNamedArgument('', $parameter, $argument);

            return $argument;
        } catch (Throwable $e) {
            $types[] = $parameter::class;
        }
    }

    throw new InvalidArgumentException(
        message("Argument provided doesn't match the union type %type%")
            ->withCode('%type%', implode(',', $types))
    );
}

function assertNamedArgument(
    string $name,
    ParameterInterface $parameter,
    mixed $argument
): void {
    $parameters = parameters(
        ...[
            $name => $parameter,
        ]
    );
    $arguments = [
        $name => $argument,
    ];

    try {
        arguments($parameters, $arguments);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            message('Argument [%name%]: %message%')
                ->withTranslate('%name%', $name)
                ->withTranslate('%message%', $e->getMessage())
        );
    }
}

function assertArgument(ParameterInterface $parameter, mixed $argument): mixed
{
    return match (true) {
        $parameter instanceof ArrayParameterInterface,
        $parameter instanceof ArrayStringParameterInterface,
        $parameter instanceof FileParameterInterface
        // @phpstan-ignore-next-line
        => assertArray($parameter, $argument),
        $parameter instanceof BooleanParameterInterface
        // @phpstan-ignore-next-line
        => assertBoolean($parameter, $argument),
        $parameter instanceof FloatParameterInterface
        // @phpstan-ignore-next-line
        => assertFloat($parameter, $argument),
        $parameter instanceof GenericParameterInterface
        // @phpstan-ignore-next-line
        => assertGeneric($parameter, $argument),
        $parameter instanceof IntegerParameterInterface
        // @phpstan-ignore-next-line
        => assertInteger($parameter, $argument),
        $parameter instanceof ObjectParameterInterface
        // @phpstan-ignore-next-line
        => assertObject($parameter, $argument),
        $parameter instanceof StringParameterInterface
        // @phpstan-ignore-next-line
        => assertString($parameter, $argument),
        $parameter instanceof UnionParameterInterface
        => assertUnion($parameter, $argument),
        $parameter instanceof NullParameterInterface
        => assertNull($parameter, $argument),
        default => null,
    };
}
