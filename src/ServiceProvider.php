<?php
namespace anlutro\LaravelForm;

class ServiceProvider
{
	protected $defer = true;

	public function register()
	{
		$this->app->bindShared('anlutro\LaravelForm\Builder', function($app) {
			$builder = new Builder($app['form'], $app['validator']);
			if ($app->bound('request')) {
				$builder->setRequest($request);
			}
		});

		$this->app->rebinding('request', function($app, $request) {
			$app['anlutro\LaravelForm\Builder']->setRequest($request);
		});
	}

	public function provides()
	{
		return ['anlutro\LaravelForm\Builder'];
	}
}
