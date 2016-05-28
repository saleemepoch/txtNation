<?php namespace saleemepoch\txtNation\Providers;

/**
 * Class txtNationServiceProvider
 * @package saleemepoch\txtNation
 */

use Illuminate\Support\ServiceProvider;
use saleemepoch\txtNation\SMSMessage;

class txtNationServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Publish config files
		$this->publishes([
			__DIR__. '/../../config/config.php' => config_path('txtNation.php'),
		]);

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerTxtNation();

		$this->mergeConfig();
	}

	/**
	 * Register the application bindings.
	 *
	 * @return void
	 */
	private function registerTxtNation()
	{
		$this->app->bind('txtNation', function () {
			return new SMSMessage;
		});
	}

	/**
	 * Merges user's and txtNation's configs.
	 *
	 * @return void
	 */
	private function mergeConfig()
	{
		$this->mergeConfigFrom(
			__DIR__. '/../../config/config.php', 'txtNation'
		);
	}
}
