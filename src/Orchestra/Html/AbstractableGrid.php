<?php namespace Orchestra\Html;

use Illuminate\Container\Container;

abstract class AbstractableGrid {

	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app = null;

	/**
	 * Grid attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();
	
	/**
	 * Create a new Grid instance.
	 * 
	 * @param  \Illuminate\Container\Container  $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
		$this->initiate();
	}

	/**
	 * Load grid configuration.
	 *
	 * @return void
	 */
	protected abstract function initiate();

	/**
	 * Add or append Grid attributes.
	 * 
	 * @param  mixed    $key
	 * @param  mixed    $value
	 * @return array|null
	 */
	public function attributes($key = null, $value = null)
	{
		if (is_null($key)) return $this->attributes;

		if (is_array($key))
		{
			$this->attributes = array_merge($this->attributes, $key);
		}
		else
		{
			$this->attributes[$key] = $value;
		}
	}

	/**
	 * Magic Method for calling the methods.
	 *
	 * @param  string   $method
	 * @param  array    $parameters
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public abstract function __call($method, array $parameters = array());

	/**
	 * Magic Method for handling dynamic data access.
	 *
	 * @param  string   $key
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public abstract function __get($key);

	/**
	 * Magic Method for handling the dynamic setting of data.
	 *
	 * @param  string   $key
	 * @param  array    $parameters
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public abstract function __set($key, $parameters);

	/**
	 * Magic Method for checking dynamically-set data.
	 * 
	 * @param  string   $key
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
	public abstract function __isset($key);
}
