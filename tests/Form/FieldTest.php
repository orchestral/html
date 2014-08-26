<?php namespace Orchestra\Html\Form\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Orchestra\Html\Form\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Html\Form\Field::getField() method.
     *
     * @test
     */
    public function testGetFieldMethod()
    {
        $stub = new Field(array(
            'field' => function ($row) {
                return 'foo';
            },
        ));

        $row = new Fluent();
        $control = new Fluent();

        $this->assertEquals('foo', $stub->getField($row, $control));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

    /**
     * Test Orchestra\Html\Form\Field::getField() method
     * when given \Illuminate\Support\Facades\RenderableInterface.
     *
     * @test
     */
    public function testGetFieldMethodWhenGivenRenderable()
    {
        $renderable = m::mock('\Illuminate\Support\Contracts\RenderableInterface');

        $renderable->shouldReceive('render')->once()->andReturn('foo');

        $stub = new Field(array(
            'field' => function ($row) use ($renderable) {
                return $renderable;
            },
        ));

        $row = new Fluent();
        $control = new Fluent();

        $this->assertEquals('foo', $stub->getField($row, $control));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }
}
