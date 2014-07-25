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
    protected $perPage = 10;


    /**
     * Enable sorting of the table
     *
     * @var array
     */
    protected $sortable = array();

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
        '__call'  => array('columns', 'view', 'sortable'),
        '__get'   => array('attributes', 'sortable', 'columns', 'model', 'paginate', 'view', 'rows', 'searchable'),
        '__set'   => array('attributes', 'perPage'),
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
     * Set sortable columns
     *
     * <code>
     *      $table->sortable(['title', 'description', ...]);
     * </code>
     *
     * @param array $sortable
     */
    public function sortable(array $sortable)
	{
		$this->sortable = $sortable;
	}

    public function paginate($amount)
    {
        // check if row-data is already set, because if it is,
        // we cant paginate..
        if(isset($this->rows->data) && count($this->rows->data) > 0)
        {
            throw new \LogicException("Paginate() should be called before with()");
        }
        $this->paginate = true;
        $this->perPage = $amount;
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
    public function with($model)
    {
        $this->model = $model;

		if($this->paginate == true )
        {
             $model->paginate($this->perPage);
            $this->rows($model->toArray());
		}
        else
        {
            $model->get();
           $model->toArray();
            $this->rows($model->toArray());
        }

       if ($model instanceof ArrayableInterface) {
            $this->rows($model->toArray());
        } elseif (is_array($model)) {
            $this->rows($model);
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
     * @return void
     */
    public function rows(array $rows = null)
    {
        if (is_null($rows)) {

            return $this->rows->data;
        }

        $this->rows->data = $rows;
       ;
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
     * @return array    array
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
}
