<?php namespace Orchestra\Html\Form\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
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
        $stub   = new Factory($this->getContainer());
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
        $stub   = new Factory($this->getContainer());
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
        $app = new Container;
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
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['request'] = m::mock('\Illuminate\Http\Request');
        $app['translator'] = m::mock('\Illuminate\Translation\Translator');
        $app['view'] = m::mock('\Illuminate\Contracts\View\Factory');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        return $app;
    }
}
