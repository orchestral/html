Orchestra Platform Html Component
==============

Orchestra\Html extends the functionality of Illuminate\Html with the extra functionality to including a chainable Form and Table builder. These set of functionality are the backbone in allowing extensions in Orchestra Platform to attach action to any existing form or table.

[![Build Status](https://travis-ci.org/orchestral/html.png?branch=master)](https://travis-ci.org/orchestral/html)

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/html": "2.0.*"
	},
	"minimum-stability": "dev"
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

* [Documentation](http://orchestraplatform.com/docs/2.0/components/html)
* [Change Logs](https://github.com/orchestral/html/wiki/Change-Logs)
