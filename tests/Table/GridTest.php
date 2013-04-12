<?php namespace Orchestra\Html\Tests\Table;

class GridTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Orchestra\Html\Table\Grid.
	 *
	 * @test
	 */
	public function testInstanceOfGrid()
	{
		$stub = new \Orchestra\Html\Table\Grid(array(
			'emptyMessage' => 'No data',
			'view'         => 'foo',
		));

		$refl         = new \ReflectionObject($stub);
		$emptyMessage = $refl->getProperty('emptyMessage');
		$view         = $refl->getProperty('view');

		$emptyMessage->setAccessible(true);
		$view->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Html\Table\Grid', $stub);
		$this->assertEquals('No data', $emptyMessage->getValue($stub));
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Html\Table\Grid::with() method.
	 *
	 * @test
	 */
	public function testWithMethod()
	{
		$mock = array(new \Illuminate\Support\Fluent);
		$stub = new \Orchestra\Html\Table\Grid(array());
		$stub->with($mock, false);

		$refl     = new \ReflectionObject($stub);
		$rows     = $refl->getProperty('rows');
		$model    = $refl->getProperty('model');
		$paginate = $refl->getProperty('paginate');

		$rows->setAccessible(true);
		$model->setAccessible(true);
		$paginate->setAccessible(true);

		$this->assertEquals($mock, $rows->getValue($stub)->data);
		$this->assertEquals($mock, $model->getValue($stub));
		$this->assertFalse($paginate->getValue($stub));
		$this->assertTrue(isset($stub->model));
	}

	/**
	 * Test Orchestra\Html\Table\Grid::layout() method.
	 *
	 * @test
	 */
	public function testLayoutMethod()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$refl = new \ReflectionObject($stub);
		$view = $refl->getProperty('view');
		$view->setAccessible(true);

		$stub->layout('horizontal');
		$this->assertEquals('orchestra::support.table.horizontal', $view->getValue($stub));

		$stub->layout('vertical');
		$this->assertEquals('orchestra::support.table.vertical', $view->getValue($stub));

		$stub->layout('foo');
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Html\Table\Grid::of() method.
	 *
	 * @test
	 */
	public function testOfMethod()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());
		$expected = array(
			new \Illuminate\Support\Fluent(array(
				'id'           => 'id',
				'label'        => 'Id',
				'value'        => 'Foobar',
				'label_attributes' => array(),
				'cell_attributes'  => function ($row) { return array(); }
			)),
			new \Illuminate\Support\Fluent(array(
				'id'               => 'foo1',
				'label'            => 'Foo1',
				'value'            => 'Foo1 value',
				'label_attributes' => array(),
				'cell_attributes'  => function ($row) { return array(); }
			)),
			new \Illuminate\Support\Fluent(array(
				'id'               => 'foo2',
				'label'            => 'Foo2',
				'value'            => 'Foo2 value',
				'label_attributes' => array(),
				'cell_attributes'  => function ($row) { return array(); }
			))
		);

		$stub->column('id', function ($c)
		{
			$c->value('Foobar');
		});

		$stub->column(function ($c)
		{
			$c->id('foo1')->label('Foo1');
			$c->value('Foo1 value');
		});

		$stub->column('Foo2', 'foo2')->value('Foo2 value');

		$stub->attributes = array('class' => 'foo');

		$output = $stub->of('id');

		$this->assertEquals('Foobar', $output->value);
		$this->assertEquals('Id', $output->label);
		$this->assertEquals('id', $output->id);

		$this->assertEquals(array('class' => 'foo'), $stub->attributes);
		$this->assertEquals($expected, $stub->columns());
	}

	/**
	 * Test Orchestra\Html\Table\Grid::of() method throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testOfMethodThrowsException()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$output = $stub->of('id');
	}

	/**
	 * Test Orchestra\Html\Table\Grid::attributes() method.
	 *
	 * @test
	 */
	public function testAttributesMethod()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

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
	 * Test Orchestra\Html\Table\Grid magic method __call() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodCallThrowsException()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$stub->invalid_method();
	}

	/**
	 * Test Orchestra\Html\Table\Grid magic method __get() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodGetThrowsException()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$invalid = $stub->invalid_property;
	}

	/**
	 * Test Orchestra\Html\Table\Grid magic method __set() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodSetThrowsException()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$stub->invalid_property = array('foo');
	}

	/**
	 * Test Orchestra\Html\Table\Grid magic method __set() throws 
	 * exception when $values is not an array.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$stub->attributes = 'foo';
	}

	/**
	 * Test Orchestra\Html\Table\Grid magic method __isset() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testMagicMethodIssetThrowsException()
	{
		$stub = new \Orchestra\Html\Table\Grid(array());

		$invalid = isset($stub->invalid_property) ? true : false;
	}
}