<?php

namespace Orchestra\Html\Tests\Form;

use Mockery as m;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\TestCase;
use Orchestra\Html\Form\Control;
use Orchestra\Html\Form\Fieldset;
use Illuminate\Container\Container;
use Orchestra\Contracts\Html\Form\Field;
use Orchestra\Contracts\Html\Form\Template;

class FieldsetTest extends TestCase
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
    protected function setUp(): void
    {
        $this->app = new Container();
    }

    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        unset($this->app);
        m::close();
    }

    /**
     * Fieldset config.
     *
     * @return array
     */
    private function getFieldsetTemplates()
    {
        return [
            'button' => [],
            'checkbox' => [],
            'input' => [],
            'file' => [],
            'password' => [],
            'radio' => [],
            'select' => [],
            'textarea' => [],
        ];
    }

    /**
     * Get template config.
     *
     * @return array
     */
    private function getPresenterInstance()
    {
        return new StubTemplatePresenter();
    }

    /**
     * Test instance of Orchestra\Html\Form\Fieldset.
     *
     * @test
     */
    public function testInstanceOfFieldset()
    {
        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), 'foo', function ($f) {
            $f->attributes(['class' => 'foo']);
        });

        $this->assertEquals('foo', $stub->legend());

        $this->assertInstanceOf('\Orchestra\Html\Form\Fieldset', $stub);
        $this->assertEquals([], $stub->controls);
        $this->assertTrue(isset($stub->name));

        $this->assertEquals(['class' => 'foo'], $stub->attributes);
        $this->assertEquals('foo', $stub->name());

        $stub->attributes = ['class' => 'foobar'];
        $this->assertEquals(['class' => 'foobar'], $stub->attributes);
    }

    /**
     * Test Orchestra\Html\Form\Fieldset::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf()
            ->shouldReceive('generate')->once()->with('text');

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
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
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();
        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $app['Orchestra\Contracts\Html\Form\Control'] = $control = new Control($app, $request);

        $request->shouldReceive('old')->times(11)->andReturn([]);

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
            $f->control('button', 'button_foo', function ($c) {
                $c->label('Foo')->value(function () {
                    return 'foobar';
                });
            });

            $f->control('checkbox', 'checkbox_foo', function ($c) {
                $c->label('Foo')->value('foobar')->checked(true);
            });

            $f->control('file', 'file_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('input:email', 'email_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('input:textarea', 'textarea_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('password', 'password_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('radio', 'radio_foo', function ($c) {
                $c->label('Foo')->value('foobar')->checked(true);
            });

            $f->control('select', 'select_foo', function ($c) {
                $c->label('Foo')->value('foobar')->options(function () {
                    return [
                        'yes' => 'Yes',
                        'no' => 'No',
                    ];
                });
            });

            $f->control('text', 'text_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('text', 'a', 'A');

            $f->control('text', function ($c) {
                $c->name('b')->label('B')->value('value-of-B');
            });
        });

        $output = $stub->of('button_foo');

        $this->assertEquals('button_foo', $output->id);
        $this->assertEquals('button_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('checkbox_foo');

        $this->assertEquals('checkbox_foo', $output->id);
        $this->assertEquals('checkbox_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('email_foo');

        $this->assertEquals('email_foo', $output->id);
        $this->assertEquals('email_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('file_foo');

        $this->assertEquals('file_foo', $output->id);
        $this->assertEquals('file_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('password_foo');

        $this->assertEquals('password_foo', $output->id);
        $this->assertEquals('password_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('radio_foo');

        $this->assertEquals('radio_foo', $output->id);
        $this->assertEquals('radio_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('select_foo');

        $this->assertEquals('select_foo', $output->id);
        $this->assertEquals('select_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('text_foo');

        $this->assertEquals('text_foo', $output->id);
        $this->assertEquals('text_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('textarea_foo');

        $this->assertEquals('textarea_foo', $output->id);
        $this->assertEquals('textarea_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('a');

        $this->assertEquals('a', $output->id);
        $this->assertEquals('a', call_user_func($output->field, new Fluent(), $output));

        $controls = $stub->controls;
        $output = end($controls);

        $this->assertEquals('b', call_user_func($output->field, new Fluent(), $output));
    }

    /**
     * Test Orchestra\Html\Form\Fieldset::of() method throws exception.
     *
     * @test
     */
    public function testOfMethodThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
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
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function () {
            //
        });

        $refl = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);

        $stub->attributes(['class' => 'foo']);

        $this->assertEquals(['class' => 'foo'], $attributes->getValue($stub));
        $this->assertEquals(['class' => 'foo'], $stub->attributes());

        $stub->attributes('id', 'foobar');

        $this->assertEquals(['id' => 'foobar', 'class' => 'foo'], $attributes->getValue($stub));
        $this->assertEquals(['id' => 'foobar', 'class' => 'foo'], $stub->attributes());
    }

    /**
     * Test Orchestra\Html\Form\Fieldset magic method __call() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodCallThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
            //
        });

        $stub->invalidMethod();
    }

    /**
     * Test Orchestra\Html\Form\Fieldset magic method __get() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodGetThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
            //
        });

        $stub->invalidProperty;
    }

    /**
     * Test Orchestra\Html\Form\Fieldset magic method __set() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodSetThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
            //
        });

        $stub->invalidProperty = ['foo'];
    }

    /**
     * Test Orchestra\Html\Form\Fieldset magic method __set() throws
     * exception when $values is not an array.
     *
     * @test
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $this->expectException('InvalidArgumentException');

        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
            //
        });

        $stub->attributes = 'foo';
    }

    /**
     * Test Orchestra\Html\Form\Fieldset magic method __isset() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodIssetThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $app = $this->app;
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = $this->getPresenterInstance();

        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, $this->getFieldsetTemplates(), function ($f) {
            //
        });

        isset($stub->invalidProperty) ? true : false;
    }
}

class StubTemplatePresenter implements Template
{
    /**
     * Button template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function button(Field $field)
    {
        return $field->name;
    }

    /**
     * Checkbox template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkbox(Field $field)
    {
        return $field->name;
    }

    /**
     * Checkboxes template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkboxes(Field $field)
    {
        return $field->name;
    }

    /**
     * File template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function file(Field $field)
    {
        return $field->name;
    }

    /**
     * Input template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function input(Field $field)
    {
        return $field->name;
    }

    /**
     * Password template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function password(Field $field)
    {
        return $field->name;
    }

    /**
     * Radio template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function radio(Field $field)
    {
        return $field->name;
    }

    /**
     * Select template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function select(Field $field)
    {
        return $field->name;
    }

    /**
     * Textarea template.
     *
     * @param  \Orchestra\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function textarea(Field $field)
    {
        return $field->name;
    }
}
