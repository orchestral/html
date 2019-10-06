<?php

namespace Orchestra\Html\Form;

use Illuminate\Contracts\View\Factory as View;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Orchestra\Contracts\Html\Form\Builder as BuilderContract;
use Orchestra\Contracts\Html\Grid as GridContract;
use Orchestra\Html\Builder as BaseBuilder;

class FormBuilder extends BaseBuilder implements BuilderContract
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Request $request, Translator $translator, View $view, GridContract $grid)
    {
        $this->request = $request;
        $this->translator = $translator;
        $this->view = $view;
        $this->grid = $grid;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $grid = $this->grid;

        $data = [
            'grid' => $grid,
            'format' => $grid->format,
            'submit' => $this->translator->get($grid->submit),
            'token' => $grid->token,
            'meta' => $grid->viewData,
        ];

        $view = $this->view->make($grid->view())->with($data);

        return $view->render();
    }
}
