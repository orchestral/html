<?php

namespace Orchestra\Html;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Orchestra\Support\Str;
use RuntimeException;

abstract class Grid
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Grid attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Key map for column overwriting.
     *
     * @var array
     */
    protected $keyMap = [];

    /**
     * Meta attributes.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Selected view path for layout.
     *
     * @var string
     */
    protected $view;

    /**
     * List of view data.
     *
     * @var array
     */
    protected $viewData = [];

    /**
     * Grid Definition.
     *
     * @var array
     */
    protected $definition = [
        'name' => null,
        '__call' => [],
        '__get' => [],
        '__set' => ['attributes'],
        '__isset' => [],
    ];

    /**
     * Create a new Grid instance.
     */
    public function __construct(Container $app, array $config)
    {
        $this->app = $app;

        if (\method_exists($this, 'initiate')) {
            $app->call([$this, 'initiate'], ['config' => $config]);
        }
    }

    /**
     * Add or append Grid attributes.
     *
     * @param  string|array|null  $key
     * @param  mixed  $value
     *
     * @return array|null
     */
    public function attributes($key = null, $value = null)
    {
        if (\is_null($key)) {
            return $this->attributes;
        }

        if (\is_array($key)) {
            $this->attributes = \array_merge($this->attributes, $key);
        } else {
            $this->attributes[$key] = $value;
        }

        return null;
    }

    /**
     * Allow column overwriting.
     *
     * @param  mixed|null  $callback
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return \Illuminate\Support\Fluent
     */
    public function of(string $name, $callback = null)
    {
        $type = $this->definition['name'];

        if (\is_null($type) || ! \property_exists($this, $type)) {
            throw new RuntimeException('Not supported.');
        } elseif (! isset($this->keyMap[$name])) {
            throw new InvalidArgumentException("Name [{$name}] is not available.");
        }

        $id = $this->keyMap[$name];

        if (\is_callable($callback)) {
            \call_user_func($callback, $this->{$type}[$id]);
        }

        return $this->{$type}[$id];
    }

    /**
     * Forget meta value.
     */
    public function forget(string $key): void
    {
        Arr::forget($this->meta, $key);
    }

    /**
     * Get meta value.
     *
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->meta, $key, $default);
    }

    /**
     * Set meta value.
     *
     * @param  mixed  $value
     *
     * @return array
     */
    public function set(string $key, $value)
    {
        return Arr::set($this->meta, $key, $value);
    }

    /**
     * Find definition that match the given id.
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    abstract public function find(string $name);

    /**
     * Build basic name, label and callback option.
     *
     * @param  mixed  $name
     * @param  mixed  $callback
     */
    protected function buildFluentAttributes($name, $callback = null): array
    {
        $label = $name;

        if (! \is_string($label)) {
            $callback = $label;
            $name = '';
            $label = '';
        } elseif (\is_string($callback)) {
            $name = Str::lower($callback);
            $callback = null;
        } else {
            $name = Str::lower($name);
            $label = Str::humanize($name);
        }

        return [$label, $name, $callback];
    }

    /**
     * Magic Method for calling the methods.
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        unset($parameters);

        if (! \in_array($method, $this->definition['__call'])) {
            throw new InvalidArgumentException("Unable to use __call for [{$method}].");
        }

        return $this->$method;
    }

    /**
     * Magic Method for handling dynamic data access.
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if (! \in_array($key, $this->definition['__get'])) {
            throw new InvalidArgumentException("Unable to use __get for [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * Magic Method for handling the dynamic setting of data.
     *
     * @param  array   $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function __set(string $key, $parameters): void
    {
        if (! \in_array($key, $this->definition['__set'])) {
            throw new InvalidArgumentException("Unable to use __set for [{$key}].");
        }

        if ($key !== 'attributes') {
            $this->{$key} = $parameters;

            return;
        }

        if (! \is_array($parameters)) {
            throw new InvalidArgumentException('Require values to be an array.');
        }

        $this->attributes($parameters, null);
    }

    /**
     * Magic Method for checking dynamically-set data.
     *
     * @throws \InvalidArgumentException
     */
    public function __isset(string $key): bool
    {
        if (! \in_array($key, $this->definition['__isset'])) {
            throw new InvalidArgumentException("Unable to use __isset for [{$key}].");
        }

        return isset($this->{$key});
    }
}
