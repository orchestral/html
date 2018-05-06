<?php

namespace Orchestra\Html;

use Illuminate\Contracts\Container\Container;
use Orchestra\Contracts\Html\Builder as BuilderContract;

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
     * @param  callable|null  $callback
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    abstract public function make(callable $callback = null): BuilderContract;

    /**
     * Create a new builder instance of a named builder.
     *
     * @param  string  $name
     * @param  callable|null  $callback
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function of(string $name, callable $callback = null): BuilderContract
    {
        if (! isset($this->names[$name])) {
            $this->names[$name] = $this->make($callback);
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
