---
title: HTML Change Log

---

## Version 3.1 {#v3-1}

### v3.1.8 {#v3-1-8}

* Set the `Orchestra\Html\Table\Grid::$model` as array by default to avoid class to throw an error when manually setting the rows.
* Avoid rebuilding model to array when it's an empty array.

### v3.1.7 {#v3-1-7}

* Don't double escape in `Orchestra\Html\Support\FormBuilder::option()`.
* Allow `Orchestra\Html\Support\FormBuilder::checkbox()` to work with eloquent relationship.
* Fixes incorrect param type in `Orchestra\Html\Support\HtmlBuilder` codeblock.

### v3.1.6 {#v3-1-6}

* Rework on configuration binding. Solve issue where configuration is missing when binding occur earlier than service provider booting process especially during testing.
* Use new `Orchestra\Html\HtmlBuilder::attributable()` on table default views.

### v3.1.5 {#v3-1-5}

* Add `Orchestra\Html\HtmlBuilder::attributable()` method.

### v3.1.4 {#v3-1-4}

* Add support for Laravel 5 Config.
* Add tel and time input field.
* Allow inserting just the basic Eloquent model into the `Orchestra\Html\Table\Grid::with()` method.

### v3.1.3 {#v3-1-3}

* Add a new `Orchestra\Html\Grid::find()` contracts and implements it on:
  - `Orchestra\Html\Form\Fieldset`.
  - `Orchestra\Html\Table\Grid`.

### v3.1.2 {#v3-1-2}

* Improved performances by reducing call within `Illuminate\Container\Container`.

### v3.1.1 {#v3-1-1}

* Bump minimum version to PHP v5.5.0.
* Ensure checkboxes and select "options" return array instead of `Illuminate\Support\Collection` etc.

### v3.1.0 {#v3-1-0}

* Update support for Laravel Framework v5.1.
* Add `Form::number()` and `Form::date()` helpers.
* Add `HTML::meta()` and `HTML::dl()` helpers.

## Version 3.0 {#v3-0}

### v3.0.2 {#v3-0-2}

* Add `Orchestra\Html\HtmlBuilder::attributable()` method.

### v3.0.1 {#v3-0-1}

* Add `Form::number()` and `Form::date()` helpers.
* Add `HTML::meta()` and `HTML::dl()` helpers.

### v3.0.0 {#v3-0-0}

* Update support for Laravel Framework v5.0.
* Simplify PSR-2 path.
* Replace `illuminate/html`.
* Allow `Orchestra\Html\Form\Factory` to handle all request to `Illuminate\Html\FormBuilder`.
* Utilize both `illuminate/contracts` and `orchestra/contracts`.
* Update support for Laravel 5 paginator class.
* Resolve form template directly from config, allow developer to add additional form type.
* Add `Orchestra\Html\FormBuilder`, extending `Illuminate\Html\FormBuilder` to remove checkboxes macro.
* `Orchestra\Html\Form\Field::getField()` can automatically render any object implementing `Illuminate\Contracts\Support\Renderable`.

## Version 2.2 {#v2-2}

### v2.2.4 {#v2-2-4}

* Allow `Orchestra\Html\Form\Factory` to handle all request to `Illuminate\Html\FormBuilder`.
* Allow `Orchestra\Html\Form\Field` to handle instance of `Illuminate\Support\Contracts\RenderableInterface`.
* Allow to filter sortable columns either using `only` or `except` option based on changes to `Orchestra\Support\Traits\QueryFilterTrait`.

### v2.2.3 {#v2-2-3}

* Add `Orchestra\Html\Table\Grid::searchable()` and `Orchestra\Html\Table\Grid::sortable()` to facilitate searching and sorting.
* Allow to retrieve instance of `Illuminate\Http\Request` and `Illuminate\Translation\Translator` from within Form and Table builder.
* Add `Orchestra\Html\Table\Grid::paginated()` helper method to access pagination state for current table.
* Utilize `Illuminate\Support\Arr`.

### v2.2.2 {#v2-2-2}

* Allow support for `checkboxes` form.
* Utilize `data_get()` and `array_*()` helpers.

### v2.2.1 {#v2-2-1}

* Allow control to be accessed from `Orchestra\Html\Form\Grid` context via a new `find()` method.

### v2.2.0 {#v2-2-0}

* Bump minimum version to PHP v5.4.0.
* Update to use `Illuminate\View\Factory`.
* Rename Environment to Factory.

## Version 2.1 {#v2-1}

