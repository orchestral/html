<?php namespace Orchestra\Html\Abstractable;

use InvalidArgumentException;
use RuntimeException;
use Illuminate\Container\Container;
use Orchestra\Support\Str;

abstract class Grid
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Grid attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Key map for column overwriting.
     *
     * @var array
     */
    protected $keyMap = array();

    /**
     * Grid Definition.
     *
     * @var array
     */
    protected $definition = array(
        'name'    => null,
        '__call'  => array(),
        '__get'   => array(),
        '__set'   => array('attributes'),
        '__isset' => array(),
    );

    /**
     * Create a new Grid instance.
     *
     * @param  \Illuminate\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->initiate();
    }

    /**
     * Load grid configuration.
     *
     * @return void
     */
    abstract protected function initiate();

    /**
     * Add or append Grid attributes.
     *
     * @param  mixed    $key
     * @param  mixed    $value
     * @return array|null
     */
    public function attributes($key = null, $value = null)
    {
        if (is_null($key)) {
            return $this->attributes;
        } elseif (is_array($key)) {
            $this->attributes = array_merge($this->attributes, $key);
        } else {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Allow column overwriting.
     *
     * @param  string   $name
     * @param  mixed    $callback
     * @return \Illuminate\Support\Fluent
     * @throws \InvalidArgumentException
     */
    public function of($name, $callback = null)
    {
        $type = $this->definition['name'];

        if (is_null($type) or ! property_exists($this, $type)) {
            throw new RuntimeException("Not supported.");
        } elseif (! isset($this->keyMap[$name])) {
            throw new InvalidArgumentException("Name [{$name}] is not available.");
        }

        $id = $this->keyMap[$name];

        if (is_callable($callback)) {
            call_user_func($callback, $this->{$type}[$id]);
        }

        return $this->{$type}[$id];
    }

    /**
     * Build basic name, label and callback option.
     *
     * @param  mixed   $name
     * @param  mixed   $callback
     * @return array
     */
    protected function buildFluentAttributes($name, $callback = null)
    {
        $label = $name;

        if (! is_string($label)) {
            $callback = $label;
            $name     = '';
            $label    = '';
        } elseif (is_string($callback)) {
            $name     = Str::lower($callback);
            $callback = null;
        } else {
            $name  = Str::lower($name);
            $label = Str::title($name);
        }

        return array($label, $name, $callback);
    }

    /**
     * Magic Method for calling the methods.
     *
     * @param  string   $method
     * @param  array    $parameters
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call($method, array $parameters = array())
    {
        unset($parameters);

        if (! in_array($method, $this->definition['__call'])) {
            throw new InvalidArgumentException("Unable to use __call for [{$method}].");
        }

        return $this->$method;
    }

    /**
     * Magic Method for handling dynamic data access.
     *
     * @param  string   $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (! in_array($key, $this->definition['__get'])) {
            throw new InvalidArgumentException("Unable to use __get for [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * Magic Method for handling the dynamic setting of data.
     *
     * @param  string   $key
     * @param  array    $parameters
     * @return void
     * @throws \InvalidArgumentException
     */
    public function __set($key, $values)
    {
        if (! in_array($key, $this->definition['__set'])) {
            throw new InvalidArgumentException("Unable to use __set for [{$key}].");
        } elseif (! is_array($values)) {
            throw new InvalidArgumentException("Require values to be an array.");
        }

        $this->attributes($values, null);
    }

    /**
     * Magic Method for checking dynamically-set data.
     *
     * @param  string   $key
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function __isset($key)
    {
        if (! in_array($key, $this->definition['__isset'])) {
            throw new InvalidArgumentException("Unable to use __isset for [{$key}].");
        }

        return isset($this->{$key});
    }
}
