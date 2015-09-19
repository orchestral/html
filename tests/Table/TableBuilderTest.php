<?php namespace Orchestra\Html\Table\TestCase;

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
        $grid = new Grid($this->getContainer(), []);

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new TableBuilder($request, $translator, $view, $grid);

        $refl = new \ReflectionObject($stub);
        $name = $refl->getProperty('name');
        $grid = $refl->getProperty('grid');

        $name->setAccessible(true);
        $grid->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Table\TableBuilder', $stub);
        $this->assertInstanceOf('\Orchestra\Html\Builder', $stub);
        $this->assertInstanceOf('\Illuminate\Contracts\Support\Renderable', $stub);

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
        $grid = new Grid($this->getContainer(), []);

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\Contracts\View\Factory');

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
        $grid = new Grid($this->getContainer(), []);

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $request->shouldReceive('query')->twice()->andReturn(['page' => 2, 'q' => 'user']);
        $translator->shouldReceive('get')->twice()->andReturn([]);
        $view->shouldReceive('make')->twice()->andReturn($view)
            ->shouldReceive('with')->twice()->andReturn($view)
            ->shouldReceive('render')->twice()->andReturn('mocked');

        $mock = [
            new Fluent(['id' => 1, 'name' => 'Laravel']),
            new Fluent(['id' => 2, 'name' => 'Illuminate']),
            new Fluent(['id' => 3, 'name' => 'Symfony']),
        ];

        $stub1 = new TableBuilder($request, $translator, $view, $grid);
        $stub1->extend(function ($t) use ($mock) {
            $t->rows($mock);
            $t->attributes(['class' => 'foo']);

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
            $t->attributes = ['class' => 'foo'];

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

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        return new Container();
    }
}
