HTML Change Log
==============

## Version 2.1

### v2.1.0@dev

* `HTML::image()`, `HTML::link()`, `HTML::create()`, `HTML::ol()`, `HTML::ul()` and macro will utilize `HTML::raw()`.
* Predefined package path to avoid additional overhead to guest package path.

## Version 2.0

### v2.0.8

* Huge internal refactor to reduce code complexity, which would result to increase in performance.

### v2.0.7

* Directly inject `session.store` instance instead of `session` (Session Manager) instance based on upstream changes.

### v2.0.6

* Append `$grid` when rendering view for `Orchestra\Html\Table\TableBuider` and `Orchestra\Html\Form\FormBuilder`.

### v2.0.5

* Update Form configuration to match Orchestra Platform official grid CSS structure.
* Include `.form-control` as default class attribute for Form builder.

### v2.0.4

* Code improvements.

### v2.0.3

* Fixed can't inherit abstract function `Illuminate\Support\Contracts\RenderableInterface::render()` (previously declared abstract in `Orchestra\Html\AbstractableBuilder`).

### v2.0.2

* Fixed `Orchestra\Html\HtmlBuilder::__call()` to only handle macros, this would avoid rare a bug where infinite loop was reported.

### v2.0.1

* `HTML::image()`, `HTML::link()`, `HTML::create()`, `HTML::ol()`, `HTML::ul()` and macro will utilize `HTML::raw()`.

### v2.0.0

* Migrate `Orchestra\Html`, `Orchestra\Form` and `Orchestra\Table` from Orchestra Platform 1.2.
* `Orchestra\Table` would automatically paginate result via `$table->with($model)`, disable it via `$table->with($model, false);`.
* `Orchestra\Form` add helper method to attach Eloquent using `$form->with($model);`.
* `Orchestra\Form::extend()` and `Orchestra\Table::extend()` should return self.
