---
title: HTML Change Log

---
## Version 2.2 {#v2-2}

### v2.2.0 {#v2-2-0}

* Bump minimum version to PHP v5.4.0.
* Update to use `Illuminate\View\Factory`.
* Rename Environment to Factory.

## Version 2.1 {#v2-1}

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
