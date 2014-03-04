<?php namespace Orchestra\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Table\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Setup the test Factory.
     */
    public function setUp()
    {
        $this->app = new Container;
    }

    /**
     * Teardown the test Factory.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test Orchestra\Html\Table\Factory::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');
        $app['request'] = m::mock('\Illuminate\Http\Request');
        $app['translator'] = m::mock('\Illuminate\Translation\Translator');
        $app['view'] = m::mock('\Illuminate\View\Factory');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub   = new Factory($app);
        $output = $stub->make(function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
    }

    /**
     * Test Orchestra\Html\Table\Factory::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');
        $app['request'] = m::mock('\Illuminate\Http\Request');
        $app['translator'] = m::mock('\Illuminate\Translation\Translator');
        $app['view'] = m::mock('\Illuminate\View\Factory');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $stub   = new Factory($app);
        $output = $stub->of('foo', function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
    }
}
