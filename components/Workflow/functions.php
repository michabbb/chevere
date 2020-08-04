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

namespace Chevere\Components\Workflow;

use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\WorkflowRunInterface;

function workflowRunner(WorkflowRunInterface $workflowRun): WorkflowRunInterface
{
    $type = new Type(ResponseInterface::class);
    foreach ($workflowRun->workflow()->getGenerator() as $step => $task) {
        if ($workflowRun->has($step)) {
            continue; // @codeCoverageIgnore
        }
        $action = $task->action();
        if (!class_exists($action)) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message("Class %action% doesn't exist"))
                    ->code('%action%', $action)
            );
            // @codeCoverageIgnoreEnd
        }
        $magicArguments = $task->arguments();
        $arguments = [];
        foreach ($magicArguments as $magicArgument) {
            if (!$workflowRun->workflow()->hasVar($magicArgument)) {
                // @codeCoverageIgnoreStart
                $arguments[] = $magicArgument;
                continue;
                // @codeCoverageIgnoreEnd
            }
            $reference = $workflowRun->workflow()->getVar($magicArgument);
            if (isset($reference[1])) {
                $arguments[] = $workflowRun->get($reference[0])->data()[$reference[1]];
            } else {
                $arguments[] = $workflowRun->arguments()->get($reference[0]);
            }
        }
        $response = (new $action(...$arguments))->execute();
        if (!$type->validate($response)) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message('Return value for %callable% must of type %object% and must implement %interface%, type %provided% provided'))
                    ->code('%callable%', $action)
                    ->code('%object%', 'object')
                    ->code('%interface%', $type->typeHinting())
                    ->code('%provided%', gettype($response))
            );
            // @codeCoverageIgnoreEnd
        }
        try {
            $workflowRun = $workflowRun->withAdded($step, $response);
        }
        // @codeCoverageIgnoreStart
        catch (ArgumentCountException $e) {
            throw new LogicException(
                (new Message('Unexpected response from callable %callable% at step %step%'))
                    ->code('%callable%', $action)
                    ->code('%step%', $step),
                0,
                $e
            );
        }
        // @codeCoverageIgnoreEnd
    }

    return $workflowRun;
}
