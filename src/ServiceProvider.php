<?php
namespace anlutro\LaravelForm;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function register()
	{
		$this->app->bindShared('anlutro\LaravelForm\Builder', function($app) {
			$builder = new Builder($app['form'], $app['validator']);

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
