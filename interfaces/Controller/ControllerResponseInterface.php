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

namespace Chevere\Interfaces\Controller;

/**
 * Describes the component in charge of handling the controller response.
 */
interface ControllerResponseInterface
{
    public function __construct(array $data);

    /**
     * Provides access to controller response data.
     */
    public function data(): array;
}
