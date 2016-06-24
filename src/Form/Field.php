<?php

namespace Orchestra\Html\Form;

use Illuminate\Support\Fluent;
use Illuminate\Contracts\Support\Renderable;
use Orchestra\Contracts\Html\Form\Field as FieldContract;

class Field extends Fluent implements FieldContract
{
    /**
     * Get value of column.
     *
     * @param  mixed  $row
     * @param  mixed  $control
     * @param  array  $templates
     *
     * @return string
     */
    public function getField($row, array $templates = [])
    {
        $value = $this->attributes['field']($row, $this, $templates);

        if ($value instanceof Renderable) {
            return $value->render();
        }

        return $value;
    }
}
