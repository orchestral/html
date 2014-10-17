<?php namespace Orchestra\Html\Table;

use Closure;
use Orchestra\Html\Factory as FactoryContract;

class Factory extends FactoryContract
{
    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback = null)
    {
        $builder = new TableBuilder(
            $this->app['request'],
            $this->app['translator'],
            $this->app['view'],
            new Grid($this->app)
        );

        return $builder->extend($callback);
    }
}
