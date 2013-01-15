Dictate
=======

A port of Laravel 3's Str class for Laravel 4.

## Installation & Configuration

###Installing with composer

Add `"dictate/string": "1.0.*"` to the `require` section of your `composer.json` file

```composer
"require": {
	"dictate\string": "1.0.*"
}
```

Run `composer install` and you are done.

###Configuration

Add the following codes to the `providers` and `aliases` section in your `app\config\app.php` file

```php
'providers' => array(
	...
	...
	'Dictate\String\StringServiceProvider',
),
```

```php
'aliases' => array(
	...
	...
	'Str'             => 'Dictate\String\StringFacade',
),
```

##Usage

The documentation can be found on the Laravel website [here](http://laravel.com/docs/strings)