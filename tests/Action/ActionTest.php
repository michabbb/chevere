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

namespace Chevere\Tests\Action;

use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Tests\Action\_resources\ActionTestAction;
use Chevere\Tests\Action\_resources\ActionTestController;
use Chevere\Tests\Action\_resources\ActionTestGenericResponse;
use Chevere\Tests\Action\_resources\ActionTestGenericResponseError;
use Chevere\Tests\Action\_resources\ActionTestInvalidRunReturn;
use Chevere\Tests\Action\_resources\ActionTestInvalidScope;
use Chevere\Tests\Action\_resources\ActionTestMissingRun;
use Chevere\Tests\Action\_resources\ActionTestNoReturnType;
use Chevere\Tests\Action\_resources\ActionTestNoStrict;
use Chevere\Tests\Action\_resources\ActionTestParameterAttributes;
use Chevere\Tests\Action\_resources\ActionTestRunParameterMissingType;
use Chevere\Tests\Action\_resources\ActionTestRunParameters;
use Chevere\Tests\Action\_resources\ActionTestRunReturnExtraArguments;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\ErrorException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;

final class ActionTest extends TestCase
{
    public function testConstruct(): void
    {
        $action = new ActionTestAction();
        $this->assertSame('', $action->description());
        $this->assertCount(0, $action->getParameters());
        $this->assertCount(0, $action->acceptResponse()->items());
        $action->run();
    }

    public function testMissingRunMethod(): void
    {
        $this->expectException(LogicException::class);
        (new ActionTestMissingRun())->assert();
    }

    public function testRunParams(): void
    {
        $defaults = [
            'intDefault' => 1,
            'stringDefault' => 'default',
            'boolDefault' => false,
            'floatDefault' => 0.0,
            'arrayDefault' => [],
            'objectDefault' => null,
        ];
        $types = [
            'int' => IntegerParameterInterface::class,
            'string' => StringParameterInterface::class,
            'bool' => BooleanParameterInterface::class,
            'float' => FloatParameterInterface::class,
            'array' => ArrayParameterInterface::class,
            'object' => ObjectParameterInterface::class,
            'file' => ObjectParameterInterface::class,
        ];
        $optional = array_keys($defaults);
        $required = array_keys($types);
        $action = new ActionTestRunParameters();
        $this->assertSame($optional, ActionTestRunParameters::getParameters()->optionalKeys());
        $this->assertSame($required, ActionTestRunParameters::getParameters()->requiredKeys());
        foreach ($defaults as $name => $value) {
            $parameter = ActionTestRunParameters::getParameters()->get(strval($name));
            $this->assertEquals($value, $parameter->default());
        }
        foreach ($types as $parameter => $class) {
            $parameter = strval($parameter);
            $this->assertInstanceOf($class, ActionTestRunParameters::getParameters()->get($parameter));
        }
        $this->assertSame(
            FileInterface::class,
            $action->getParameters()->get('file')->type()->typeHinting()
        );
    }

    public function testParamsAttributes(): void
    {
        $action = new ActionTestParameterAttributes();
        $this->assertSame('An int', $action->getParameters()->get('int')->description());
        /** @var StringParameterInterface $parameter */
        $parameter = $action->getParameters()->get('name');
        $this->assertSame('The name', $parameter->description());
        $this->assertSame('/^[a-z]$/', $parameter->regex()->__toString());
    }

    public function testRunWithArguments(): void
    {
        $parameter = 'name';
        $value = 'PeoplesHernandez';
        $action = new ActionTestController();
        $arguments = [
            $parameter => $value,
        ];
        $array = $action->run(...$arguments);
        $expected = [
            'user' => $value,
        ];
        $this->assertSame($expected, $array);
        $response = $action->getResponse(...$arguments);
        $this->assertSame(0, $response->code());
        $this->assertSame($expected, $response->data());
    }

    public function testInvalidRunReturn(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(ActionTestInvalidRunReturn::class . '::run');
        ( new ActionTestInvalidRunReturn())->assert();
    }

    public function testInvalidRunParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('$mixed');
        (new ActionTestRunParameterMissingType())->assert();
    }

    public function testRunReturnExtraArguments(): void
    {
        $action = new ActionTestRunReturnExtraArguments();
        $this->expectException(ArgumentCountError::class);
        $action->getResponse();
    }

    public function testActionNoStrict(): void
    {
        $action = new ActionTestNoStrict();
        $response = $action->getResponse();
        $run = $action->run();
        $this->assertSame($run, $response->data());
    }

    public function testActionGenericResponse(): void
    {
        $action = new ActionTestGenericResponse();
        $this->expectNotToPerformAssertions();
        $action->getResponse();
    }

    public function testActionGenericResponseError(): void
    {
        $action = new ActionTestGenericResponseError();
        $this->expectException(InvalidArgumentException::class);
        $action->getResponse();
    }

        public function testActionInvalidScope(): void
        {
            $this->expectException(ErrorException::class);
            (new ActionTestInvalidScope())->assert();
        }

        public function testActionNoReturnType(): void
        {
            $this->expectException(ErrorException::class);
            (new ActionTestNoReturnType())->assert();
        }
}
