<?php
/**
 * Laravel 4 Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-form
 */

namespace anlutro\LaravelForm;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
		$this->app->bindShared('anlutro\LaravelForm\Builder', function($app) {
			$builder = new Builder($app['html'], $app['validator']);

			if ($app->bound('request')) {
				$builder->setRequest($app['request']);
			}

			$app->rebinding('request', function($app, $request) {
				$app['anlutro\LaravelForm\Builder']->setRequest($request);
			});

			return $builder;
		});
	}

	public function provides()
	{
		return ['anlutro\LaravelForm\Builder'];
	}
}
