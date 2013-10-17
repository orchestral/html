HTML Component
==============

* [Installation](#installation)
* [Configuration](#configuration)

`Orchestra\Html` extends the functionality of `Illuminate\Html` with the extra functionality to including a chainable Form and Table builder. These set of functionality are the backbone in allowing extensions in Orchestra Platform to attach action to any existing form or table.

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/html": "2.1.*@dev"
	}
}
```

## Configuration

Next add the service provider in `app/config/app.php`.

```php
'providers' => array(
	
	// ...
	# Remove 'Illuminate\Html\HtmlServiceProvider' 
	# and add 'Orchestra\Html\HtmlServiceProvider'
	
	'Orchestra\Html\HtmlServiceProvider',
),
```

> `Orchestra\Html\HtmlServiceProvider` should replace `Illuminate\Html\HtmlServiceProvider`.
