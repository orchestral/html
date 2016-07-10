<?php

namespace Orchestra\Html;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Translation\Translator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as View;
use Orchestra\Contracts\Html\Grid as GridContract;
use Orchestra\Contracts\Html\Builder as BuilderContract;

abstract class Builder implements BuilderContract, Htmlable
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
     * @var \Orchestra\Html\Grid
     */
    protected $grid;

    /**
     * Name of builder.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new Builder instance.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Translation\Translator  $translator
     * @param \Illuminate\Contracts\View\Factory  $view
     * @param \Orchestra\Contracts\Html\Grid  $grid
     */
    abstract public function __construct(Request $request, Translator $translator, View $view, GridContract $grid);

    /**
     * Extend decoration.
     *
     * @param  \Closure  $callback
     *
     * @return $this
     */
    public function extend(Closure $callback = null)
    {
        // Run the table designer.
        ! is_null($callback) && $callback($this->grid, $this->request, $this->translator);

        return $this;
    }

    /**
     * Magic Method for calling the methods.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return $this
     */
    public function __call($method, array $parameters)
    {
        $this->grid->{$method}(...$parameters);

        return $this;
    }

    /**
     * Magic method to get Grid instance.
     *
     * @param  string  $key
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (! in_array($key, ['grid', 'name'])) {
            throw new InvalidArgumentException("Unable to get property [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * Get the the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->render();
    }

    /**
     * An alias to render().
     *
     * @return string
     *
     * @see static::render()
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
