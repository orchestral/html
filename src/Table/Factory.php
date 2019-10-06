<?php

namespace Orchestra\Html\Table;

use Orchestra\Contracts\Html\Builder as BuilderContract;
use Orchestra\Contracts\Html\Table\Factory as FactoryContract;
use Orchestra\Html\Factory as BaseFactory;

class Factory extends BaseFactory implements FactoryContract
{
    /**
     * {@inheritdoc}
     */
    public function make(callable $callback = null): BuilderContract
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
