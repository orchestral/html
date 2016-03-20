<?php

namespace Orchestra\Html;

use Closure;
use Illuminate\Contracts\Container\Container;

abstract class Factory
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Factory instances.
     *
     * @var array
     */
    protected $names = [];

    /**
     * Factory configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Construct a new factory.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Create a new Builder instance.
     *
     * @param  \Closure|null  $callback
     *
     * @return object
     */
    abstract public function make(Closure $callback = null);

    /**
     * Create a new builder instance of a named builder.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return object
     */
    public function of($name, Closure $callback = null)
    {
        if (! isset($this->names[$name])) {
            $this->names[$name]       = $this->make($callback);
            $this->names[$name]->name = $name;
        }

        return $this->names[$name];
    }

    /**
     * Set configuration.
     *
     * @param  array  $config
     *
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }
}
