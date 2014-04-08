<?php namespace Orchestra\Html;

use BadMethodCallException;
use Orchestra\Support\Expression;

class HtmlBuilder extends \Illuminate\Html\HtmlBuilder
{
    /**
     * Generate a HTML element.
     *
     * @param  string   $tag
     * @param  mixed    $value
     * @param  array    $attributes
     * @return \Orchestra\Support\Expression
     */
    public function create($tag = 'div', $value = null, $attributes = array())
    {
        if (is_array($value)) {
            $attributes = $value;
            $value      = null;
        }

        $content = '<'.$tag.$this->attributes($attributes).'>';

        if (! is_null($value)) {
            $content .= $this->entities($value).'</'.$tag.'>';
        }

        return $this->raw($content);
    }

    /**
     * {@inheritdoc}
     */
    public function entities($value)
    {
        if ($value instanceof Expression) {
            return $value->get();
        }

        return parent::entities($value);
    }

    /**
     * Create a new HTML expression instance are used to inject HTML.
     *
     * @param  string   $value
     * @return \Orchestra\Support\Expression
     */
    public function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Build a list of HTML attributes from one or two array.
     *
     * @param  array    $attributes
     * @param  array    $defaults
     * @return array
     */
    public function decorate(array $attributes, array $defaults = array())
    {
        $class = $this->buildClassDecorate($attributes, $defaults);

        $attributes = array_merge($defaults, $attributes);

        empty($class) || $attributes['class'] = $class;

        return $attributes;
    }

    /**
     * Build class attribute from one or two array.
     *
     * @param  array    $attributes
     * @param  array    $defaults
     * @return array
     */
    protected function buildClassDecorate(array $attributes, array $defaults = array())
    {
        // Special consideration to class, where we need to merge both string
        // from $attributes and $defaults, then take union of both.
        $default   = array_get($defaults, 'class', '');
        $attribute = array_get($attributes, 'class', '');

        $classes   = explode(' ', trim($default.' '.$attribute));
        $current   = array_unique($classes);
        $excludes  = array();

        foreach ($current as $c) {
            if (starts_with($c, '!')) {
                $excludes[] = substr($c, 1);
                $excludes[] = $c;
            }
        }

        return implode(' ', array_diff($current, $excludes));
    }

    /**
     * {@inheritdoc}
     */
    public function image($url, $alt = null, $attributes = array(), $secure = null)
    {
        return $this->raw(parent::image($url, $alt, $attributes, $secure));
    }

    /**
     * {@inheritdoc}
     */
    public function link($url, $title = null, $attributes = array(), $secure = null)
    {
        return $this->raw(parent::link($url, $title, $attributes, $secure));
    }

    /**
     * {@inheritdoc}
     */
    public function mailto($email, $title = null, $attributes = array())
    {
        return $this->raw(parent::mailto($email, $title, $attributes));
    }

    /**
     * {@inheritdoc}
     */
    protected function listing($type, $list, $attributes = array())
    {
        return $this->raw(parent::listing($type, $list, $attributes));
    }

    /**
     * {@inheritdoc}
     */
    protected function listingElement($key, $type, $value)
    {
        return $this->raw(parent::listingElement($key, $type, $value));
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        $value = call_user_func_array(static::$macros[$method], $parameters);

        if (is_string($value)) {
            return $this->raw($value);
        }

        return $value;
    }
}
