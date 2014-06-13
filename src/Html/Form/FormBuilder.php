<?php namespace Orchestra\Html\Form;

use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Illuminate\View\Environment as View;
use Orchestra\Html\Abstractable\Grid as AbstractableGrid;

class FormBuilder extends \Orchestra\Html\Abstractable\Builder
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Request $request, Translator $translator, View $view, AbstractableGrid $grid)
    {
        $this->request    = $request;
        $this->translator = $translator;
        $this->view       = $view;
        $this->grid       = $grid;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $grid = $this->grid;
        $form = $grid->attributes;

        $data = array(
            'grid'      => $grid,
            'fieldsets' => $grid->fieldsets(),
            'form'      => $form,
            'format'    => $grid->format,
            'hiddens'   => $grid->hiddens,
            'row'       => $grid->row,
            'submit'    => $this->translator->get($grid->submit),
            'token'     => $grid->token,
        );

        return $this->view->make($grid->view)->with($data)->render();
    }
}
