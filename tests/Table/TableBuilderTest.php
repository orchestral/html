<?php namespace Orchestra\Html\Tests\Table;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Fluent;
use Orchestra\Html\Table\TableBuilder;
use Orchestra\Html\Table\Grid;

class TableBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test construct a new Orchestra\Html\Table\TableBuilder.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\View\Environment');
        $grid = new Grid($app);

        $stub = new TableBuilder($request, $translator, $view, $grid);

        $refl = new \ReflectionObject($stub);
        $name = $refl->getProperty('name');
        $grid = $refl->getProperty('grid');

        $name->setAccessible(true);
        $grid->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $stub);
        $this->assertInstanceOf('\Orchestra\Html\Abstractable\Builder', $stub);
        $this->assertInstanceOf('\Illuminate\Support\Contracts\RenderableInterface', $stub);

        $this->assertNull($name->getValue($stub));
        $this->assertNull($stub->name);
        $this->assertInstanceOf('\Orchestra\Html\Table\Grid', $grid->getValue($stub));
        $this->assertInstanceOf('\Orchestra\Html\Table\Grid', $stub->grid);
    }

    /**
     * test Orchestra\Html\Table\TableBuilder::__get() throws an exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodThrowsException()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\View\Environment');
        $grid = new Grid($app);

        $stub = new TableBuilder($request, $translator, $view, $grid);
        $stub->someInvalidRequest;
    }

    /**
     * test Orchestra\Html\Table\TableBuilder::render() method.
     *
     * @test
     */
    public function testRenderMethod()
    {
        $app = new Container;
        $app['config'] = $config = m::mock('\Illuminate\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::table', array())->andReturn(array());

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\View\Environment');
        $grid = new Grid($app);

        $request->shouldReceive('query')->twice()->andReturn(array('page' => 2, 'q' => 'user'));
        $translator->shouldReceive('get')->twice()->andReturn(array());
        $view->shouldReceive('make')->twice()->andReturn($view)
            ->shouldReceive('with')->twice()->andReturn($view)
            ->shouldReceive('render')->twice()->andReturn('mocked');

        $mock = array(
            new Fluent(array('id' => 1, 'name' => 'Laravel')),
            new Fluent(array('id' => 2, 'name' => 'Illuminate')),
            new Fluent(array('id' => 3, 'name' => 'Symfony')),
        );

        $stub1 = new TableBuilder($request, $translator, $view, $grid);
        $stub1->extend(function ($t) use ($mock) {
            $t->rows($mock);
            $t->attributes(array('class' => 'foo'));

            $t->column('id');
            $t->column(function ($c) {
                $c->id = 'name';
                $c->label('Name');
                $c->value(function ($row) {
                    return $row->name;
                });
            });
        });

        $stub2 = new TableBuilder($request, $translator, $view, $grid);
        $stub2->extend(function ($t) use ($mock) {
            $t->rows($mock);
            $t->attributes = array('class' => 'foo');

            $t->column('ID', 'id');
            $t->column('name', function ($c) {
                $c->value(function ($row) {
                    return '<strong>'.$row->name.'</strong>';
                });
            });
        });

        ob_start();
        echo $stub1;
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('mocked', $output);
        $this->assertEquals('mocked', $stub2->render());
    }
}
