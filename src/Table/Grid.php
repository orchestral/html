<?php

namespace Orchestra\Html\Table;

use Closure;
use InvalidArgumentException;
use Orchestra\Html\Grid as BaseGrid;
use Illuminate\Contracts\Support\Arrayable;
use Orchestra\Support\Concerns\QueryFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Orchestra\Contracts\Html\Table\Grid as GridContract;

class Grid extends BaseGrid implements GridContract
{
    use QueryFilter;

    /**
     * All the columns.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Set the no record message.
     *
     * @var string
     */
    public $empty;

    /**
     * Eloquent model used for table.
     *
     * @var mixed
     */
    protected $model = [];

    /**
     * List of rows in array, is used when model is null.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Grid headers attributes resolver.
     *
     * @var \Closure|null
     */
    protected $header;

    /**
     * Enable to attach pagination during rendering.
     *
     * @var bool
     */
    protected $paginate = false;

    /**
     * The number of models/entities to return for pagination.
     *
     * @var int|null
     */
    protected $perPage;

    /**
     * The page name for pagination.
     *
     * @var string
     */
    protected $pageName = 'page';

    /**
     * {@inheritdoc}
     */
    protected $definition = [
        'name' => 'columns',
        '__call' => ['columns', 'view'],
        '__get' => ['attributes', 'model', 'paginate', 'pageName', 'viewData'],
        '__set' => ['attributes', 'pageName'],
        '__isset' => ['attributes', 'model', 'paginate', 'pageName'],
    ];

