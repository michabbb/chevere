<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Middleware;

use Chevere\Components\Middleware\Exceptions\MiddlewareContractException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Middleware\MiddlewareContract;
use Chevere\Contracts\Middleware\MiddlewareNameContract;
use InvalidArgumentException;

final class MiddlewareName implements MiddlewareNameContract
{
    /** @var string */
    private $name;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertMiddlewareContract();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->name;
    }

    private function assertMiddlewareContract(): void
    {
        if (!class_exists($this->name)) {
            throw new InvalidArgumentException(
                (new Message("Middleware %middleware% doesn't exists"))
                    ->code('%middleware%', $this->name)
                    ->toString()
            );
        }
        $interfaces = class_implements($this->name);
        if (false === $interfaces || !in_array(MiddlewareContract::class, $interfaces)) {
            throw new MiddlewareContractException(
                (new Message('Middleware %middleware% must implement the %contract% contract'))
                    ->code('%middleware%', $this->name)
                    ->code('%contract%', MiddlewareContract::class)
                    ->toString()
            );
        }
    }
}
