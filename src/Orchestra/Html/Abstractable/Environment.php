<?php namespace Orchestra\Html\Abstractable;

use Closure;
use InvalidArgumentException;
use Illuminate\Container\Container;

abstract class Environment {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app = null;

	/**
	 * Environment instances.
	 *
	 * @var array
	 */
	protected $names = array();

	/**
	 * Construct a new environment.
	 * 
	 * @param  \Illuminate\Container\Container  $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Create a new Builder instance.
	 *
	 * @param  \Closure $callback
	 * @return object
	 */
	abstract public function make(Closure $callback);

	/**
	 * Create a new builder instance of a named builder.
	 *
	 * @param  string   $name
	 * @param  \Closure $callback
	 * @return object
	 */
	public function of($name, Closure $callback = null)
	{
		if ( ! isset($this->names[$name]))
		{
			$this->names[$name] = $this->make($callback);
			$this->names[$name]->name = $name;
		}

		return $this->names[$name];
	}
}