    /**
     * Load grid configuration.
     *
     * @param  array  $config
     *
     * @return void
     */
    public function initiate(array $config): void
    {
        foreach ($config as $key => $value) {
            if (\property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->header(function () {
            return [];
        });
    }

    /**
     * Set table layout (view).
     *
     * <code>
     *      // use default horizontal layout
     *      $table->layout('horizontal');
     *
     *      // use default vertical layout
     *      $table->layout('vertical');
     *
     *      // define table using custom view
     *      $table->layout('path.to.view');
     * </code>
     *
     * @param  string  $name
     * @param  array   $data
     *
     * @return $this
     */
    public function layout(string $name, array $data = [])
    {
        if (\in_array($name, ['horizontal', 'vertical'])) {
            $this->view = "orchestra/html::table.{$name}";
        } else {
            $this->view = $name;
        }

        $this->viewData = $data;

        return $this;
    }

    /**
     * Attach Eloquent as row and allow pagination (if required).
     *
     * <code>
     *      // add model without pagination
     *      $table->with(User::all(), false);
     *
     *      // add model with pagination
     *      $table->with(User::paginate(30), true);
     * </code>
     *
     * @param  mixed  $model
     * @param  bool   $paginate
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function with($model, bool $paginate = true)
    {
        $this->model = $model;
        $this->paginate = $paginate;

        return $this;
    }

    /**
     * Get whether current setup is paginated.
     *
     * @return bool
     */
    public function paginated(): bool
    {
        return $this->paginate;
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $table->rows(DB::table('users')->get());
     * </code>
     *
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $data
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function rows($data)
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $this->setRowsData($data);

        return $this;
    }

    /**
     * Get raw data.
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function data(): array
    {
        if (empty($this->data) && ! empty($this->model)) {
            $this->buildRowsFromModel($this->model);
        }

        return $this->data;
    }

    /**
     * Add or append grid header attributes.
     *
     * @param  \Closure|null  $callback
     *
     * @return \Closure|array|null
     */
    public function header(Closure $callback = null)
    {
        if (\is_null($callback)) {
            return $this->header;
        }

        $this->header = $callback;

        return null;
    }

    /**
     * Append a new column to the table.
     *
     * <code>
     *      // add a new column using just field name
     *      $table->column('username');
     *
     *      // add a new column using a label (header title) and field name
     *      $table->column('User Name', 'username');
     *
     *      // add a new column by using a field name and closure
     *      $table->column('fullname', function ($column)
     *      {
     *          $column->label = 'User Name';
     *          $column->value = function ($row) {
     *              return $row->first_name.' '.$row->last_name;
     *          };
     *
     *          $column->attributes(function ($row) {
     *              return array('data-id' => $row->id);
     *          });
     *      });
     * </code>
     *
     * @param  mixed  $name
     * @param  mixed|null  $callback
     *
     * @return \Orchestra\Contracts\Html\Table\Column
     */
    public function column($name, $callback = null)
    {
        list($name, $column) = $this->buildColumn($name, $callback);

        $this->columns[] = $column;
        $this->keyMap[$name] = \count($this->columns) - 1;

        return $column;
    }

    /**
     * Find definition that match the given id.
     *
     * @param  string  $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Orchestra\Contracts\Html\Table\Column
     */
    public function find(string $name): Column
    {
        if (! \array_key_exists($name, $this->keyMap)) {
            throw new InvalidArgumentException("Name [{$name}] is not available.");
        }

        return $this->columns[$this->keyMap[$name]];
    }

    /**
     * Setup pagination.
     *
     * @param  bool|int|null  $perPage
     *
     * @return $this
     */
    public function paginate($perPage)
    {
        if (\filter_var($perPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) && ! \is_bool($perPage)) {
            $this->perPage = $perPage;
            $this->paginate = true;
        } elseif (\filter_var($perPage, FILTER_VALIDATE_BOOLEAN)) {
            $this->perPage = null;
            $this->paginate = $perPage;
        } else {
            $this->perPage = null;
            $this->paginate = false;
        }

        return $this;
    }

    /**
     * Execute searchable filter on model instance.
     *
     * @param  array   $attributes
     * @param  string  $key
     *
     * @return void
     */
    public function searchable(array $attributes, string $key = 'q'): void
    {
        $model = $this->resolveQueryBuilderFromModel($this->model);
        $request = $this->app->make('request');
        $value = $request->input($key);

        $request->merge(["{$key}" => \rawurlencode($value)]);

        $this->set('search', [
            'attributes' => $attributes,
            'key' => $key,
            'value' => $value,
        ]);

        $this->model = $this->setupWildcardQueryFilter($model, $value, $attributes);
    }

    /**
     * Execute sortable query filter on model instance.
     *
     * @param  array   $orderColumns
     * @param  string  $orderByKey
     * @param  string  $directionKey
     *
     * @return void
     */
    public function sortable(
        array $orderColumns = [],
        string $orderByKey = 'order_by',
        string $directionKey = 'direction'
    ): void {
        $model = $this->resolveQueryBuilderFromModel($this->model);
        $request = $this->app->make('request');

        $orderByValue = $request->input($orderByKey);
        $directionValue = $request->input($directionKey);

        $this->set('filter.order_by', ['key' => $orderByKey, 'value' => $orderByValue]);
        $this->set('filter.direction', ['key' => $directionKey, 'value' => $directionValue]);
        $this->set('filter.columns', $orderColumns);

        $this->model = $this->setupBasicQueryFilter($model, [
            'order_by' => $orderByValue,
            'direction' => $directionValue,
            'columns' => $orderColumns,
        ]);
    }

    /**
     * Build control.
     *
     * @param  mixed  $name
     * @param  mixed  $callback
     *
     * @return array
     */
    protected function buildColumn($name, $callback = null): array
    {
        list($label, $name, $callback) = $this->buildFluentAttributes($name, $callback);

        $value = '';

        if (! empty($name)) {
            $value = function ($row) use ($name) {
                return \data_get($row, $name);
            };
        }

        $column = new Column([
            'id' => $name,
            'label' => $label,
            'value' => $value,
            'headers' => [],
            'attributes' => function ($row) {
                return [];
            },
        ]);

        if (\is_callable($callback)) {
            $callback($column);
        }

        return [$name, $column];
    }

    /**
     * Convert the model to Paginator when available or convert it
     * to a collection.
     *
     * @param  mixed  $model
     *
     * @return \Illuminate\Contracts\Support\Arrayable|array
     */
    protected function buildModel($model)
    {
        try {
            $query = $this->resolveQueryBuilderFromModel($model);
        } catch (InvalidArgumentException $e) {
            $query = $model;
        }

        if ($this->paginate === true && \method_exists($query, 'paginate')) {
            $query = $query->paginate($this->perPage, ['*'], $this->pageName);
        } elseif ($this->isQueryBuilder($query)) {
            $query = $query->get();
        }

        return $query;
    }

    /**
     * Get rows from model instance.
     *
     * @param  object  $model
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function buildRowsFromModel($model): void
    {
        $this->model = $model = $this->buildModel($model);

        if ($model instanceof Paginator) {
            $this->setRowsData($model->items());
            $this->paginate = true;
        } elseif ($model instanceof Collection) {
            $this->setRowsData($model->all());
        } elseif ($model instanceof Arrayable) {
            $this->setRowsData($model->toArray());
        } elseif (\is_array($model)) {
            $this->setRowsData($model);
        } else {
            throw new InvalidArgumentException('Unable to convert $model to array.');
        }
    }

    /**
     * Set rows data.
     *
     * @param  array  $data
     *
     * @return array
     */
    protected function setRowsData(array $data = []): array
    {
        return $this->data = $data;
    }

    /**
     * Resolve query builder from model instance.
     *
     * @param  mixed  $model
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function resolveQueryBuilderFromModel($model)
    {
        if ($this->isEloquentModel($model)) {
            return $model->newQuery();
        } elseif ($this->isEloquentRelationModel($model)) {
            return $model->getQuery();
        } elseif (! $this->isQueryBuilder($model)) {
            throw new InvalidArgumentException('Unable to load Query Builder from $model');
        }

        return $model;
    }

    /**
     * Check if given $model is a query builder.
     *
     * @param  mixed  $model
     *
     * @return bool
     */
    protected function isQueryBuilder($model): bool
    {
        return $model instanceof \Illuminate\Database\Query\Builder
                    || $model instanceof \Illuminate\Database\Eloquent\Builder;
    }

    /**
     * Check if given $model is a Model instance.
     *
     * @param mixed $model
     *
     * @return bool
     */
    protected function isEloquentModel($model): bool
    {
        return $model instanceof \Illuminate\Database\Eloquent\Model;
    }

    /**
     * Check if given $model is a Model instance.
     *
     * @param mixed $model
     *
     * @return bool
     */
    protected function isEloquentRelationModel($model): bool
    {
        return $model instanceof \Illuminate\Database\Eloquent\Relations\Relation;
    }
}
