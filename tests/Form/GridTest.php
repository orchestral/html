<?php

namespace Orchestra\Html\TestCase\Form;

use Mockery as m;
use Orchestra\Html\Form\Grid;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;

class GridTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown()
    {
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
            'select' => [],
            'textarea' => [],
            'input' => [],
            'password' => [],
            'file' => [],
            'radio' => [],
        ];
    }

    /**
     * Test instanceof Orchestra\Html\Form\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app = new Container();

        $config = [
            'submit' => 'Submit',
            'attributes' => ['id' => 'foo'],
            'view' => 'foo',
        ];

        $stub = new Grid($app, $config);

        $stub->attributes = ['class' => 'foobar'];

        $refl = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $submit = $refl->getProperty('submit');
        $view = $refl->getProperty('view');

        $attributes->setAccessible(true);
        $submit->setAccessible(true);
        $view->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Form\Grid', $stub);
        $this->assertEquals('Submit', $submit->getValue($stub));
        $this->assertEquals('foo', $view->getValue($stub));

        $this->assertEquals('foo', $stub->view());
        $this->assertEquals(['id' => 'foo', 'class' => 'foobar'], $attributes->getValue($stub));
    }

    /**
     * Test Orchestra\Html\Form\Grid::row() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $stub = new Grid($this->getContainer(), []);
        $mock = new \Illuminate\Support\Fluent();
        $stub->with($mock);

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $data->setAccessible(true);

        $this->assertEquals($mock, $data->getValue($stub));
        $this->assertEquals($mock, $stub->data());
    }

    /**
     * Test Orchestra\Html\Form\Grid::layout() method.
     *
     * @test
     */
    public function testLayoutMethod()
    {
        $stub = new Grid($this->getContainer(), []);

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
        $stub = new Grid($this->getContainer(), []);

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
     * Test Orchestra\Html\Form\Grid::fieldset() method.
     *
     * @test
     */
    public function testFieldsetMethod()
    {
        $app = new Container();
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');
        $app['Orchestra\Contracts\Html\Form\Template'] = $presenter = m::mock('\Orchestra\Html\Form\BootstrapThreePresenter');

        $config = [
            'templates' => $this->getFieldsetTemplates(),
            'presenter' => 'Orchestra\Html\Form\BootstrapThreePresenter',
        ];

        $control->shouldReceive('setTemplates')->twice()->with($this->getFieldsetTemplates())->andReturnSelf()
            ->shouldReceive('setPresenter')->twice()->with($presenter)->andReturnSelf()
            ->shouldReceive('generate')->twice();

        $stub = new Grid($app, $config);
        $refl = new \ReflectionObject($stub);
        $fieldsets = $refl->getProperty('fieldsets');
        $fieldsets->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Support\Collection', $fieldsets->getValue($stub));
        $this->assertEquals([], $fieldsets->getValue($stub)->toArray());

        $stub->fieldset(function ($f) {
            $f->control('text', 'email');
        });
        $stub->fieldset('Foobar', function ($f) {
            $f->control('text', 'email');
        });

        $fieldset = $fieldsets->getValue($stub);

        $this->assertInstanceOf('\Orchestra\Html\Form\Fieldset', $fieldset[0]);
        $this->assertNull($fieldset[0]->name);
        $this->assertInstanceOf('\Orchestra\Html\Form\Field', $stub->find('email'));

        $this->assertInstanceOf('\Orchestra\Html\Form\Fieldset', $fieldset[1]);
        $this->assertEquals('Foobar', $fieldset[1]->name);
        $this->assertInstanceOf('\Orchestra\Html\Form\Field', $stub->find('foobar.email'));
        $this->assertEquals('email', $stub->find('foobar.email')->name);
    }

    /**
     * Test Orchestra\Html\Form\Grid::hidden() method.
     *
     * @test
     */
    public function testHiddenMethod()
    {
        $stub = new Grid($app = $this->getContainer(), []);
        $app['form'] = $form = m::mock('\Illuminate\Html\FormBuilder');

        $form->shouldReceive('hidden')->once()
                ->with('foo', 'foobar', m::any())->andReturn('hidden_foo')
            ->shouldReceive('hidden')->once()
                ->with('foobar', 'stubbed', m::any())->andReturn('hidden_foobar');

        $stub->with(new \Illuminate\Support\Fluent([
            'foo' => 'foobar',
            'foobar' => 'foo',
        ]));

        $stub->hidden('foo');
        $stub->hidden('foobar', function ($f) {
            $f->value('stubbed');
        });

        $refl = new \ReflectionObject($stub);
        $hiddens = $refl->getProperty('hiddens');
        $hiddens->setAccessible(true);

        $data = $hiddens->getValue($stub);
        $this->assertEquals('hidden_foo', $data['foo']);
        $this->assertEquals('hidden_foobar', $data['foobar']);
    }

    /**
     * Test Orchestra\Html\Form\Grid::find() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFindMethodThrowsException()
    {
        $stub = new Grid($this->getContainer(), []);

        $stub->find('foobar.email');
    }

    /**
     * Test Orchestra\Html\Form\Grid magic method __call() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodCallThrowsException()
    {
        $stub = new Grid($this->getContainer(), []);

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
        $stub = new Grid($this->getContainer(), []);

        $stub->invalidProperty;
    }

    /**
     * Test Orchestra\Html\Form\Grid magic method __set() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsException()
    {
        $stub = new Grid($this->getContainer(), []);

        $stub->invalidProperty = ['foo'];
    }

    /**
     * Test Orchestra\Html\Form\Grid magic method __set() throws
     * exception when $values is not an array.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $stub = new Grid($this->getContainer(), []);

        $stub->attributes = 'foo';
    }

    /**
     * Test Orchestra\Html\Form\Grid::of() method throws exception.
     *
     * @expectedException \RuntimeException
     */
    public function testOfMethodThrowsException()
    {
        $stub = new Grid($this->getContainer(), []);

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
        $stub = new Grid($this->getContainer(), []);

        isset($stub->invalidProperty) ? true : false;
    }

    /**
     * Test Orchestra\Html\Form\Grid::resource() method as POST to create.
     *
     * @test
     */
    public function testResourceMethodAsPost()
    {
        $stub = new Grid($this->getContainer(), []);

        $listener = m::mock('\Orchestra\Contracts\Html\Form\Presenter');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $listener->shouldReceive('handles')->once()
                ->with('orchestra::users')->andReturn('orchestra::users')
            ->shouldReceive('setupForm')->once();

        $stub->resource($listener, 'orchestra::users', $model);
    }

    /**
     * Test Orchestra\Html\Form\Grid::resource() method as PUT to update.
     *
     * @test
     */
    public function testResourceMethodAsPut()
    {
        $stub = new Grid($this->getContainer(), []);

        $listener = m::mock('\Orchestra\Contracts\Html\Form\Presenter');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $model->exists = true;
        $model->shouldReceive('getKey')->once()->andReturn(20);

        $listener->shouldReceive('handles')->once()
                ->with('orchestra::users/20')->andReturn('orchestra::users/20')
            ->shouldReceive('setupForm')->once();

        $stub->resource($listener, 'orchestra::users', $model);
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        return new Container();
    }
}
