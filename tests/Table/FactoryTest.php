<?php namespace Orchestra\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Table\Factory;

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

        $this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
    }

    /**
     * Test Orchestra\Html\Table\Factory::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $stub   = new Factory($this->getContainer());
        $output = $stub->of('foo', function () {
            //
        });

        $this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $output);
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app                                           = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['request']                                = m::mock('\Illuminate\Http\Request');
        $app['translator']                             = m::mock('\Illuminate\Translation\Translator');
        $app['view']                                   = m::mock('\Illuminate\Contracts\View\Factory');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', [])->andReturn([]);

        return $app;
    }
}
