<?php

namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Orchestra\Html\Form\Grid;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Orchestra\Html\Form\FormBuilder;

class FormBuilderTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
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
        $grid = new Grid($this->getContainer(), []);

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $stub = new FormBuilder($request, $translator, $view, $grid);

        $refl = new \ReflectionObject($stub);
        $name = $refl->getProperty('name');
        $grid = $refl->getProperty('grid');

        $name->setAccessible(true);
        $grid->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Form\FormBuilder', $stub);
        $this->assertInstanceOf('\Orchestra\Html\Builder', $stub);

        $this->assertNull($name->getValue($stub));
        $this->assertNull($stub->name);
        $this->assertInstanceOf('\Orchestra\Html\Form\Grid', $grid->getValue($stub));
        $this->assertInstanceOf('\Orchestra\Html\Form\Grid', $stub->grid);
    }

    /**
     * test Orchestra\Html\Form\FormBuilder::__get() throws an exception.
     *
     * @test
     */
    public function testMagicMethodThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $grid = new Grid($this->getContainer(), []);

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\Contracts\View\Factory');

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
        $grid = new Grid($this->getContainer(), []);

        $request = m::mock('\Illuminate\Http\Request');
        $translator = m::mock('\Illuminate\Translation\Translator');
        $view = m::mock('\Illuminate\Contracts\View\Factory');

        $translator->shouldReceive('get')->twice()->andReturn([]);
        $view->shouldReceive('make')->twice()->andReturn($view)
            ->shouldReceive('with')->twice()->andReturn($view)
            ->shouldReceive('render')->twice()->andReturn('mocked');

        $data = new \Illuminate\Support\Fluent([
            'id' => 1,
            'name' => 'Laravel',
        ]);

        $stub1 = new FormBuilder($request, $translator, $view, $grid);
        $stub1->extend(function ($form) use ($data) {
            $form->with($data);
            $form->attributes([
                'method' => 'POST',
                'url' => 'http://localhost',
                'class' => 'foo',
            ]);
        });

        $stub2 = new FormBuilder($request, $translator, $view, $grid);
        $stub2->extend(function ($form) use ($data) {
            $form->with($data);
            $form->attributes = [
                'method' => 'POST',
                'url' => 'http://localhost',
                'class' => 'foo',
            ];
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
