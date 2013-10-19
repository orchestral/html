<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\FormBuilder;

class FormBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test construct a new Orchestra\Html\Form\FormBuilder.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new FormBuilder($app, function () { });

        $refl = new \ReflectionObject($stub);
        $name = $refl->getProperty('name');
        $grid = $refl->getProperty('grid');

        $name->setAccessible(true);
        $grid->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $stub);
        $this->assertInstanceOf('\Orchestra\Html\Abstractable\Builder', $stub);
        $this->assertInstanceOf('\Illuminate\Support\Contracts\RenderableInterface', $stub);

        $this->assertNull($name->getValue($stub));
        $this->assertNull($stub->name);
        $this->assertInstanceOf('\Orchestra\Html\Form\Grid', $grid->getValue($stub));
        $this->assertInstanceOf('\Orchestra\Html\Form\Grid', $stub->grid);
    }

    /**
     * test Orchestra\Html\Form\FormBuilder::__get() throws an exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->with('orchestra/html::form', array())->once()->andReturn(array());

        $stub = new FormBuilder($app, function () { });
        $stub->someInvalidRequest;
    }

    /**
     * test Orchestra\Html\Form\FormBuilder::render() method.
     *
     * @test
     */
    public function testRenderMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['translator'] = $lang = m::mock('Lang');
        $app['view'] = $view = m::mock('View');

        $config->shouldReceive('get')->twice()
            ->with('orchestra/html::form', array())->andReturn(array());
        $lang->shouldReceive('get')->twice()->andReturn(array());
        $view->shouldReceive('make')->twice()->andReturn($view)
            ->shouldReceive('with')->twice()->andReturn($view)
            ->shouldReceive('render')->twice()->andReturn('mocked');

        $data = new \Illuminate\Support\Fluent(array(
            'id'   => 1,
            'name' => 'Laravel'
        ));

        $mock1 = new FormBuilder($app, function ($form) use ($data)
        {
            $form->with($data);
            $form->attributes(array(
                'method' => 'POST',
                'url'    => 'http://localhost',
                'class'  => 'foo',
            ));
        });

        $mock2 = new FormBuilder($app, function ($form) use ($data)
        {
            $form->with($data);
            $form->attributes = array(
                'method' => 'POST',
                'url'    => 'http://localhost',
                'class'  => 'foo'
            );
        });

        ob_start();
        echo $mock1;
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('mocked', $output);
        $this->assertEquals('mocked', $mock2->render());
    }
}
