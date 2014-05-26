Orchestra Platform Html Component
==============

`Orchestra\Html` extends the functionality of Illuminate\Html with the extra functionality to including a chainable Form and Table builder. These set of functionality are the backbone in allowing extensions in Orchestra Platform to attach action to any existing form or table.

[![Latest Stable Version](https://poser.pugx.org/orchestra/html/v/stable.png)](https://packagist.org/packages/orchestra/html) 
[![Total Downloads](https://poser.pugx.org/orchestra/html/downloads.png)](https://packagist.org/packages/orchestra/html) 
[![Build Status](https://travis-ci.org/orchestral/html.svg?branch=2.2)](https://travis-ci.org/orchestral/html) 
[![Coverage Status](https://coveralls.io/repos/orchestral/html/badge.png?branch=2.2)](https://coveralls.io/r/orchestral/html?branch=2.2) 
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/orchestral/html/badges/quality-score.png?s=8f6fa828398be2155999264f1979d557f9300f3d)](https://scrutinizer-ci.com/g/orchestral/html/) 

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/html": "2.2.*"
	}
}
```

Next add the service provider in `app/config/app.php`.

```php
'providers' => array(

	// ...

	'Orchestra\Html\HtmlServiceProvider',
),
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/html)
* [Change Log](http://orchestraplatform.com/docs/latest/components/html/changes#v2-2)
