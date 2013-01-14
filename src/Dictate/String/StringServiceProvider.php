<?php namespace Dictate\String;

use Illuminate\Support\ServiceProvider;

class StringServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['string'] = $this->app->share(function($app)
        {
            return new String($app);
        });
	}

}