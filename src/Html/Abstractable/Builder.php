<?php namespace Orchestra\Html\Abstractable;

use Closure;
use InvalidArgumentException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;

abstract class Builder implements Renderable
{
    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Translator instance.
     *
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * View instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Grid instance.
     *
     * @var object
     */
    protected $grid;

    /**
     * Name of builder.
     *
     * @var string
     */
    public $name = null;

    /**
     * Create a new Builder instance.
     *
     * @var \Illuminate\Http\Request           $request
     * @var \Illuminate\Translation\Translator $translator
     * @var \Illuminate\Contracts\View\Factory $view
     * @var Grid                               $grid
     */
    abstract public function __construct(Request $request, Translator $translator, View $view, Grid $grid);

    /**
     * Extend decoration.
     *
     * @param  \Closure $callback
     * @return $this
     */
    public function extend(Closure $callback = null)
    {
        // Run the table designer.
        ! is_null($callback) && call_user_func($callback, $this->grid, $this->request, $this->translator);

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
     * @see    static::render()
     */
    public function __toString()
    {
        return $this->render();
    }
}
