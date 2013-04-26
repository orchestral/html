<?php namespace Orchestra\Html\Tests;

use Mockery as m;
use Orchestra\Html\HtmlBuilder;

class HtmlBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Application instance.
	 *
	 * @var Illuminate\Foundation\Application
	 */
	private $app = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->app = m::mock('\Illuminate\Routing\UrlGenerator');
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
	 * Test Orchestra\Html\HtmlBuilder::create() with content
	 * 
	 * @test
	 */
	public function testCreateWithContent()
	{
		$stub     = new HtmlBuilder($this->app);
		$expected = '<div class="foo">Bar</div>';
		$output   = $stub->create('div', 'Bar', array('class' => 'foo'));

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Orchestra\Html\HtmlBuilder::create() without content
	 * 
	 * @test
	 */
	public function testCreateWithoutContent()
	{
		$stub     = new HtmlBuilder($this->app);
		$expected = '<img src="hello.jpg" class="foo">';
		$output   = $stub->create('img', array(
			'src'   => 'hello.jpg', 
			'class' => 'foo',
		));

		$this->assertEquals($expected, $output);

		$expected = '<img src="hello.jpg" class="foo">';
		$output   = $stub->create('img', null, array(
			'src' => 'hello.jpg', 
			'class' => 'foo',
		));

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Orchestra\Html\HtmlBuilder::entities() method
	 *
	 * @test
	 */
	public function testEntitiesMethod()
	{
		$stub   = new HtmlBuilder($this->app);
		$output = $stub->raw('<img src="foo.jpg">');

		$this->assertEquals('<img src="foo.jpg">', $stub->entities($output));

		$output = '<img src="foo.jpg">';
		$this->assertEquals('&lt;img src=&quot;foo.jpg&quot;&gt;', $stub->entities($output));
	}

	/**
	 * Test Orchestra\Html\HtmlBuilder::raw() method.
	 *
	 * @test
	 */
	public function testRawExpressionMethod()
	{
		$stub   = new HtmlBuilder($this->app);
		$this->assertInstanceOf('\Orchestra\Support\Expression', $stub->raw('hello'));
	}

	/**
	 * Test Orchestra\Html\HtmlBuilder::decorate() method.
	 *
	 * @test
	 */
	public function testDecorateMethod()
	{
		$stub   = new HtmlBuilder($this->app);
		
		$output   = $stub->decorate(array('class' => 'span4 table'), array('id' => 'foobar'));
		$expected = array('id' => 'foobar', 'class' => 'span4 table');
		$this->assertEquals($expected, $output);

		$output   = $stub->decorate(array('class' => 'span4 !span12'), array('class' => 'span12'));
		$expected = array('class' => 'span4');
		$this->assertEquals($expected, $output);

		$output   = $stub->decorate(array('id' => 'table'), array('id' => 'foobar', 'class' => 'span4'));
		$expected = array('id' => 'table', 'class' => 'span4');
		$this->assertEquals($expected, $output);
	}
}
