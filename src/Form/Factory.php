<?php

namespace Orchestra\Html\Form;

use Orchestra\Contracts\Html\Builder as BuilderContract;
use Orchestra\Contracts\Html\Form\Factory as FactoryContract;
use Orchestra\Html\Factory as BaseFactory;

class Factory extends BaseFactory implements FactoryContract
{
    /**
     * {@inheritdoc}
     */
    public function make(callable $callback = null): BuilderContract
    {
        $builder = new FormBuilder(
            $this->app->make('request'),
            $this->app->make('translator'),
            $this->app->make('view'),
            new Grid($this->app, $this->config)
        );

        return $builder->extend($callback);
    }

    /**
     * Allow to access `form` service location method using magic method.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->app->make('form')->{$method}(...$parameters);
    }
}
