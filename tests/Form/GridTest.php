<?php namespace Orchestra\Html\Form\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Html\Form\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
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
        $app = new Container;
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

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
        $stub = new Grid($this->getContainer());
        $mock = new \Illuminate\Support\Fluent;
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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $app = new Container;
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Orchestra\Contracts\Html\Form\Control'] = $control = m::mock('\Orchestra\Html\Form\Control');

        $config->shouldReceive('get')->twice()
                ->with('orchestra/html::form.fieldset', array())->andReturn($this->getFieldsetTemplates())
            ->shouldReceive('get')->once()
                ->with('orchestra/html::form', array())->andReturn(array(
                    'fieldset' => $this->getFieldsetTemplates(),
                    'template' => $this->getTemplateConfig(),
                ));
        $control->shouldReceive('setTemplates')->twice()->with($this->getFieldsetTemplates())->andReturn(null)
            ->shouldReceive('generate')->twice();

        $stub      = new Grid($app);
        $refl      = new \ReflectionObject($stub);
        $fieldsets = $refl->getProperty('fieldsets');
        $fieldsets->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Support\Collection', $fieldsets->getValue($stub));
        $this->assertEquals(array(), $fieldsets->getValue($stub)->toArray());

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
        $stub = new Grid($app = $this->getContainer());
        $app['form'] = $form = m::mock('\Illuminate\Html\FormBuilder');

        $form->shouldReceive('hidden')->once()
                ->with('foo', 'foobar', m::any())->andReturn('hidden_foo')
            ->shouldReceive('hidden')->once()
                ->with('foobar', 'stubbed', m::any())->andReturn('hidden_foobar');

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
     * Test Orchestra\Html\Form\Grid::find() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFindMethodThrowsException()
    {
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

        $stub->attributes = 'foo';
    }

    /**
     * Test Orchestra\Html\Form\Grid::of() method throws exception.
     *
     * @expectedException \RuntimeException
     */
    public function testOfMethodThrowsException()
    {
        $stub = new Grid($this->getContainer());

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
        $stub = new Grid($this->getContainer());

        isset($stub->invalidProperty) ? true : false;
    }

    /**
     * Test Orchestra\Html\Form\Grid::resource() method as POST to create.
     *
     * @test
     */
    public function testResourceMethodAsPost()
    {
        $stub = new Grid($this->getContainer());

        $listener = m::mock('\Orchestra\Html\Form\PresenterInterface');
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
        $stub = new Grid($this->getContainer());

        $listener = m::mock('\Orchestra\Html\Form\PresenterInterface');
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
        $app = new Container;
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('orchestra/html::form', array())->andReturn(array());

        return $app;
    }
}
