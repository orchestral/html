<?php namespace Orchestra\Html;

class FormBuilder extends \Illuminate\Html\FormBuilder
{
    /**
     * Create a checkboxes input field.
     *
     * @param  string  $name
     * @param  array   $list
     * @param  bool    $checked
     * @param  array   $options
     * @return string
     */
    public function checkboxes($name, $list = array(), $checked = null, $options = array())
    {
        $group = [];

        foreach ($list as $id => $label) {
            $name = str_replace('[]', '', $name);
            $identifier = sprintf('%s_%s', $name, $id);

            $options['id'] = $identifier;

            $control = $this->checkbox(
                sprintf('%s[]', $name),
                $id,
                in_array($id, (array) $checked),
                $options
            );

            $label = $this->label($identifier, $label);

            $group[] = implode(' ', [$control, $label]);
        }

        return implode('<br>', $group);
    }
}
