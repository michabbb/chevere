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

namespace Chevere\Controller;

use Chevere\Action\Action;
use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Message\Message;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\StringParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;

abstract class Controller extends Action implements ControllerInterface
{
    protected StringParameterInterface $parameter;

    public function __construct()
    {
        $this->parameter = $this->parameter();
        $this->setUp();
        $this->assertParametersType();
    }

    public function parameter(): StringParameterInterface
    {
        return new StringParameter();
    }

    protected function assertParametersType(): void
    {
        $invalid = [];
        foreach ($this->parameters()->getIterator() as $name => $parameter) {
            if (!($parameter instanceof StringParameterInterface)) {
                $invalid[] = $name;
            }
        }
        if ($invalid !== []) {
            throw new InvalidArgumentException(
                (new Message('Parameter %parameters% must be of type %type% for controller %className%.'))
                    ->code('%parameters%', implode(', ', $invalid))
                    ->strong('%type%', $this->parameter->type()->typeHinting())
                    ->strong('%className%', static::class)
            );
        }
    }
}
