<?php

namespace Orchestra\Html\Tests\Form;

use Illuminate\Container\Container;
use Mockery as m;
use Orchestra\Html\Form\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Html\Table\Factory::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $stub = new Factory($this->getContainer());
        $stub->setConfig([]);

        $output = $stub->make(function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
    }

    /**
     * Test Orchestra\Html\Form\Factory::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $stub = new Factory($this->getContainer());
        $output = $stub->of('foo', function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
    }

    /**
     * Test Orchestra\Html\Form\Factory pass-through method
     * to \Illuminate\Html\FormBuilder.
     *
     * @test
     */
    public function testPassThroughMethod()
    {
        $app = new Container();
        $app['form'] = $form = m::mock('\Illuminate\Html\FormBuilder');

        $form->shouldReceive('hidden')->once()->with('foo', 'bar')->andReturn('foobar');

        $stub = new Factory($app);
        $output = $stub->hidden('foo', 'bar');

        $this->assertEquals('foobar', $output);
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app = new Container();

        $app['request'] = m::mock('\Illuminate\Http\Request');
        $app['translator'] = m::mock('\Illuminate\Translation\Translator');
        $app['view'] = m::mock('\Illuminate\Contracts\View\Factory');

        return $app;
    }
}
