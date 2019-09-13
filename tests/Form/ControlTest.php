<?php

namespace Orchestra\Html\Tests\Form;

use Illuminate\Support\Fluent;
use Mockery as m;
use Orchestra\Html\Form\Control;
use PHPUnit\Framework\TestCase;

class ControlTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Html\Form\Control configuration methods.
     *
     * @test
     */
    public function testTemplateMethods()
    {
        $template = ['foo' => 'foobar'];

        $app = m::mock('\Illuminate\Contracts\Container\Container');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Control($app, $request);

        $stub->setTemplates($template);

        $this->assertEquals($template, $stub->getTemplates());
    }

    /**
     * Test Orchestra\Html\Form\Control::buildFluentData() method.
     *
     * @test
     */
    public function testBuildFluentDataMethod()
    {
        $app = m::mock('\Illuminate\Contracts\Container\Container');
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('old')->once()->with('foobar')->andReturn(null);

        $row = new Fluent([
            'foobar' => function () {
                return 'Mr Derp';
            },
        ]);

        $control = new Fluent([
            'name' => 'foobar',
        ]);

        $stub = new Control($app, $request);
        $response = $stub->buildFluentData('text', $row, $control);

        $this->assertSame('foobar', $response->name);
        $this->assertSame('input', $response->method);
        $this->assertSame('text', $response->type);
        $this->assertSame([], $response->options);
        $this->assertFalse($response->checked);
        $this->assertSame([], $response->attributes);
    }

    /**
     * Test Orchestra\Html\Form\Control::render() throws exception.
     *
     * @test
     */
    public function testRenderMethodThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $app = m::mock('\Illuminate\Contracts\Container\Container');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Control($app, $request);

        $stub->render(
            [],
            new \Illuminate\Support\Fluent(['method' => 'foo'])
        );
    }
}
