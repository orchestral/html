<?php

namespace Orchestra\Html\Tests\Table;

use Illuminate\Support\Fluent;
use Mockery as m;
use Orchestra\Html\Table\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Html\Table\Column::getVakye() method.
     */
    public function testGetValueMethod()
    {
        $stub = new Column([
            'value' => function ($row) {
                return '<strong>';
            },
        ]);

        $row = new Fluent();

        $this->assertEquals('<strong>', $stub->getValue($row));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

    /**
     * Test Orchestra\Html\Table\Column::getVakye() method with escape
     * string.
     */
    public function testGetValueMethodWithEscapeString()
    {
        $stub = new Column([
            'value' => function ($row) {
                return '<strong>';
            },
            'escape' => true,
        ]);

        $row = new Fluent();

        $this->assertEquals('&lt;strong&gt;', $stub->getValue($row));
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }
}
