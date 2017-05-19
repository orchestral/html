HTML Component for Orchestra Platform
==============

HTML Component extends the functionality of `Illuminate\Html` with the extra functionality to including a chainable Form and Table builder. These set of functionality are the backbone in allowing extensions in Orchestra Platform to attach action to any existing form or table.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/html.svg?style=flat-square)](https://packagist.org/packages/orchestra/html)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/html.svg?style=flat-square)](https://packagist.org/packages/orchestra/html)
[![MIT License](https://img.shields.io/packagist/l/orchestra/html.svg?style=flat-square)](https://packagist.org/packages/orchestra/html)
[![Build Status](https://img.shields.io/travis/orchestral/html/master.svg?style=flat-square)](https://travis-ci.org/orchestral/html)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/html/master.svg?style=flat-square)](https://coveralls.io/r/orchestral/html?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/html/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/orchestral/html/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Change Log](https://github.com/orchestral/html/releases)

## Version Compatibility

Laravel    | HTML
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x
 5.3.x     | 3.3.x
 5.4.x     | 3.4.x
 5.5.x     | 3.5.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "orchestra/html": "~3.0"
    }
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplified by using the following command:

    composer require "orchestra/html=~3.0"

## Configuration

Next add the service provider in `config/app.php`.

```php
'providers' => [

    // ...

    Orchestra\Html\HtmlServiceProvider::class,
],
```

### Aliases

You might want to add the following to class aliases in `config/app.php`:

```php
'aliases' => [

    // ...

    'Form' => Orchestra\Support\Facades\Form::class,
    'HTML' => Orchestra\Support\Facades\HTML::class,
    'Table' => Orchestra\Support\Facades\Table::class,
],
```

## Usage

`Orchestra\Html\HtmlBuilder` is a small improvement from `Illuminate\Html\HtmlBuilder`.

> Advise to use this only when manipulating HTML outside of view, otherwise it's better (and faster) to use html.

## Create HTML

Create a HTML tag from within your libraries/extension using following code:

```php
return HTML::create('p', 'Some awesome information');
```

Will return `<p>Some awesome information</p>`.

You can customize the HTML attibutes by adding third parameter.

```php
return HTML::create('p', 'Another awesomeness', ['id' => 'foo']);
```

Will return `<p id="foo">Another awesomeness</p>`.

## Raw HTML Entities

Mark a string to be excluded from being escaped.

```php
return HTML::link('foo', HTML::raw('<img src="foo.jpg">'));
```

Will return `<a href="foo"><img src="foo.jpg"></a>`.

This also can be dynamically done via.

```php
return HTML::link('foo', HTML::image('foo.jpg'));
```

## Decorate HTML

Decorate method allow developer to define HTML attributes collection as `HTML::attributes` method, with the addition of including default attributes array as second parameter.

```php
return HTML::decorate(['class' => 'foo'], ['id' => 'foo', 'class' => 'span5']);
```

Will return `array('class' => 'foo span5', 'id' => 'foo');`.

It also support replacement of default attributes if such requirement is needed.

```php
return HTML::decorate(['class' => 'foo !span5'], ['class' => 'bar span5']);
```

Will return `array('class' => 'foo bar');`, note that `span5` is excluded when we give `!span5` in the first parameter.

## Forms

Creating forms couldn't be any easier using Orchestra's HTML package. Let's get started.

##### Creating a new Form

To create a new form, use the `Form::of()` method. The first parameter is simply a string to define what the form is for:

```php
return Form::of('users');
```

##### Form Attributes

To customize your forms attributes, call the `attributes($attributes)` method
on the `FormGrid` instance:

```php
return Form::of('users', function ($form) {
    $attributes = [
        'method' => 'PATCH',
        'id'     => 'user-login-form',
        'class'  => 'form-horizontal',
    ];

    $form->attributes($attributes);
});
```

##### Specifying the Form layout

To specify the layout of the form, call the `layout($view)` method on the
`FormGrid` instance:

```php
return Form::of('users', function ($form) {
    $form->layout('layouts.form');
});
```

##### Adding Fields

To add fields to our form, we'll pass in a closure into the second parameter, and call the `fieldset()` method off of the
injected FormGrid. Here's an example:

```php
return Form::of('users', function ($form) {
    $form->fieldset(function ($fieldset) {
        $fieldset->control('input:text', 'username');
        $fieldset->control('input:email', 'email');
        $fieldset->control('input:password', 'password');
    });
});
```

###### Available Fields

```php
// A text field
$form->control('input:text', 'name');

// A password field
$form->control('input:password', 'name');

// A file field
$fieldset->control('input:file', 'name');

// A textarea filed
$form->control('textarea', 'name');

// A select field
$form->control('select', 'name')
    ->options(['one', 'two', 'three']);
```

##### Adding Labels to Fields

To add a label onto a form control, use the method `label()`:

```php
$form->fieldset(function ($fieldset) {
    $form->control('input:text', 'username')
        ->label('Username');

    $form->control('input:email', 'email')
        ->label('Email');

    $form->control('input:password', 'password')
        ->label('Password');
});
```

##### Adding Default Values to Fields

To add a default value to the field, use the method `value()` on the form control:

```php
$form->fieldset(function ($fieldset) {
    $form->control('input:text', 'username')
        ->label('Username')
        ->value(Auth::user()->username);

    $form->control('input:email', 'email')
        ->label('Email')
        ->value(Auth::user()->email);

    $form->control('input:password', 'password')
        ->label('Password');
});
```

##### Changing the submit button label

To change the submit button label, modify the FormGrid property `submit` like so:

```php
return Form::of('users', function ($form) {
    // The form submit button label
    $form->submit = 'Save';

    $form->fieldset(function ($fieldset) {
        $form->control('input:text', 'username');
        $form->control('input:email', 'email');
        $form->control('input:password', 'password');
    });
});
```

##### Customizing the form control attributes

To customize the form controls attributes, call the `attributes($attributes)` method
on the control:

```php
$attributes = [
    'placeholder' => 'Enter your username',
    'class'       => 'form-control',
];

$form->control('input:text', 'username')
    ->attributes($attributes);
```

##### Customizing the form control itself

```php
$form->control('input:email', 'email', function ($control) {
    $control->field(function ($row) {
        return "<input type='email' name="email" value='$row->email'>";
    });
});
```

You could also create a `Renderable` class:

```php
use Illuminate\Contracts\Support\Renderable;

class EmailAddressField implements Renderable
{
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return sprintf('<input type="email" name="%s" value="%s">', $this->name, $this->value);
    }
}
```

And you can simply register it via:

```php
$form->control('input:email', 'email', function ($control) {
    $control->field(function ($row) {
        return new EmailAddressField('email', $row->email);
    });
});
```

##### Displaying your form

To display your form, simply display it in your view with unescaped blade tags:

```php
public function index()
{
    $form = Form::of('users', function ($form) {
        $form->fieldset(function ($fieldset) {
            $form->control('input:text', 'username');
            $form->control('input:email', 'email');
            $form->control('input:password', 'password');
        });
    });

    return view('index', compact('form'));
}
```

```php
// In index.blade.php

{!! $form !!}
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/html)
