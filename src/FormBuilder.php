<?php

namespace Orchestra\Html;

use Collective\Html\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
    /**
     * Create a checkboxes input field.
     *
     * @param  string  $name
     * @param  bool|array  $checked
     * @param  string  $separator
     *
     * @return string
     */
    public function checkboxes($name, array $list = [], $checked = null, array $options = [], $separator = '<br>')
    {
        $group = [];
        $name = \str_replace('[]', '', $name);

        foreach ($list as $id => $label) {
            $group[] = $this->generateCheckboxByGroup($id, $label, $name, $checked, $options);
        }

        return \implode($separator, $group);
    }

    /**
     * Generate checkbox by group.
     *
     * @param  string  $id
     * @param  string  $label
     * @param  string  $name
     * @param  bool|array  $checked
     *
     * @return array
     */
    protected function generateCheckboxByGroup($id, $label, $name, $checked, array $options)
    {
        $identifier = \sprintf('%s_%s', $name, $id);
        $key = \sprintf('%s[]', $name);
        $active = \in_array($id, (array) $checked);

        $options['id'] = $identifier;

        $control = $this->checkbox($key, $id, $active, $options);

        $label = $this->label($identifier, $label);

        return \implode(' ', [$control, $label]);
    }
}
