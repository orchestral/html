<?php namespace Orchestra\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Orchestra\Html\Table\Column;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
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
