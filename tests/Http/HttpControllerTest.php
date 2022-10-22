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

namespace Chevere\Tests\Http;

use Chevere\Tests\Http\_resources\TestHttpController;
use Chevere\Throwable\Errors\ArgumentCountError;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HttpControllerTest extends TestCase
{
    public function testAcceptGetParameters(): void
    {
        $controller = new TestHttpController();
        $this->assertSame([], $controller->get());
        $controllerWith = $controller->withGet([
            'foo-foo' => 'abc',
        ]);
        $this->assertNotSame($controller, $controllerWith);
        $this->assertNotEquals($controller, $controllerWith);
        $this->assertSame('abc', $controllerWith->get()['foo-foo']);
        $this->expectException(InvalidArgumentException::class);
        $controller->withGet([
            'foo-foo' => '123',
        ]);
    }

    public function testAcceptPostParameters(): void
    {
        $controller = new TestHttpController();
        $this->assertSame([], $controller->post());
        $controllerWith = $controller->withPost([
            'bar.bar' => '123',
        ]);
        $this->assertNotSame($controller, $controllerWith);
        $this->assertNotEquals($controller, $controllerWith);
        $this->assertSame('123', $controllerWith->post()['bar.bar']);
        $this->expectException(InvalidArgumentException::class);
        $controller->withPost([
            'bar.bar' => 'abc',
        ]);
    }

    public function testAcceptFileParameters(): void
    {
        $controller = new TestHttpController();
        $file = [
            'type' => 'text/plain',
            'tmp_name' => '/tmp/file.yx5kVl',
            'size' => 1313,
            'name' => 'readme.txt',
            'error' => 0,
        ];
        $this->assertSame([], $controller->files());
        $controllerWith = $controller->withFiles([
            'MyFile!' => $file,
        ]);
        $this->assertNotSame($controller, $controllerWith);
        $this->assertNotEquals($controller, $controllerWith);
        $this->assertSame($file, $controllerWith->files()['MyFile!']);
        $this->expectException(ArgumentCountError::class);
        $controller->withFiles([
            'MyFile!' => [],
        ]);
    }
}
