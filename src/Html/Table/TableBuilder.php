<?php namespace Orchestra\Html\Table;

use Illuminate\Support\Arr;
use Orchestra\Html\Builder;
use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use Orchestra\Html\Grid as GridContract;
use Illuminate\Contracts\View\Factory as View;

class TableBuilder extends Builder
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

        // Add paginate value for current listing while appending query string,
        // however we also need to remove ?page from being added twice.
        $input = Arr::except($this->request->query(), ['page']);

        $rows = $grid->rows();

        $pagination = (true === $grid->paginated() ? $grid->model->appends($input) : '');

        $data = [
            'attributes' => [
                'row'   => $grid->rows->attributes,
                'table' => $grid->attributes,
            ],
            'columns'    => $grid->columns(),
            'empty'      => $this->translator->get($grid->empty),
            'grid'       => $grid,
            'pagination' => $pagination,
            'rows'       => $rows,
        ];

        // Build the view and render it.
        return $this->view->make($grid->view)->with($data)->render();
    }
}
