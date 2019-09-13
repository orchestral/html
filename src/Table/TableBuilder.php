<?php

namespace Orchestra\Html\Table;

use Illuminate\Contracts\View\Factory as View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Translation\Translator;
use Orchestra\Contracts\Html\Grid as GridContract;
use Orchestra\Contracts\Html\Table\Builder as BuilderContract;
use Orchestra\Html\Builder as BaseBuilder;

class TableBuilder extends BaseBuilder implements BuilderContract
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
    public function render(): string
    {
        $grid = $this->grid;

        // Add paginate value for current listing while appending query string,
        // however we also need to remove ?page from being added twice.
        $input = Arr::except($this->request->query(), [$grid->pageName]);

        $grid->data();

        $pagination = (true === $grid->paginated() ? $grid->model->appends($input) : '');

        $data = [
            'empty' => $this->translator->get($grid->empty),
            'grid' => $grid,
            'pagination' => $pagination,
            'meta' => $grid->viewData,
        ];

        // Build the view and render it.
        $view = $this->view->make($grid->view())->with($data);

        return $view->render();
    }
}
