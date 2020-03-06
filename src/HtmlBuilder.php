<?php

namespace Orchestra\Html;

use Collective\Html\HtmlBuilder as BaseHtmlBuilder;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class HtmlBuilder extends BaseHtmlBuilder
{
    /**
     * Generate a HTML element.
     *
     * @param  mixed   $value
     */
    public function create(string $tag = 'div', $value = null, array $attributes = []): Htmlable
    {
        if (\is_array($value)) {
            $attributes = $value;
            $value = null;
        }

        $content = '<'.$tag.$this->attributes($attributes).'>';

        if (! \is_null($value)) {
            $content .= $this->entities($value).'</'.$tag.'>';
        }

        return $this->toHtmlString($content);
    }

    /**
     * Create a new HTML expression instance are used to inject HTML.
     */
    public function raw(string $value): Htmlable
    {
        return $this->toHtmlString($value);
    }

    /**
     * Build a list of HTML attributes from one or two array and generate
     * HTML attributes.
     */
    public function attributable(array $attributes, array $defaults = []): string
    {
        return $this->attributes($this->decorate($attributes, $defaults));
    }

    /**
     * Build a list of HTML attributes from one or two array.
     */
    public function decorate(array $attributes, array $defaults = []): array
    {
        $class = $this->buildClassDecorate($attributes, $defaults);

        $attributes = \array_merge($defaults, $attributes);

        if (! empty($class)) {
            $attributes['class'] = $class;
        }

        return $attributes;
    }

    /**
     * Build class attribute from one or two array.
     */
    protected function buildClassDecorate(array $attributes, array $defaults = []): string
    {
        // Special consideration to class, where we need to merge both string
        // from $attributes and $defaults, then take union of both.
        $default = $defaults['class'] ?? '';
        $attribute = $attributes['class'] ?? '';

        $classes = \explode(' ', \trim($default.' '.$attribute));
        $current = \array_unique($classes);
        $excludes = [];

        foreach ($current as $c) {
            if (Str::startsWith($c, '!')) {
                $excludes[] = substr($c, 1);
                $excludes[] = $c;
            }
        }

        return \implode(' ', \array_diff($current, $excludes));
    }
}
