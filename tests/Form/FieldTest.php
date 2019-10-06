<?php

namespace Orchestra\Html\Tests\Form;

use Illuminate\Support\Fluent;
use Mockery as m;
use Orchestra\Html\Form\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
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
        $stub = new Field([
            'field' => function ($row) {
                return 'foo';
            },
        ]);

        $row = new Fluent();

        $this->assertEquals('foo', $stub->getField($row));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

    /**
     * Test Orchestra\Html\Form\Field::getField() method
     * when given \Illuminate\Support\Facades\Renderable.
     *
     * @test
     */
    public function testGetFieldMethodWhenGivenRenderable()
    {
        $renderable = m::mock('\Illuminate\Contracts\Support\Renderable');

        $renderable->shouldReceive('render')->once()->andReturn('foo');

        $stub = new Field([
            'field' => function ($row) use ($renderable) {
                return $renderable;
            },
        ]);

        $row = new Fluent();

        $this->assertEquals('foo', $stub->getField($row));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }
}
