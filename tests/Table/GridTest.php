<?php

namespace Orchestra\Html\Tests\Table;

use Illuminate\Container\Container;
use Illuminate\Support\Fluent;
use Mockery as m;
use Orchestra\Html\Table\Column;
use Orchestra\Html\Table\Grid;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test instanceof Orchestra\Html\Table\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app = new Container();

        $config = [
            'empty' => 'No data',
            'view' => 'foo',
        ];

        $stub = new Grid($app, $config);
        $refl = new \ReflectionObject($stub);
        $empty = $refl->getProperty('empty');
        $data = $refl->getProperty('data');
        $header = $refl->getProperty('header');
        $view = $refl->getProperty('view');

        $empty->setAccessible(true);
        $data->setAccessible(true);
        $header->setAccessible(true);
        $view->setAccessible(true);

        $this->assertInstanceOf('\Orchestra\Html\Table\Grid', $stub);
        $this->assertEquals('No data', $empty->getValue($stub));
        $this->assertEquals('foo', $view->getValue($stub));
        $this->assertEquals([], value($header->getValue($stub)));
        $this->assertEquals([], $data->getValue($stub));
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $stub = new Grid($this->getContainer(), []);

        $mock = [new Fluent()];
        $stub->with($mock, false);

        $refl = new \ReflectionObject($stub);
        $data = $refl->getProperty('data');
        $header = $refl->getProperty('header');
        $model = $refl->getProperty('model');
        $paginate = $refl->getProperty('paginate');

        $data->setAccessible(true);
        $header->setAccessible(true);
        $model->setAccessible(true);
        $paginate->setAccessible(true);

        $this->assertEquals($mock, $stub->data());
        $this->assertEquals($mock, $data->getValue($stub));
        $this->assertEquals([], value($header->getValue($stub)));
        $this->assertEquals($mock, $model->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));
        $this->assertTrue(isset($stub->model));
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Illuminate\Contracts\Pagination\Paginator instance.
     *
     * @test
     */
    public function testWithMethodGivenPaginatorInstance()
    {
        $expected = ['foo'];
        $stub = new Grid($this->getContainer(), []);

        $model = m::mock('\Illuminate\Contracts\Pagination\Paginator');
        $model->shouldReceive('items')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->data());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a paginable
     * instance.
     *
     * @test
     */
    public function testWithMethodGivenModelBuilderInstance()
    {
        $expected = ['foo'];
        $stub = new Grid($this->getContainer(), []);

        $model = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $model->shouldReceive('paginate')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->data());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Illuminate\Contracts\Support\Arrayable instance.
     *
     * @test
     */
    public function testWithMethodGivenArrayableInterfaceInstance()
    {
        $expected = ['foo'];
        $stub = new Grid($this->getContainer(), []);

        $model = m::mock('\Illuminate\Contracts\Support\Arrayable');
        $model->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->data());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Query Builder instance when paginated.
     *
     * @test
     */
    public function testWithMethodGivenQueryBuilderInstanceWhenPaginated()
    {
        $expected = ['foo'];
        $stub = new Grid($this->getContainer(), []);

        $model = m::mock('\Illuminate\Database\Query\Builder');
        $arrayable = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $model->shouldReceive('paginate')->once()->with(25, ['*'], 'page')->andReturn($arrayable);
        $arrayable->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->paginate(25);
        $stub->with($model);

        $this->assertEquals($expected, $stub->data());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method given a
     * Query Builder instance when not paginated.
     *
     * @test
     */
    public function testWithMethodGivenQueryBuilderInstanceWhenNotPaginated()
    {
        $expected = ['foo'];
        $stub = new Grid($this->getContainer(), []);

        $model = m::mock('\Illuminate\Database\Query\Builder');
        $arrayable = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $model->shouldReceive('get')->once()->andReturn($arrayable);
        $arrayable->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->with($model);
        $stub->paginate(null);

        $this->assertEquals($expected, $stub->data());
    }

    /**
     * Test Orchestra\Html\Table\Grid::with() method throws an exceptions
     * when $model can't be converted to array.
     *
     * @test
     */
    public function testWithMethodThrowsAnException()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);

        $model = 'Foo';

        $stub->with($model, false);

        $stub->data();
    }

    /**
     * Test Orchestra\Html\Table\Grid::layout() method.
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
        $this->assertEquals('orchestra/html::table.horizontal', $view->getValue($stub));

        $stub->layout('vertical');
        $this->assertEquals('orchestra/html::table.vertical', $view->getValue($stub));

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
        $me = $this;
        $stub = new Grid($this->getContainer(), []);

        $stub->with([
            new Fluent(['foo1' => 'Foo1']),
        ], false);

        $expected = [
            new Column([
                'id' => 'id',
                'label' => 'Id',
                'value' => function ($row) {
                    return $row->id;
                },
                'headers' => [],
                'attributes' => function ($row) {
                    return [];
                },
            ]),
            new Column([
                'id' => 'foo1',
                'label' => 'Foo1',
                'value' => 'Foo1 value',
                'headers' => [],
                'attributes' => function ($row) {
                    return [];
                },
            ]),
            new Column([
                'id' => 'foo2',
                'label' => 'Foo2',
                'value' => 'Foo2 value',
                'headers' => [],
                'attributes' => function ($row) {
                    return [];
                },
            ]),
        ];

        $stub->column('id');

        $stub->column(function ($c) {
            $c->id('foo1')->label('Foo1');
            $c->value('Foo1 value');
        });

        $stub->column('Foo2', 'foo2')->value('Foo2 value');

        $stub->attributes = ['class' => 'foo'];

        $output = $stub->of('id', function ($fluent) use ($me) {
            $me->assertInstanceOf('\Illuminate\Support\Fluent', $fluent);
        });

        $this->assertEquals('Id', $output->label);
        $this->assertEquals('id', $output->id);
        $this->assertEquals([], call_user_func($output->attributes, new Fluent()));
        $this->assertEquals(5, call_user_func($output->value, new Fluent(['id' => 5])));

        $this->assertEquals(['class' => 'foo'], $stub->attributes);
        $this->assertEquals($expected, $stub->columns());
    }

    /**
     * Test Orchestra\Html\Table\Grid::of() method throws exception.
     *
     * @test
     */
    public function testOfMethodThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);
        $stub->of('id');
    }

    /**
     * Test Orchestra\Html\Table\Grid::paginate() method.
     *
     * @test
     */
    public function testPaginateMethod()
    {
        $stub = new Grid($this->getContainer(), []);
        $refl = new \ReflectionObject($stub);

        $perPage = $refl->getProperty('perPage');
        $paginate = $refl->getProperty('paginate');

        $perPage->setAccessible(true);
        $paginate->setAccessible(true);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(25);

        $this->assertEquals(25, $perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(2.5);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(-10);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(true);

        $this->assertNull($perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(false);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(1);

        $this->assertEquals(1, $perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(0);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));
    }

    /**
     * Test Orchestra\Html\Table\Grid::searchable() method.
     *
     * @test
     */
    public function testSearchableMethod()
    {
        $attributes = ['email', 'fullname'];
        $app = $this->getContainer();

        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('q')->andReturn('orchestra*')
            ->shouldReceive('merge')->once()->with(m::type('Array'))->andReturnNull();

        $stub = new Grid($app, []);

        $model = m::mock('\Illuminate\Database\Query\Builder');

        $model->shouldReceive('getConnection->getDriverName')->andReturn('mysql');
        $model->shouldReceive('where')->once()->with(m::type('Closure'))
                ->andReturnUsing(static function ($c) use ($model) {
                    $c($model);
                })
            ->shouldReceive('orWhere')->twice()->with(m::type('Closure'))
                ->andReturnUsing(static function ($c) use ($model) {
                    $c($model);
                })
            ->shouldReceive('orWhere')->once()->with('email', 'like', 'orchestra%')
            ->shouldReceive('orWhere')->once()->with('fullname', 'like', 'orchestra%');

        $stub->with($model);

        $this->assertNull($stub->searchable($attributes));

        $this->assertEquals($attributes, $stub->get('search.attributes'));
        $this->assertEquals('q', $stub->get('search.key'));
        $this->assertEquals('orchestra*', $stub->get('search.value'));
    }

    /**
     * Test Orchestra\Html\Table\Grid::searchable() method
     * throws exception when model is not a query builder.
     *
     * @test
     */
    public function testSearchableMethodThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $attributes = ['email', 'fullname'];

        $stub = new Grid($this->getContainer(), []);

        $stub->with('Foo');

        $stub->searchable($attributes);
    }

    /**
     * Test Orchestra\Html\Table\Grid::sortable() method.
     *
     * @test
     */
    public function testSortableMethod()
    {
        $app = $this->getContainer();
        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('order_by')->andReturn('email')
            ->shouldReceive('input')->once()->with('direction')->andReturn('desc');

        $stub = new Grid($app, []);

        $model = m::mock('\Illuminate\Database\Query\Builder');

        $model->shouldReceive('orderBy')->once()->with('email', 'DESC')->andReturnSelf();

        $stub->with($model);

        $this->assertNull($stub->sortable(['only' => ['email'], 'except' => ['fullname']], 'order_by', 'direction'));

        $this->assertEquals(['key' => 'order_by', 'value' => 'email'], $stub->get('filter.order_by'));
        $this->assertEquals(['key' => 'direction', 'value' => 'desc'], $stub->get('filter.direction'));
        $this->assertEquals(['only' => ['email'], 'except' => ['fullname']], $stub->get('filter.columns'));
    }

    /**
     * Test Orchestra\Html\Table\Grid::sortable() method
     * throws exception when model is not a query builder.
     *
     * @test
     */
    public function testSortableMethodThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $attributes = ['email', 'fullname'];

        $stub = new Grid($this->getContainer(), []);

        $stub->with('Foo');

        $stub->sortable($attributes);
    }

    /**
     * Test Orchestra\Html\Table\Grid::attributes() method.
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
     * Test Orchestra\Html\Table\Grid magic method definitions.
     *
     * @test
     */
    public function testMagicMethodDefinitions()
    {
        $app = new Container();

        $stub = new Grid($app, []);
        $refl = new \ReflectionObject($stub);
        $pageName = $refl->getProperty('pageName');

        $pageName->setAccessible(true);

        $this->assertEquals('page', $pageName->getValue($stub));

        $stub->pageName = 'foo';

        $this->assertEquals('foo', $pageName->getValue($stub));
        $this->assertEquals('foo', $stub->pageName);
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __call() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodCallThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);

        $stub->invalidMethod();
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __get() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodGetThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);

        $stub->invalidProperty;
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __set() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodSetThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);

        $stub->invalidProperty = ['foo'];
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __set() throws
     * exception when $values is not an array.
     *
     * @test
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);

        $stub->attributes = 'foo';
    }

    /**
     * Test Orchestra\Html\Table\Grid magic method __isset() throws
     * exception.
     *
     * @test
     */
    public function testMagicMethodIssetThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $stub = new Grid($this->getContainer(), []);

        isset($stub->invalidProperty) ? true : false;
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
