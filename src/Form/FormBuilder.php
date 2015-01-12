<?php namespace Orchestra\Html\Form;

use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Orchestra\Html\Builder as BaseBuilder;
use Illuminate\Contracts\View\Factory as View;
use Orchestra\Contracts\Html\Grid as GridContract;
use Orchestra\Contracts\Html\Form\Builder as BuilderContract;

class FormBuilder extends BaseBuilder implements BuilderContract
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Request $request, Translator $translator, View $view, GridContract $grid)
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

        $data = [
            'grid'      => $grid,
            'fieldsets' => $grid->fieldsets(),
            'form'      => $form,
            'format'    => $grid->format,
            'hiddens'   => $grid->hiddens,
            'row'       => $grid->row,
            'submit'    => $this->translator->get($grid->submit),
            'token'     => $grid->token,
        ];

        return $this->view->make($grid->view)->with($data)->render();
    }
}
