<?php namespace Orchestra\Html;

use Closure;
use InvalidArgumentException;
use Illuminate\Support\Contracts\RenderableInterface;

abstract class AbstractableBuilder implements RenderableInterface {
	
	/**
	 * Application instance.
	 *
	 * @var Illuminate\Foundation\Application
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
	 * @access public
	 * @param  Illuminate\Foundation\Application    $app
	 * @param  Closure                              $callback
	 * @return void	 
	 */
	abstract public function __construct($app, Closure $callback);

	/**
	 * Extend decoration. 
	 *
	 * @access public
	 * @param  Closure $callback
	 * @return void
	 */
	public function extend(Closure $callback)
	{
		// Run the table designer.
		call_user_func($callback, $this->grid);
	}

	/**
	 * Magic method to get Grid instance.
	 */
	public function __get($key)
	{
		if ( ! in_array($key, array('grid', 'name'))) 
		{
			throw new InvalidArgumentException(
				"Unable to get property [{$key}]."
			);
		}
		
		return $this->{$key};
	}

	/**
	 * An alias to render()
	 *
	 * @access  public
	 * @see     render()
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Render the decoration.
	 *
	 * @abstract
	 * @access  public
	 * @return  string
	 */
	abstract public function render();
}
