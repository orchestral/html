<?php namespace Orchestra\Html\Form;

use Closure;
use Orchestra\Html\Factory as FactoryContract;

class Factory extends FactoryContract
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

    /**
     * Allow to access `form` service location method using magic method.
     *
     * @param  string   $method
     * @param  array    $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->app['form'], $method], $parameters);
    }
}
