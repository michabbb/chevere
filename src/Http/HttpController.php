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

namespace Chevere\Http;

use Chevere\Controller\Controller;
use Chevere\Http\Interfaces\HttpControllerInterface;
use Chevere\Parameter\Arguments;
use function Chevere\Parameter\integerParameter;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringParameter;

abstract class HttpController extends Controller implements HttpControllerInterface
{
    /**
     * @var array<int|string, string>
     */
    protected $get = [];

    /**
     * @var array<int|string, string>
     */
    protected $post = [];

    /**
     * @var array<int|string, array<string, int|string>>
     */
    protected $files = [];

    public function acceptGet(): ParametersInterface
    {
        return parameters();
    }

    public function acceptPost(): ParametersInterface
    {
        return parameters();
    }

    public function acceptFiles(): ParametersInterface
    {
        return parameters();
    }

    public function withGet(array $get): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptGet(),
            ...$get
        );
        /** @var array<int|string, string> */
        $array = $arguments->toArray();
        $new->get = $array;

        return $new;
    }

    public function withPost(array $post): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptPost(),
            ...$post
        );
        /** @var array<int|string, string> */
        $array = $arguments->toArray();
        $new->post = $array;

        return $new;
    }

    public function withFiles(array $files): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptFiles(),
            ...$files
        );
        /** @var array<int|string, array<string, int|string>> */
        $array = $arguments->toArray();
        $required = parameters(
            type: stringParameter(),
            tmp_name: stringParameter(),
            size: integerParameter(),
            name: stringParameter(),
            error: integerParameter()
        );
        foreach ($array as $file) {
            $arguments = new Arguments($required, ...$file);
        }
        $new->files = $array;

        return $new;
    }

    public function get(): array
    {
        return $this->get;
    }

    public function post(): array
    {
        return $this->post;
    }

    public function files(): array
    {
        return $this->files;
    }
}
