<?php

namespace Orchestra\Html\Table;

use Closure;
use Orchestra\Html\Factory as BaseFactory;
use Orchestra\Contracts\Html\Table\Factory as FactoryContract;

class Factory extends BaseFactory implements FactoryContract
{
    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback = null)
    {
        $builder = new TableBuilder(
            $this->app->make('request'),
            $this->app->make('translator'),
            $this->app->make('view'),
            new Grid($this->app, $this->config)
        );

        return $builder->extend($callback);
    }
}
