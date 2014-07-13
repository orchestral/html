<?php namespace Orchestra\Html\Abstractable;

use Closure;
use InvalidArgumentException;
use Illuminate\Container\Container;
use Illuminate\Support\Contracts\RenderableInterface;

abstract class Builder implements RenderableInterface
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app = null;

    /**
     * Name of builder.
     *
     * @var string
     */
    public $name = null;

    /**
     * Grid instance.
     *
     * @var object
     */
    protected $grid = null;

    /**
     * Create a new Builder instance.
     *
     * @param  \Illuminate\Container\Container  $app
     * @param  \Closure                         $callback
     */
    abstract public function __construct(Container $app, Closure $callback);

    /**
     * Extend decoration.
     *
     * @param  \Closure $callback
     * @return AbstractableBuilder
     */
    public function extend(Closure $callback)
    {
        // Run the table designer.
        call_user_func($callback, $this->grid);

        return $this;
    }

    /**
     * Magic method to get Grid instance.
     *
     * @param  string   $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (! in_array($key, array('grid', 'name'))) {
            throw new InvalidArgumentException("Unable to get property [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * An alias to render().
     *
     * @return string
     * @see    AbstractableBuilder::render()
     */
    public function __toString()
    {
        return $this->render();
    }
}
