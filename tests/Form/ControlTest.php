<?php namespace Orchestra\Html\Form\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Orchestra\Html\Form\Control;

class ControlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Html\Form\Control configuration methods.
     *
     * @test
     */
    public function testTemplateMethods()
    {
        $template = array('foo' => 'foobar');

        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $html = m::mock('\Orchestra\Html\HtmlBuilder');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Control($config, $html, $request);

        $stub->setTemplates($template);

        $this->assertEquals($template, $stub->getTemplates());
    }

     /**
     * Test Orchestra\Html\Form\Control::buildFluentData() method.
     *
     * @test
     */
    public function testBuildFluentDataMethod()
    {
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $html = m::mock('\Orchestra\Html\HtmlBuilder');
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('old')->once()->with('foobar')->andReturn(null);

        $row = new Fluent(array(
            'foobar' => function () {
                return 'Mr Derp';
            },
        ));

        $control = new Fluent(array(
            'name' => 'foobar',
        ));

        $stub = new Control($config, $html, $request);
        $stub->buildFluentData('text', $row, $control);
    }

    /**
     * Test Orchestra\Html\Form\Control::render() throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRenderMethodThrowsException()
    {
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $html = m::mock('\Orchestra\Html\HtmlBuilder');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Control($config, $html, $request);

        $stub->render(
            array(),
            new \Illuminate\Support\Fluent(array('method' => 'foo'))
        );
    }
}
