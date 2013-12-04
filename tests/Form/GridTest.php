<?php namespace Orchestra\Support\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container;
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
     * Fieldset config.
     *
     * @return array
     */
    private function getFieldsetConfig()
    {
        return array(
            'select'   => array(),
            'textarea' => array(),
            'input'    => array(),
            'password' => array(),
            'file'     => array(),
            'radio'    => array(),
        );
    }

    /**
     * Get template config.
     *
     * @return array
     */
    private function getTemplateConfig()
    {
        return array(
            'input'    => function ($data) {
                return $data->name;
            },
            'textarea' => function ($data) {
                return $data->name;
            },
            'password' => function ($data) {
                return $data->name;
            },
            'file'     => function ($data) {
                return $data->name;
            },
            'radio'    => function ($data) {
                return $data->name;
            },
            'checkbox' => function ($data) {
                return $data->name;
            },
            'select'   => function ($data) {
                return $data->name;
            },
        );
    }

    /**
     * Test instanceof Orchestra\Html\Form\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array(
                'submit'     => 'Submit',
                'attributes' => array('id' => 'foo'),
                'view'       => 'foo',
            ));

        $stub = new Grid($app);
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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $mock = new \Illuminate\Support\Fluent;
        $stub = new Grid($app);
        $stub->with($mock);

        $refl = new \ReflectionObject($stub);
        $row  = $refl->getProperty('row');
        $row->setAccessible(true);

        $this->assertEquals($mock, $row->getValue($stub));
        $this->assertEquals($mock, $stub->row());
        $this->assertTrue(isset($stub->row));
    }

    /**
     * Test Orchestra\Html\Form\Grid::layout() method.
     *
     * @test
     */
    public function testLayoutMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->twice()
                ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig())
            ->shouldReceive('get')->once()
                ->with('orchestra/html::form', array())->andReturn(array(
                    'fieldset' => $this->getFieldsetConfig(),
                    'template' => $this->getTemplateConfig(),
                ));
        $control->shouldReceive('setConfig')->twice()->with($this->getFieldsetConfig())->andReturn(null);

        $stub      = new Grid($app);
        $refl      = new \ReflectionObject($stub);
        $fieldsets = $refl->getProperty('fieldsets');
        $fieldsets->setAccessible(true);

        $this->assertEquals(array(), $fieldsets->getValue($stub));

        $stub->fieldset('Foobar', function ($f) {
            //
        });

        $stub->fieldset(function ($f) {
            //
        });

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['form'] = $form = m::mock('Form');

        $form->shouldReceive('hidden')->once()
                ->with('foo', 'foobar', m::any())->andReturn('hidden_foo')
            ->shouldReceive('hidden')->once()
                ->with('foobar', 'stubbed', m::any())->andReturn('hidden_foobar');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->with(new \Illuminate\Support\Fluent(array(
            'foo'    => 'foobar',
            'foobar' => 'foo',
        )));

        $stub->hidden('foo');
        $stub->hidden('foobar', function ($f) {
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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->attributes = 'foo';
    }

    /**
     * Test Orchestra\Html\Form\Grid::of() method throws exception.
     *
     * @expectedException \RuntimeException
     */
    public function testOfMethodThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
        $stub->of('foo');
    }

    /**
     * Test Orchestra\Html\Form\Grid magic method __isset() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodIssetThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $stub = new Grid($app);
        $invalid = isset($stub->invalidProperty) ? true : false;
    }

    /**
     * Test Orchestra\Html\Form\Grid::resource() method as POST to create.
     *
     * @test
     */
    public function testResourceMethodAsPost()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $listener = m::mock('\Orchestra\Html\Form\PresenterInterface');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $listener->shouldReceive('handles')->once()
                ->with('orchestra::users')->andReturn('orchestra::users')
            ->shouldReceive('setupForm')->once();

        $stub = new Grid($app);
        $stub->resource($listener, 'orchestra::users', $model);
    }

    /**
     * Test Orchestra\Html\Form\Grid::resource() method as PUT to update.
     *
     * @test
     */
    public function testResourceMethodAsPut()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        $listener = m::mock('\Orchestra\Html\Form\PresenterInterface');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $model->exists = true;
        $model->shouldReceive('getKey')->once()->andReturn(20);

        $listener->shouldReceive('handles')->once()
                ->with('orchestra::users/20')->andReturn('orchestra::users/20')
            ->shouldReceive('setupForm')->once();

        $stub = new Grid($app);
        $stub->resource($listener, 'orchestra::users', $model);
    }
}
