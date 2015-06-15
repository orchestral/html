<?php namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Orchestra\Support\Collection;
use Orchestra\Html\Grid as BaseGrid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Config\Repository;
use Orchestra\Contracts\Html\Form\Presenter;
use Orchestra\Contracts\Html\Form\Grid as GridContract;

class Grid extends BaseGrid implements GridContract
{
    /**
     * Enable CSRF token.
     *
     * @var bool
     */
    public $token = false;

    /**
     * Hidden fields.
     *
     * @var array
     */
    protected $hiddens = [];

    /**
     * List of row in array.
     *
     * @var array
     */
    protected $row = null;

    /**
     * All the fieldsets.
     *
     * @var \Orchestra\Support\Collection
     */
    protected $fieldsets;

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
    protected $definition = [
        'name'    => null,
        '__call'  => ['fieldsets', 'view', 'hiddens'],
        '__get'   => ['attributes', 'row', 'view', 'hiddens'],
        '__set'   => ['attributes'],
        '__isset' => ['attributes', 'row', 'view', 'hiddens'],
    ];

    /**
     * Load grid configuration.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     *
     * @return void
     */
    public function initiate(Repository $config)
    {
        $this->fieldsets = new Collection();

        foreach ($config->get('orchestra/html::form', []) as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->row = [];
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
     * @param  string  $name
     *
     * @return $this
     */
    public function layout($name)
    {
        if (in_array($name, ['horizontal', 'vertical'])) {
            $this->view = "orchestra/html::form.{$name}";
        } else {
            $this->view = $name;
        }

        return $this;
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $form->with(DB::table('users')->get());
     * </code>
     *
     * @param  array|\stdClass|\Illuminate\Database\Eloquent\Model  $row
     *
     * @return mixed
     */
    public function with($row = null)
    {
        is_array($row) && $row = new Fluent($row);

        if (! is_null($row)) {
            $this->row = $row;
        }

        return $this->row;
    }

    /**
     * Attach rows data instead of assigning a model.
     *
     * @param  array  $row
     *
     * @return mixed
     *
     * @see    $this->with()
     */
    public function row($row = null)
    {
        return $this->with($row);
    }

    /**
     * Create a new Fieldset instance.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return \Orchestra\Html\Form\Fieldset
     */
    public function fieldset($name, Closure $callback = null)
    {
        $fieldset = new Fieldset($this->app, $name, $callback);

        if (is_null($name = $fieldset->getName())) {
            $name = sprintf('fieldset-%d', $this->fieldsets->count());
        } else {
            $name = Str::slug($name);
        }

        $this->keyMap[$name] = $fieldset;

        $this->fieldsets->push($fieldset);

        return $fieldset;
    }

    /**
     * Find control that match the given id.
     *
     * @param  string  $name
     *
     * @return \Orchestra\Html\Form\Field|null
     *
     * @throws \InvalidArgumentException
     */
    public function find($name)
    {
        if (Str::contains($name, '.')) {
            list($fieldset, $control) = explode('.', $name, 2);
        } else {
            $fieldset = 'fieldset-0';
            $control  = $name;
        }

        if (! array_key_exists($fieldset, $this->keyMap)) {
            throw new InvalidArgumentException("Name [{$name}] is not available.");
        }

        return $this->keyMap[$fieldset]->of($control);
    }

    /**
     * Add hidden field.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function hidden($name, $callback = null)
    {
        $value = data_get($this->row, $name);

        $field = new Fluent([
            'name'       => $name,
            'value'      => $value ?: '',
            'attributes' => [],
        ]);

        if ($callback instanceof Closure) {
            call_user_func($callback, $field);
        }

        $this->hiddens[$name] = $this->app->make('form')->hidden($name, $field->get('value'), $field->get('attributes'));
    }

    /**
     * Setup form configuration.
     *
     * @param  \Orchestra\Contracts\Html\Form\Presenter  $listener
     * @param  string  $url
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $attributes
     *
     * @return $this
     */
    public function resource(Presenter $listener, $url, Model $model, array $attributes = [])
    {
        $method = 'POST';

        if ($model->exists) {
            $url    = "{$url}/{$model->getKey()}";
            $method = 'PUT';
        }

        $attributes['method'] = $method;

        return $this->setup($listener, $url, $model, $attributes);
    }

    /**
     * Setup simple form configuration.
     *
     * @param  \Orchestra\Contracts\Html\Form\Presenter  $listener
     * @param  string  $url
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $attributes
     *
     * @return $this
     */
    public function setup(Presenter $listener, $url, $model, array $attributes = [])
    {
        $method = Arr::get($attributes, 'method', 'POST');
        $url    = $listener->handles($url);

        $attributes = array_merge($attributes, [
            'url'    => $url,
            'method' => $method,
        ]);

        $this->with($model);
        $this->attributes($attributes);
        $listener->setupForm($this);

        return $this;
    }
}
