<?php

namespace Orchestra\Html;

use Orchestra\Html\Traits\Decorate;
use Illuminate\Contracts\Support\Htmlable;
use Collective\Html\HtmlBuilder as BaseHtmlBuilder;

class HtmlBuilder extends BaseHtmlBuilder
{
    use Decorate;

    /**
     * Generate a HTML element.
     *
     * @param  string  $tag
     * @param  mixed   $value
     * @param  array   $attributes
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function create($tag = 'div', $value = null, $attributes = [])
    {
        if (is_array($value)) {
            $attributes = $value;
            $value      = null;
        }

        $content = '<'.$tag.$this->attributes($attributes).'>';

        if (! is_null($value)) {
            $content .= $this->entities($value).'</'.$tag.'>';
        }

        return $this->toHtmlString($content);
    }

    /**
     * {@inheritdoc}
     */
    public function entities($value, $encoding = false)
    {
        if ($value instanceof Htmlable) {
            return $value->toHtml();
        }

        return parent::entities($value, $encoding);
    }

    /**
     * Create a new HTML expression instance are used to inject HTML.
     *
     * @param  string  $value
     *
     * @return \Illuminate\Contracts\Support\Htmlable
     */
    public function raw($value)
    {
        return $this->toHtmlString($value);
    }

    /**
     * Build a list of HTML attributes from one or two array and generate
     * HTML attributes.
     *
     * @param  array  $attributes
     * @param  array  $defaults
     *
     * @return string
     */
    public function attributable(array $attributes, array $defaults = [])
    {
        return $this->attributes($this->decorate($attributes, $defaults));
    }
}
