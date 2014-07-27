<?php namespace Orchestra\Html\Table;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Translation\Translator;
use Illuminate\View\Factory as View;
use Orchestra\Html\Abstractable\Grid as AbstractableGrid;

class TableBuilder extends \Orchestra\Html\Abstractable\Builder
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

        // Add paginate value for current listing while appending query string,
        // however we also need to remove ?page from being added twice.
        $input = Arr::except($this->request->query(), array('page'));

        $rows = $grid->rows();

        $pagination = (true === $grid->paginate ? $grid->model->appends($input)->links() : '');

        if(count($grid->searchable) > 0)
        {
            \Orchestra\Site::set('header::search-form', \Input::get($grid->key, ''));
            \Orchestra\Site::set('header::search-key', $grid->key);
        }


        $data = array(
            'attributes' => array(
                'row'   => $grid->rows->attributes,
                'table' => $grid->attributes,
            ),
            'columns'    => $grid->columns(),
            'empty'      => $this->translator->get($grid->empty),
            'grid'       => $grid,
            'pagination' => $pagination,
            'rows'       => $rows,
            'sortable'   => $grid->sortable,
            'sortKey'    => $grid->sortKey,
            'orderKey'   => $grid->orderKey
        );

        // Build the view and render it.
        return $this->view->make($grid->view)->with($data)->render();
    }
}
