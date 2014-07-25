<?php namespace Orchestra\Html\Table;

use InvalidArgumentException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Fluent;
use Illuminate\Support\Contracts\ArrayableInterface;

class Grid extends \Orchestra\Html\Abstractable\Grid
{
    /**
     * List of rows in array, is used when model is null.
     *
     * @var array
     */
    protected $rows = null;

    /**
     * Eloquent model used for table.
     *
     * @var mixed
     */
    protected $model = null;

    /**
     * All the columns.
     *
     * @var array
     */
    protected $columns = array();

    /**
     * Enable to attach pagination during rendering.
     *
     * @var boolean
     */
    protected $paginate = false;

    /**
     * The number of models/entities to return for pagination.
     *
     * @var int|null
     */
    protected $perPage;

    /**
     * Set the no record message.
     *
     * @var string
     */
    public $empty = null;

    /**
     * Selected view path for table layout.
     *
     * @var array
     */
    protected $view = null;

    /**
     * {@inheritdoc}
     */
    protected $definition = array(
        'name'    => 'columns',
        '__call'  => array('columns', 'view'),
        '__get'   => array('attributes', 'columns', 'model', 'paginate', 'view', 'rows'),
        '__set'   => array('attributes'),
        '__isset' => array('attributes', 'columns', 'model', 'paginate', 'view'),
    );

    /**
     * {@inheritdoc}
     */
    protected function initiate()
    {
        $config = $this->app['config']->get('orchestra/html::table', array());

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->rows = new Fluent(array(
            'data'       => array(),
            'attributes' => function () {
                return array();
            },
        ));
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
     * @param  mixed   $model
     * @param  boolean $paginate
     * @return void
     */
    public function with($model, $paginate = true)
    {
        $this->model    = $model;
        $this->paginate = $paginate;
    }

    /**
     * Get rows collection.
     *
     * @return array
     */
    protected function query()
    {
        if (empty($this->rows->data)) {
            $this->buildRowsFromModel($this->model);
        }

        return $this->rows->data;
    }

    /**
     * Get rows from model instance.
     *
     * @param  $model
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function buildRowsFromModel($model)
    {
        if ($model instanceof Paginator) {
            $this->setRowsData($model->getItems());
            $this->paginate = true;
        } elseif ($this->paginate === true && method_exists($model, 'paginate')) {
            $this->setRowsData($model->paginate());
        } elseif ($model instanceof ArrayableInterface) {
            $this->setRowsData($model->toArray());
        } elseif (is_array($model)) {
            $this->setRowsData($model);
        } else {
            throw new InvalidArgumentException("Unable to convert \$model to array.");
        }
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
     * @param  string   $name
     * @return void
     */
    public function layout($name)
    {
        if (in_array($name, array('horizontal', 'vertical'))) {
            $this->view = "orchestra/html::table.{$name}";
        } else {
            $this->view = $name;
        }
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $table->rows(DB::table('users')->get());
     * </code>
     *
     * @param  array    $rows
     * @return array
     * @throws \InvalidArgumentException
     */
    public function rows(array $rows = null)
    {
        if (is_null($rows)) {
            return $this->query();
        }

        $this->setRowsData($rows);
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
     * @param  mixed    $label
     * @param  mixed    $callback
     * @return \Illuminate\Support\Fluent
     */
    public function column($name, $callback = null)
    {
        list($name, $column) = $this->buildColumn($name, $callback);

        $this->columns[]      = $column;
        $this->keyMap[$name] = count($this->columns) - 1;

        return $column;
    }

    /**
     * Build control.
     *
     * @param  mixed    $name
     * @param  mixed    $callback
     */
    protected function buildColumn($name, $callback = null)
    {
        list($label, $name, $callback) = $this->buildFluentAttributes($name, $callback);

        if (! empty($name)) {
            $value = function ($row) use ($name) {
                return data_get($row, $name);
            };
        } else {
            $value = '';
        }

        $column = new Column(array(
            'id'         => $name,
            'label'      => $label,
            'value'      => $value,
            'headers'    => array(),
            'attributes' => function ($row) {
                return array();
            },
        ));

        if (is_callable($callback)) {
            call_user_func($callback, $column);
        }

        return array($name, $column);
    }

    /**
     * Set rows data.
     *
     * @param  array $rows
     * @return void
     */
    protected function setRowsData(array $rows = array())
    {
        $this->rows->data = $rows;
    }
}
