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

namespace Chevere\Tests\Workflow\_resources\src;

use Chevere\Components\Action\Action;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;

class WorkflowRunnerFunctionTestStep2 extends Action
{
    public function getParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('foo'))
            ->withAddedRequired(new StringParameter('bar'));
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return (new Parameters)
            ->withAddedRequired(new StringParameter('response-2'));
    }

    public function run(array $arguments): ResponseSuccessInterface
    {
        $arguments = $this->getArguments($arguments);

        return $this->getResponseSuccess(
            [
                'response-2' => $arguments->getString('foo') . ' ^ ' . $arguments->getString('bar'),
            ]
        );
    }
}