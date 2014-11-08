<?php namespace Orchestra\Html\Form;

use Illuminate\Support\Fluent;
use Illuminate\Contracts\Support\Renderable;
use Orchestra\Contracts\Html\Form\Field as FieldContract;

class Field extends Fluent implements FieldContract
{
    /**
     * Get value of column.
     *
     * @param  mixed   $row
     * @param  mixed   $control
     * @param  array   $attributes
     * @return string
     */
    public function getField($row, $control, array $attributes = [])
    {
        $value = call_user_func($this->attributes['field'], $row, $control, $attributes);

        if ($value instanceof Renderable) {
            return $value->render();
        }

        return $value;
    }
}
