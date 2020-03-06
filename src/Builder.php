<?php

namespace Orchestra\Html;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use InvalidArgumentException;
use Orchestra\Contracts\Html\Builder as BuilderContract;
use Orchestra\Contracts\Html\Grid as GridContract;

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
     */
    abstract public function __construct(Request $request, Translator $translator, View $view, GridContract $grid);

    /**
     * Extend decoration.
     *
     * @param  callable  $callback
     *
     * @return $this
     */
    public function extend(callable $callback = null)
    {
        // Run the table designer.
        if (! \is_null($callback)) {
            \call_user_func($callback, $this->grid, $this->request, $this->translator);
        }

        return $this;
    }

    /**
     * Magic Method for calling the methods.
     *
     * @return $this
     */
    public function __call(string $method, array $parameters)
    {
        $this->grid->{$method}(...$parameters);

        return $this;
    }

    /**
     * Magic method to get Grid instance.
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if (! \in_array($key, ['grid', 'name'])) {
            throw new InvalidArgumentException("Unable to get property [{$key}].");
        }

        return $this->{$key};
    }

    /**
     * Get the the HTML string.
     */
    public function toHtml(): string
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
