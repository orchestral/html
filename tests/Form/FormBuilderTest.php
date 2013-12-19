<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\FormBuilder;
use Orchestra\Html\Form\Grid;

class FormBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct a new Orchestra\Html\Form\FormBuilder.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\View\Environment');
        $grid = new Grid($app);

        $stub = new FormBuilder($request, $translator, $view, $grid);

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
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\View\Environment');
        $grid = new Grid($app);

        $stub = new FormBuilder($request, $translator, $view, $grid);
        $stub->someInvalidRequest;
    }

    /**
     * test Orchestra\Html\Form\FormBuilder::render() method.
     *
     * @test
     */
    public function testRenderMethod()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\View\Environment');
        $grid = new Grid($app);

        $translator->shouldReceive('get')->twice()->andReturn(array());
        $view->shouldReceive('make')->twice()->andReturn($view)
            ->shouldReceive('with')->twice()->andReturn($view)
            ->shouldReceive('render')->twice()->andReturn('mocked');

        $data = new \Illuminate\Support\Fluent(array(
            'id'   => 1,
            'name' => 'Laravel'
        ));

        $stub1 = new FormBuilder($request, $translator, $view, $grid);
        $stub1->extend(function ($form) use ($data) {
            $form->with($data);
            $form->attributes(array(
                'method' => 'POST',
                'url'    => 'http://localhost',
                'class'  => 'foo',
            ));
        });

        $stub2 = new FormBuilder($request, $translator, $view, $grid);
        $stub2->extend(function ($form) use ($data) {
            $form->with($data);
            $form->attributes = array(
                'method' => 'POST',
                'url'    => 'http://localhost',
                'class'  => 'foo'
            );
        });

        ob_start();
        echo $stub1;
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('mocked', $output);
        $this->assertEquals('mocked', $stub2->render());
    }
}
