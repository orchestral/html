<?php namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Fluent;
use Orchestra\Html\Form\Control;
use Orchestra\Html\Form\Fieldset;

class FieldsetTest extends \PHPUnit_Framework_TestCase
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
            'checkbox' => array(),
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
     * Test instance of Orchestra\Html\Form\Fieldset.
     *
     * @test
     */
    public function testInstanceOfFieldset()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, 'foo', function ($f) {
            $f->attributes(array('class' => 'foo'));
        });

        $this->assertEquals('foo', $stub->legend());

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null)
            ->shouldReceive('generate')->once()->with('text');

        $stub = new Fieldset($app, function ($f) {
            $f->control('text', 'id', function ($c) {
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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['request'] = $input = m::mock('Input');
        $app['html'] = $html = m::mock('Html');

        $app['orchestra.form.control'] = $control = new Control($app);

        $config->shouldReceive('get')->once()
                ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig())
            ->shouldReceive('get')->times(10)
                ->with('orchestra/html::form.templates', array())->andReturn($this->getTemplateConfig());
        $input->shouldReceive('old')->times(10)->andReturn(array());
        $html->shouldReceive('decorate')->times(10)->andReturn('foo');

        $stub = new Fieldset($app, function ($f) {
            $f->control('text', 'text_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('input:email', 'email_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('password', 'password_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('file', 'file_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('input:textarea', 'textarea_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('radio', 'radio_foo', function ($c) {
                $c->label('Foo')->value('foobar')->checked(true);
            });

            $f->control('checkbox', 'checkbox_foo', function ($c) {
                $c->label('Foo')->value('foobar')->checked(true);
            });

            $f->control('select', 'select_foo', function ($c) {
                $c->label('Foo')->value('foobar')->options(function () {
                    return array(
                        'yes' => 'Yes',
                        'no'  => 'No',
                    );
                });
            });

            $f->control('text', 'a', 'A');

            $f->control('text', function ($c) {
                $c->name('b')->label('B')->value('value-of-B');
            });
        });

        $output = $stub->of('text_foo');

        $this->assertEquals('text_foo', $output->id);
        $this->assertEquals('text_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('email_foo');

        $this->assertEquals('email_foo', $output->id);
        $this->assertEquals('email_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('password_foo');

        $this->assertEquals('password_foo', $output->id);
        $this->assertEquals('password_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('file_foo');

        $this->assertEquals('file_foo', $output->id);
        $this->assertEquals('file_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('textarea_foo');

        $this->assertEquals('textarea_foo', $output->id);
        $this->assertEquals('textarea_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('radio_foo');

        $this->assertEquals('radio_foo', $output->id);
        $this->assertEquals('radio_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('checkbox_foo');

        $this->assertEquals('checkbox_foo', $output->id);
        $this->assertEquals('checkbox_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('select_foo');

        $this->assertEquals('select_foo', $output->id);
        $this->assertEquals('select_foo', call_user_func($output->field, new Fluent, $output));

        $output = $stub->of('a');

        $this->assertEquals('a', $output->id);
        $this->assertEquals('a', call_user_func($output->field, new Fluent, $output));

        $controls = $stub->controls;
        $output   = end($controls);

        $this->assertEquals('b', call_user_func($output->field, new Fluent, $output));
    }

    /**
     * Test Orchestra\Html\Form\Fieldset::of() method throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOfMethodThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function ($f) {
            //
        });

        $stub->of('id');
    }

    /**
     * Test Orchestra\Support\Form\Grid::attributes() method.
     *
     * @test
     */
    public function testAttributesMethod()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function () {
            //
        });

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function ($f) {
            //
        });

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function ($f) {
            //
        });

        $stub->invalidProperty;
    }

    /**
     * Test Orchestra\Html\Form\Fieldset magic method __set() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsException()
    {
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function ($f) {
            //
        });

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function ($f) {
            //
        });

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
        $app = $this->app;
        $app['config'] = $config = m::mock('Config');
        $app['orchestra.form.control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetConfig());
        $control->shouldReceive('setConfig')->once()->with($this->getFieldsetConfig())->andReturn(null);

        $stub = new Fieldset($app, function ($f) {
            //
        });

        isset($stub->invalidProperty) ? true : false;
    }
}
