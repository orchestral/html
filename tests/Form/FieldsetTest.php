<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Orchestra\Html\Form\Fieldset;

class FieldsetTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$app    = m::mock('Application');
		$config = m::mock('Config');
		$input  = m::mock('Input');
		$html   = m::mock('Html');

		\Illuminate\Support\Facades\Config::setFacadeApplication($app);
		\Illuminate\Support\Facades\Html::setFacadeApplication($app);
		\Illuminate\Support\Facades\Input::setFacadeApplication($app);

		$fieldset = array(
			'select'   => array(),
			'textarea' => array(),
			'input'    => array(),
			'password' => array(),
			'file'     => array(),
			'radio'    => array(),
		);

		$templates = array(
			'input'    => function ($data) { return $data->name; },
			'textarea' => function ($data) { return $data->name; },
			'password' => function ($data) { return $data->name; },
			'file'     => function ($data) { return $data->name; },
			'radio'    => function ($data) { return $data->name; },
			'checkbox' => function ($data) { return $data->name; },
			'select'   => function ($data) { return $data->name; },
		);

		$app->shouldReceive('instance')->andReturn(true);
		$config->shouldReceive('get')->with('orchestra/html::form.fieldset')->andReturn($fieldset)
			->shouldReceive('get')->with('orchestra/html::form.templates', array())->andReturn($templates);
		$input->shouldReceive('old')->andReturn(array());
		$html->shouldReceive('decorate')->andReturn('foo');

		\Illuminate\Support\Facades\Config::swap($config);
		\Illuminate\Support\Facades\Html::swap($html);
		\Illuminate\Support\Facades\Input::swap($input);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		m::close();
	}

	/**
	 * Test instance of Orchestra\Html\Form\Fieldset.
	 *
	 * @test
	 */
	public function testInstanceOfFieldset()
	{
		$stub = new Fieldset('foo', function ($f)
		{
			$f->attributes(array('class' => 'foo'));
		});

		$this->assertInstanceOf('\Orchestra\Html\Form\Fieldset', $stub);
		$this->assertEquals(array(), $stub->controls);
		$this->assertTrue(isset($stub->name));

		$this->assertEquals(array('class' => 'foo'), $stub->attributes);
		$this->assertEquals('foo', $stub->name());

		$stub->attributes = array('class' => 'foobar');
		$this->assertEquals(array('class' => 'foobar'), $stub->attributes);
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset::of() method.
	 *
	 * @test
	 */
	public function testOfMethod()
	{
		$stub = new Fieldset(function ($f)
		{
			$f->control('text', 'id', function ($c)
			{
				$c->value('Foobar');
			});
		});		

		$output = $stub->of('id');

		$this->assertEquals('Foobar', $output->value);
		$this->assertEquals('Id', $output->label);
		$this->assertEquals('id', $output->id);
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset::control() method.
	 *
	 * @test
	 */
	public function testControlMethod()
	{
		$stub = new Fieldset(function ($f)
		{
			$f->control('text', 'text_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('input:email', 'email_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('password', 'password_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('file', 'file_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('textarea', 'textarea_foo', function ($c)
			{
				$c->label('Foo')->value('foobar');
			});

			$f->control('radio', 'radio_foo', function ($c)
			{
				$c->label('Foo')->value('foobar')->checked(true);
			});

			$f->control('checkbox', 'checkbox_foo', function ($c)
			{
				$c->label('Foo')->value('foobar')->checked(true);
			});

			$f->control('select', 'select_foo', function ($c)
			{
				$c->label('Foo')->value('foobar')->options(array(
					'yes' => 'Yes',
					'no'  => 'No',
				));
			});

			$f->control('text', 'a', 'A');

			$f->control('text', function($c)
			{
				$c->name('b')->label('B')->value('value-of-B');
			});
		});

		$output = $stub->of('text_foo');

		$this->assertEquals('text_foo', $output->id);
		$this->assertEquals('text_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('email_foo');

		$this->assertEquals('email_foo', $output->id);
		$this->assertEquals('email_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('password_foo');
		
		$this->assertEquals('password_foo', $output->id);
		$this->assertEquals('password_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('file_foo');
		
		$this->assertEquals('file_foo', $output->id);
		$this->assertEquals('file_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('textarea_foo');

		$this->assertEquals('textarea_foo', $output->id);
		$this->assertEquals('textarea_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('radio_foo');

		$this->assertEquals('radio_foo', $output->id);
		$this->assertEquals('radio_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('checkbox_foo');

		$this->assertEquals('checkbox_foo', $output->id);
		$this->assertEquals('checkbox_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('select_foo');

		$this->assertEquals('select_foo', $output->id);
		$this->assertEquals('select_foo', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$output = $stub->of('a');

		$this->assertEquals('a', $output->id);
		$this->assertEquals('a', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));

		$controls = $stub->controls;
		$output   = end($controls);

		$this->assertEquals('b', 
			 call_user_func($output->field, new \Illuminate\Support\Fluent, $output));
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset::of() method throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testOfMethodThrowsException()
	{
		$stub = new Fieldset(function ($f) {});

		$output = $stub->of('id');
	}

	/**
	 * Test Orchestra\Support\Form\Grid::attributes() method.
	 *
	 * @test
	 */
	public function testAttributesMethod()
	{
		$stub = new Fieldset(function () {});

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
	 * Test Orchestra\Html\Form\Fieldset magic method __call() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodCallThrowsException()
	{
		$stub = new Fieldset(function ($f) {});
		$stub->invalidMethod();
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset magic method __get() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodGetThrowsException()
	{
		$stub = new Fieldset(function ($f) {});
		$invalid = $stub->invalidProperty;
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset magic method __set() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodSetThrowsException()
	{
		$stub = new Fieldset(function ($f) {});
		$stub->invalidProperty = array('foo');
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset magic method __set() throws 
	 * exception when $values is not an array.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
	{
		$stub = new Fieldset(function ($f) {});
		$stub->attributes = 'foo';
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset magic method __isset() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodIssetThrowsException()
	{
		$stub    = new Fieldset(function ($f) {});
		$invalid = isset($stub->invalidProperty) ? true : false;
	}

	/**
	 * Test Orchestra\Html\Form\Fieldset::render() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testRenderMethodThrowsException()
	{
		Fieldset::render(
			array(),
			new \Illuminate\Support\Fluent(array('method' => 'foo'))
		);
	}
}
