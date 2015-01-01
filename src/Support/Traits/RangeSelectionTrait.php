<?php namespace Orchestra\Html\Support\Traits;

trait RangeSelectionTrait
{
    /**
     * Create a select range field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     * @return string
     */
    public function selectRange($name, $begin, $end, $selected = null, $options = [])
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->select($name, $range, $selected, $options);
    }

    /**
     * Create a select year field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     * @return string
     */
    public function selectYear($name, $begin, $end, $selected = null, $options = [])
    {
        return call_user_func([$this, 'selectRange'], $name, $begin, $end, $selected, $options);
    }

    /**
     * Create a select month field.
     *
     * @param  string  $name
     * @param  string  $selected
     * @param  array   $options
     * @param  string  $format
     * @return string
     */
    public function selectMonth($name, $selected = null, $options = [], $format = '%B')
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = strftime($format, mktime(0, 0, 0, $month, 1));
        }

        return $this->select($name, $months, $selected, $options);
    }
}
