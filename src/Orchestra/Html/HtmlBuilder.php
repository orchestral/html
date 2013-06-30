<?php namespace Orchestra\Html;

use Orchestra\Support\Expression;

class HtmlBuilder extends \Illuminate\Html\HtmlBuilder {

	/**
	 * Generate a HTML element.
	 *
	 * @access public
	 * @param  string   $tag
	 * @param  mixed    $value
	 * @param  array    $attributes
	 * @return \Orchestra\Support\Expression
	 */
	public function create($tag = 'div', $value = null, $attributes = array())
	{
		if (is_array($value))
		{
			$attributes = $value;
			$value      = null;
		}

		$content = '<'.$tag.$this->attributes($attributes).'>';

		if ( ! is_null($value))
		{
			$content .= $this->entities($value).'</'.$tag.'>';
		}
		
		return $this->raw($content);
	}

	/**
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be 
	 * used.
	 *
	 * @access public
	 * @param  string   $value
	 * @return string
	 */
	public function entities($value)
	{
		if ($value instanceof Expression) return $value->get();
		
		return parent::entities($value);
	}

	/**
	 * Create a new HTML expression instance are used to inject HTML.
	 * 
	 * @access public
	 * @param  string      $value
	 * @return \Orchestra\Support\Expression
	 */
	public function raw($value)
	{
		return new Expression($value);
	}

	/**
	 * Build a list of HTML attributes from one or two array.
	 *
	 * @access public
	 * @param  array    $attributes
	 * @param  array    $defaults
	 * @return array
	 */
	public function decorate($attributes, $defaults = null)
	{
		// Special consideration to class, where we need to merge both string 
		// from $attributes and $defaults and take union of both.
		$c1       = isset($defaults['class']) ? $defaults['class'] : '';
		$c2       = isset($attributes['class']) ? $attributes['class'] : '';
		$classes  = explode(' ', trim($c1.' '.$c2));
		$current  = array_unique($classes);
		$excludes = array();

		foreach ($current as $c)
		{
			if (starts_with($c, '!'))
			{
				$excludes[] = substr($c, 1);
				$excludes[] = $c;
			}
		}

		$class      = implode(' ', array_diff($current, $excludes));
		$attributes = array_merge($defaults, $attributes);

		empty($class) or $attributes['class'] = $class;

		return $attributes;
	}
	
	/**
	 * Generate an HTML image element.
	 *
	 * @param  string  $url
	 * @param  string  $alt
	 * @param  array   $attributes
	 * @return \Orchestra\Support\Expression
	 */
	public function image($url, $alt = null, $attributes = array())
	{
		return $this->raw(parent::image($url, $alt, $attributes));
	}
	
	/**
	 * Generate a HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $secure
	 * @return \Orchestra\Support\Expression
	 */
	public function link($url, $title = null, $attributes = array(), $secure = null)
	{
		return $this->raw(parent::link($url, $title, $attributes, $secure));
	}
	
	/**
	 * Generate a HTML link to an email address.
	 * 
	 * @param  string  $email
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return \Orchestra\Support\Expression
	 */
	public function mailto($email, $title = null, $attributes = array())
	{
		return $this->raw(parent::mailto($email, $title, $attributes));
	}
	
	/**
	 * Create a listing HTML element.
	 *
	 * @param  string  $type
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return \Orchestra\Support\Expression
	 */
	protected function listing($type, $list, $attributes = array())
	{
		return $this->raw(parent::listing($type, $list, $attributes));
	}
	
	/**
	 * Create the HTML for a listing element.
	 *
	 * @param  mixed    $key
	 * @param  string  $type
	 * @param  string  $value
	 * @return \Orchestra\Support\Expression
	 */
	protected function listingElement($key, $type, $value)
	{
		return $this->raw(parent::listingElement($key, $type, $value));
	}
	
	/**
	 * Dynamically handle calls to the html class.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$value = call_user_func_array(array($this, $method), $parameters);
		
		if(is_string($value)) return $this->raw($value);
		
		return $value;
	}
	
}
