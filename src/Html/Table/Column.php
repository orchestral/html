<?php namespace Orchestra\Html\Table;

use Illuminate\Support\Fluent;

class Column extends Fluent
{
    /**
     * Get value of column.
     *
     * @param  mixed   $row
     * @return string
     */
    public function getValue($row)
    {
        $escape = $this->get('escape', false);
        $value  = call_user_func($this->attributes['value'], $row);

        return ($escape === true ? e($value) : $value);
    }
}
