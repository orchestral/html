<?php namespace Orchestra\Html\Table;

use Closure;

class Factory extends \Orchestra\Html\Abstractable\Factory
{
    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback)
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