### v2.1.6 {#v2-1-6}

* Allow to retrieve instance of `Illuminate\Http\Request` and `Illuminate\Translation\Translator` from within Form and Table builder.
* Add `Orchestra\Html\Table\Grid::paginated()` helper method to access pagination state for current table.

### v2.1.5 {#v2-1-5}

* Allow support for `checkboxes` form.
* Utilize `data_get()` and `array_*()` helpers.

### v2.1.4 {#v2-1-4}

* Allow control to be accessed from `Orchestra\Html\Form\Grid` context via a new `find()` method.

### v2.1.3 {#v2-1-3}

* Add secure url option to `HTML::image()` as per changes on `Illuminate\Html`.

### v2.1.2 {#v2-1-2}

* Implement [PSR-4](https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md) autoloading structure.

### v2.1.1 {#v2-1-1}

* Fixes `FatalErrorException` being thrown when inheriting abstract method.

### v2.1.0 {#v2-1-0}

* `HTML::image()`, `HTML::link()`, `HTML::create()`, `HTML::ol()`, `HTML::ul()` and macro will utilize `HTML::raw()`.
* Predefined package path to avoid additional overhead to guest package path.
* Add `Orchestra\Html\Form\Grid::resource()` and `Orchestra\Html\Form\Grid::setup()` to simplify some code generation via `Orchestra\Html\Form\PresenterInterface` contract.
* Implement [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard.
* Allow creation of `Form::button` from Form Builder.
* Add additional to array convertion as `Orchestra\Html\Table\Grid::with()` should be able to take any time of:
  - Array
  - `Illuminate\Support\Contracts\ArrayableInterface`
  - `Illuminate\Pagination\Paginator` instance
  - Database Query Builder (Fluent and Eloquent)
* `Orchestra\Html\Table\Grid::with()` to convert array to `Illuminate\Support\Fluent` to provide an object-like usage.

## Version 2.0 {#v2-0}

### v2.0.11 {#v2-0-11}

* Remove `abstract public function render()` from `Orchestra\Html\Abstractable\Builder` to avoid exception to be thrown in some environment.

### v2.0.10 {#v2-0-10}

* Allow creation of `Form::button` from Form Builder.
* Add additional to array convertion as `Orchestra\Html\Table\Grid::with()` should be able to take any time of:
  - Array
  - `Illuminate\Support\Contracts\ArrayableInterface`
  - `Illuminate\Pagination\Paginator` instance
  - Database Query Builder (Fluent and Eloquent)
* `Orchestra\Html\Table\Grid::with()` to convert array to `Illuminate\Support\Fluent` to provide an object-like usage.

### v2.0.9 {#v2-0-9}

* Implement [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard.

### v2.0.8 {#v2-0-8}

* Huge internal refactor to reduce code complexity, which would result to increase in performance.

### v2.0.7 {#v2-0-7}

* Directly inject `session.store` instance instead of `session` (Session Manager) instance based on upstream changes.

### v2.0.6 {#v2-0-6}

* Append `$grid` when rendering view for `Orchestra\Html\Table\TableBuider` and `Orchestra\Html\Form\FormBuilder`.

### v2.0.5 {#v2-0-5}

* Update Form configuration to match Orchestra Platform official grid CSS structure.
* Include `.form-control` as default class attribute for Form builder.

### v2.0.4 {#v2-0-4}

* Code improvements.

### v2.0.3 {#v2-0-3}

* Fixed can't inherit abstract function `Illuminate\Support\Contracts\RenderableInterface::render()` (previously declared abstract in `Orchestra\Html\AbstractableBuilder`).

### v2.0.2 {#v2-0-2}

* Fixed `Orchestra\Html\HtmlBuilder::__call()` to only handle macros, this would avoid rare a bug where infinite loop was reported.

### v2.0.1 {#v2-0-1}

* `HTML::image()`, `HTML::link()`, `HTML::create()`, `HTML::ol()`, `HTML::ul()` and macro will utilize `HTML::raw()`.

### v2.0.0 {#v2-0-0}

* Migrate `Orchestra\Html`, `Orchestra\Form` and `Orchestra\Table` from Orchestra Platform 1.2.
* `Orchestra\Table` would automatically paginate result via `$table->with($model)`, disable it via `$table->with($model, false);`.
* `Orchestra\Form` add helper method to attach Eloquent using `$form->with($model);`.
* `Orchestra\Form::extend()` and `Orchestra\Table::extend()` should return self.
