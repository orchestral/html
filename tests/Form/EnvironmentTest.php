<?php namespace Orchestra\Html\Form\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Html\Table\Environment::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $stub   = new Environment($this->getContainer());
        $output = $stub->make(function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
    }

    /**
     * Test Orchestra\Html\Form\Environment::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $stub   = new Environment($this->getContainer());
        $output = $stub->of('foo', function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $output);
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');
        $app['request'] = m::mock('\Illuminate\Http\Request');
        $app['translator'] = m::mock('\Illuminate\Translation\Translator');
        $app['view'] = m::mock('\Illuminate\View\Environment');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        return $app;
    }
}
