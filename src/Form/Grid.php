<?php

namespace Orchestra\Html\Form;

use Closure;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Orchestra\Support\Collection;
use Orchestra\Html\Grid as BaseGrid;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Contracts\Html\Form\Presenter;
use Orchestra\Contracts\Html\Form\Grid as GridContract;
use Orchestra\Contracts\Html\Form\Fieldset as FieldsetContract;

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
     * List of data in array.
     *
     * @var \Illuminate\Support\Fluent|null
     */
    protected $data;

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
    public $submit;

    /**
     * Set the no record message.
     *
     * @var string
     */
    public $format;

    /**
     * Templates collection.
     *
     * @var array
     */
    protected $templates = [];

    /**
     * {@inheritdoc}
     */
    protected $definition = [
        'name' => null,
        '__call' => ['fieldsets', 'view', 'hiddens'],
        '__get' => ['attributes', 'viewData'],
        '__set' => ['attributes'],
        '__isset' => ['attributes'],
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
        $this->fieldsets = new Collection();

        foreach ($config as $key => $value) {
            if (\property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->data = new Fluent();
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
     * @param  array   $data
     *
     * @return $this
     */
    public function layout(string $name, array $data = [])
    {
        if (\in_array($name, ['horizontal', 'vertical'])) {
            $this->view = "orchestra/html::form.{$name}";
        } else {
            $this->view = $name;
        }

        $this->viewData = $data;

        return $this;
    }

    /**
     * Attach data.
     *
     * <code>
     *      // assign a data
     *      $form->with(DB::table('users')->get());
     * </code>
     *
     * @param  array|\stdClass|\Illuminate\Database\Eloquent\Model  $data
     *
     * @return mixed
     */
    public function with($data)
    {
        if (\is_array($data)) {
            $data = new Fluent($data);
        }

        $this->data = $data;

        return $this;
    }

    /**
     * Get raw data.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Create a new Fieldset instance.
     *
     * @param  string|\Closure  $name
     * @param  \Closure|null  $callback
     *
     * @return \Orchestra\Contracts\Html\Form\Fieldset
     */
    public function fieldset($name, Closure $callback = null): FieldsetContract
    {
        $fieldset = new Fieldset($this->app, $this->templates, $name, $callback);

        if (\is_null($name = $fieldset->getName())) {
            $name = \sprintf('fieldset-%d', $this->fieldsets->count());
        } else {
            $name = Str::slug($name);
        }

        $this->keyMap[$name] = $fieldset;

        $this->fieldsets->push($fieldset);

        return $fieldset;
    }

    /**
     * Find definition that match the given id.
     *
     * @param  string  $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Orchestra\Html\Form\Field
     */
    public function find(string $name): Field
    {
        if (Str::contains($name, '.')) {
            list($fieldset, $control) = \explode('.', $name, 2);
        } else {
            $fieldset = 'fieldset-0';
            $control = $name;
        }

        if (! \array_key_exists($fieldset, $this->keyMap)) {
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
    public function hidden(string $name, $callback = null): void
    {
        $value = \data_get($this->data, $name);

        $field = new Fluent([
            'name' => $name,
            'value' => $value ?: '',
            'attributes' => [],
        ]);

        if ($callback instanceof Closure) {
            $callback($field);
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
            $url = "{$url}/{$model->getKey()}";
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
        $attributes = \array_merge($attributes, [
            'url' => $listener->handles($url),
            'method' => $attributes['method'] ?? 'POST',
        ]);

        $this->with($model);
        $this->attributes($attributes);
        $listener->setupForm($this);

        return $this;
    }
}
