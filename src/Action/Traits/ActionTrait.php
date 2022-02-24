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

namespace Chevere\Action\Traits;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Container\Container;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Psr\Container\ContainerInterface;

trait ActionTrait
{
    protected ParametersInterface $parameters;

    protected ParametersInterface $responseParameters;

    protected ParametersInterface $containerParameters;

    protected ContainerInterface $container;
    
    public function setUpBefore(): void
    {
    }

    public function setUpAfter(): void
    {
    }

    public function getContainerParameters(): ParametersInterface
    {
        return new Parameters();
    }

    public function getResponseParameters(): ParametersInterface
    {
        return new Parameters();
    }

    protected function assertRunParameters(): void
    {
    }

    final public function containerParameters(): ParametersInterface
    {
        return $this->containerParameters ??= $this->getContainerParameters();
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters ??= $this->getParameters();
    }

    final public function responseParameters(): ParametersInterface
    {
        return $this->responseParameters ??= $this->getResponseParameters();
    }

    final public function getArguments(mixed ...$namedArguments): ArgumentsInterface
    {
        return new Arguments($this->parameters(), ...$namedArguments);
    }

    final public function withContainer(ContainerInterface $container): ActionInterface
    {
        $new = clone $this;
        $new->container = $container;
        $new->assertContainer();

        return $new;
    }

    final public function container(): ContainerInterface
    {
        return $this->container ??= new Container();
    }
}
