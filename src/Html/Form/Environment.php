<?php namespace Orchestra\Html\Form;

use Closure;

class Environment extends \Orchestra\Html\Abstractable\Environment
{
    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback = null)
    {
        $builder = new FormBuilder(
            $this->app['request'],
            $this->app['translator'],
            $this->app['view'],
            new Grid($this->app)
        );

        return $builder->extend($callback);
    }
}
