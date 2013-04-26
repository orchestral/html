<?php namespace Orchestra\Support\Tests\Form;

use Mockery as m;
use Orchestra\Html\Form\Grid;

class GridTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$app = m::mock('Application');
		$config = m::mock('Config');

		$app->shouldReceive('instance')->andReturn(true);

		$fieldset = array(
			'select'   => array(),
			'textarea' => array(),
			'input'    => array(),
			'password' => array(),
			'file'     => array(),
			'radio'    => array(),
		);

		$template = array(
			'input'    => function ($data) { return $data->name; },
			'textarea' => function ($data) { return $data->name; },
			'password' => function ($data) { return $data->name; },
			'file'     => function ($data) { return $data->name; },
			'radio'    => function ($data) { return $data->name; },
			'checkbox' => function ($data) { return $data->name; },
			'select'   => function ($data) { return $data->name; },
		);

		$config->shouldReceive('get')->with('orchestra/html::form.fieldset')->andReturn($fieldset)
			->shouldReceive('get')->with('orchestra/html::form.templates', array())->andReturn($template);

		\Illuminate\Support\Facades\Config::setFacadeApplication($app);
		\Illuminate\Support\Facades\Config::swap($config);
		\Illuminate\Support\Facades\Form::setFacadeApplication($app);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Test instanceof Orchestra\Html\Form\Grid.
	 *
	 * @test
	 */
	public function testInstanceOfGrid()
	{
		$stub = new Grid(array(
			'submit'     => 'Submit',
			'attributes' => array('id' => 'foo'),
			'view'       => 'foo',
		));

		$stub->attributes = array('class' => 'foobar');

		$refl       = new \ReflectionObject($stub);
		$attributes = $refl->getProperty('attributes');
		$submit     = $refl->getProperty('submit');
		$view       = $refl->getProperty('view');

		$attributes->setAccessible(true);
		$submit->setAccessible(true);
		$view->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Html\Form\Grid', $stub);
		$this->assertEquals('Submit', $submit->getValue($stub));
		$this->assertEquals('foo', $view->getValue($stub));

		$this->assertEquals('foo', $stub->view());
		$this->assertEquals('foo', $stub->view);
		$this->assertEquals(array('id' => 'foo', 'class' => 'foobar'), $attributes->getValue($stub));
	}

	/**
	 * Test Orchestra\Html\Form\Grid::row() method.
	 *
	 * @test
	 */
	public function testWithMethod()
	{
		$mock = new \Illuminate\Support\Fluent;
		$stub = new Grid(array());
		$stub->with($mock);

		$refl = new \ReflectionObject($stub);
		$row  = $refl->getProperty('row');
		$row->setAccessible(true);

		$this->assertEquals($mock, $row->getValue($stub));
		$this->assertTrue(isset($stub->row));
	}

	/**
	 * Test Orchestra\Html\Form\Grid::layout() method.
	 *
	 * @test
	 */
	public function testLayoutMethod()
	{
		$stub = new Grid(array());

		$refl = new \ReflectionObject($stub);
		$view = $refl->getProperty('view');
		$view->setAccessible(true);

		$stub->layout('horizontal');
		$this->assertEquals('orchestra/html::form.horizontal', $view->getValue($stub));

		$stub->layout('vertical');
		$this->assertEquals('orchestra/html::form.vertical', $view->getValue($stub));

		$stub->layout('foo');
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Html\Form\Grid::attributes() method.
	 *
	 * @test
	 */
	public function testAttributesMethod()
	{
		$stub = new Grid(array());

		$refl   = new \ReflectionObject($stub);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$stub->attributes(array('class' => 'foo'));

		$this->assertEquals(array('class' => 'foo'), $attributes->getValue($stub));
		$this->assertEquals(array('class' => 'foo'), $stub->attributes());

		$stub->attributes('id', 'foobar');

		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $attributes->getValue($stub));
		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $stub->attributes());
	}

	/**
	 * Test Orchestra\Html\Form\Grid::fieldset() method.
	 *
	 * @test
	 */
	public function testFieldsetMethod()
	{
		$stub      = new Grid(array());
		$refl      = new \ReflectionObject($stub);
		$fieldsets = $refl->getProperty('fieldsets');
		$fieldsets->setAccessible(true);

		$this->assertEquals(array(), $fieldsets->getValue($stub));

		$stub->fieldset('Foobar', function ($f) {});
		$stub->fieldset(function ($f) {});

		$fieldset = $fieldsets->getValue($stub);

		$this->assertInstanceOf('\Orchestra\Html\Form\Fieldset', $fieldset[0]);
		$this->assertEquals('Foobar', $fieldset[0]->name);
		$this->assertInstanceOf('\Orchestra\Html\Form\Fieldset', $fieldset[1]);
		$this->assertNull($fieldset[1]->name);
	}

	/**
	 * Test Orchestra\Html\Form\Grid::hidden() method.
	 *
	 * @test
	 */
	public function testHiddenMethod()
	{
		$form = m::mock('Form');
		$form->shouldReceive('hidden')->once()->with('foo', 'foobar', m::any())->andReturn('hidden_foo')
			->shouldReceive('hidden')->once()->with('foobar', 'stubbed', m::any())->andReturn('hidden_foobar');

		\Illuminate\Support\Facades\Form::swap($form);

		$stub = new Grid(array());
		$stub->with(new \Illuminate\Support\Fluent(array(
			'foo'    => 'foobar',
			'foobar' => 'foo',
		)));

		$stub->hidden('foo');
		$stub->hidden('foobar', function ($f)
		{
			$f->value('stubbed');
		});

		$refl    = new \ReflectionObject($stub);
		$hiddens = $refl->getProperty('hiddens'); 
		$hiddens->setAccessible(true);

		$data = $hiddens->getValue($stub);
		$this->assertEquals('hidden_foo', $data['foo']);
		$this->assertEquals('hidden_foobar', $data['foobar']);
	}

	/**
	 * Test Orchestra\Html\Form\Grid magic method __call() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodCallThrowsException()
	{
		$stub = new Grid(array());
		$stub->invalidMethod();
	}

	/**
	 * Test Orchestra\Html\Form\Grid magic method __get() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodGetThrowsException()
	{
		$stub = new Grid(array());
		$invalid = $stub->invalidProperty;
	}

	/**
	 * Test Orchestra\Html\Form\Grid magic method __set() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodSetThrowsException()
	{
		$stub = new Grid(array());
		$stub->invalidProperty = array('foo');
	}

	/**
	 * Test Orchestra\Html\Form\Grid magic method __set() throws 
	 * exception when $values is not an array.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
	{
		$stub = new Grid(array());
		$stub->attributes = 'foo';
	}

	/**
	 * Test Orchestra\Html\Form\Grid magic method __isset() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodIssetThrowsException()
	{
		$stub = new Grid(array());
		$invalid = isset($stub->invalidProperty) ? true : false;
	}
}
