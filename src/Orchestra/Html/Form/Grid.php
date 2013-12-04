<?php namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;
use Orchestra\Support\Contracts\FormPresenterInterface as Presenter;

class Grid extends \Orchestra\Html\Abstractable\Grid
{
    /**
     * Enable CSRF token.
     *
     * @var boolean
     */
    public $token = false;

    /**
     * Hidden fields.
     *
     * @var array
     */
    protected $hiddens = array();

    /**
     * List of row in array.
     *
     * @var array
     */
    protected $row = null;

    /**
     * All the fieldsets.
     *
     * @var array
     */
    protected $fieldsets = array();

    /**
     * Set submit button message.
     *
     * @var string
     */
    public $submit = null;

    /**
     * Set the no record message.
     *
     * @var string
     */
    public $format = null;

    /**
     * Selected view path for form layout.
     *
     * @var array
     */
    protected $view = null;

    /**
     * {@inheritdoc}
     */
    protected $definition = array(
        'name'    => null,
        '__call'  => array('fieldsets', 'view', 'hiddens'),
        '__get'   => array('attributes', 'row', 'view', 'hiddens'),
        '__set'   => array('attributes'),
        '__isset' => array('attributes', 'row', 'view', 'hiddens'),
    );

    /**
     * {@inheritdoc}
     */
    protected function initiate()
    {
        $config = $this->app['config']->get('orchestra/html::form', array());

        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->row = array();
    }

    /**
     * Set fieldset layout (view).
     *
     * <code>
     *      // use default horizontal layout
     *      $fieldset->layout('horizontal');
     *
     *      // use default vertical layout
     *      $fieldset->layout('vertical');
     *
     *      // define fieldset using custom view
     *      $fieldset->layout('path.to.view');
     * </code>
     *
     * @param  string   $name
     * @return void
     */
    public function layout($name)
    {
        if (in_array($name, array('horizontal', 'vertical'))) {
            $this->view = "orchestra/html::form.{$name}";
        } else {
            $this->view = $name;
        }
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $table->with(DB::table('users')->get());
     * </code>
     *
     * @param  array|stdClass  $row
     * @return mixed
     */
    public function with($row = null)
    {
        if (is_null($row)) {
            return $this->row;
        }

        is_array($row) and $row = new Fluent($row);

        $this->row = $row;
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * @param  array    $rows
     * @return mixed
     * @see    Grid::with()
     */
    public function row($row = null)
    {
        return $this->with($row);
    }

    /**
     * Create a new Fieldset instance.
     *
     * @param  string   $name
     * @param  \Closure $callback
     * @return Fieldset
     */
    public function fieldset($name, Closure $callback = null)
    {
        return $this->fieldsets[] = new Fieldset($this->app, $name, $callback);
    }

    /**
     * Add hidden field.
     *
     * @param  string   $name
     * @param  \Closure $callback
     * @return void
     */
    public function hidden($name, $callback = null)
    {
        $value = null;

        if (isset($this->row) and isset($this->row->{$name})) {
            $value = $this->row->{$name};
        }

        $field = new Fluent(array(
            'name'       => $name,
            'value'      => $value ?: '',
            'attributes' => array(),
        ));

        if ($callback instanceof Closure) {
            call_user_func($callback, $field);
        }

        $this->hiddens[$name] = $this->app['form']->hidden($name, $field->value, $field->attributes);
    }

    /**
     * Setup form configuration.
     *
     * @param  \Orchestra\Support\Contracts\FormPresenterInterface  $listener
     * @param  string                                               $url
     * @param  \Illuminate\Database\Eloquent\Model                  $model
     * @param  array                                                $attributes
     * @return array
     */
    public function resource(Presenter $listener, $url, Model $model, array $attributes = array())
    {
        $method = 'POST';

        if ($model->exists) {
            $url = "{$url}/{$model->getKey()}";
            $method = 'PUT';
        }

        $attributes['method'] = $method;

        return $this->simple($listener, $url, $model, $attributes);
    }

    /**
     * Setup simple form configuration.
     *
     * @param  \Orchestra\Support\Contracts\FormPresenterInterface  $listener
     * @param  string                                               $url
     * @param  mixed                                                $model
     * @param  array                                                $attributes
     * @return array
     */
    public function simple(Presenter $listener, $url, $model, array $attributes = array())
    {
        $method = array_get($attributes, 'method', 'POST');
        $url    = $listener->handles($url);

        $attributes = array_merge($attributes, compact('url', 'method'));

        $this->with($model);
        $this->attributes($attributes);
        $listener->setupForm($this);

        return $this;
    }
}
