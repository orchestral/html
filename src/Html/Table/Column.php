<?php namespace Orchestra\Html\Table;

class Column extends \Illuminate\Support\Fluent
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
