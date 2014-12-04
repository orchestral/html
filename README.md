HTML Component for Orchestra Platform
==============

HTML Component extends the functionality of `Illuminate\Html` with the extra functionality to including a chainable Form and Table builder. These set of functionality are the backbone in allowing extensions in Orchestra Platform to attach action to any existing form or table.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/html.svg?style=flat)](https://packagist.org/packages/orchestra/html)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/html.svg?style=flat)](https://packagist.org/packages/orchestra/html)
[![MIT License](https://img.shields.io/packagist/l/orchestra/html.svg?style=flat)](https://packagist.org/packages/orchestra/html)
[![Build Status](https://img.shields.io/travis/orchestral/html/master.svg?style=flat)](https://travis-ci.org/orchestral/html)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/html/master.svg?style=flat)](https://coveralls.io/r/orchestral/html?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/html/master.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/html/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
* [Change Log](http://orchestraplatform.com/docs/latest/components/html/changes#v3-0)

## Version Compatibility

Laravel    | HTML
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/html": "3.0.*"
	}
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

```bash
composer require "orchestra/html=3.0.*"
```

## Configuration

Next add the service provider in `app/config/app.php`.

```php
'providers' => array(

	// ...

	'Orchestra\Html\HtmlServiceProvider',
),
```

### Aliases

You might want to add the following to class aliases in `app/config/app.php`:

```php
'aliases' => array(

	// ...

	'Orchestra\Form' => 'Orchestra\Support\Facades\Form',
	'Orchestra\Table' => 'Orchestra\Support\Facades\Table',
),
```

## Usage

`Orchestra\Html\HtmlBuilder` is a small improvement from `Illuminate\Html\HtmlBuilder`.

> Advise to use this only when manipulating HTML outside of view, otherwise it's better (and faster) to use html.

## Create HTML

Create a HTML tag from within your libraries/extension using following code:

```php
return HTML::create('p', 'Some awesome information');

// will return <p>Some awesome information</p>
```

You can customize the HTML attibutes by adding third parameter.

```php
return HTML::create('p', 'Another awesomeness', ['id' => 'foo']);

// will return <p id="foo">Another awesomeness</p>
```

## Raw HTML Entities

Mark a string to be excluded from being escaped.

```php
return HTML::link('foo', HTML::raw('<img src="foo.jpg">'));

// will return <a href="foo"><img src="foo.jpg"></a>
```

## Decorate HTML

Decorate method allow developer to define HTML attributes collection as `HTML::attributes` method, with the addition of including default attributes array as second parameter.

```php
return HTML::decorate(['class' => 'foo'], ['id' => 'foo', 'class' => 'span5']);

// will return array('class' => 'foo span5', 'id' => 'foo');
```

It also support replacement of default attributes if such requirement is needed.

```php
return HTML::decorate(['class' => 'foo !span5'], ['class' => 'bar span5']);

// will return array('class' => 'foo bar');
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/html)
