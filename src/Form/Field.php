<?php

namespace Orchestra\Html\Form;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;
use Orchestra\Contracts\Html\Form\Field as FieldContract;
use Orchestra\Html\Concerns\Decorate;

class Field extends Fluent implements FieldContract
{
    use Decorate;

    /**
     * Get value of column.
     *
     * @param  mixed  $row
     */
    public function getField($row, array $templates = []): string
    {
        $value = $this->attributes['field']($row, $this, $templates);

        if ($value instanceof Renderable) {
            return $value->render();
        }

        return $value;
    }

    /**
     * Setup attributes via decorate.
     *
     * @return $this
     */
    public function attributes(array $value = [])
    {
        $this->attributes['attributes'] = $this->decorate(
            $value, $this->attributes['attributes']
        );

        return $this;
    }
}
