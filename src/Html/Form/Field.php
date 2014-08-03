<?php namespace Orchestra\Html\Form;

use Illuminate\Support\Fluent;

class Field extends Fluent
{
    /**
     * Get value of column.
     *
     * @param  mixed   $row
     * @param  mixed   $control
     * @param  array   $attributes
     * @return string
     */
    public function getField($row, $control, array $attributes = array())
    {
        return call_user_func($this->attributes['field'], $row, $control, $attributes);
    }
}
