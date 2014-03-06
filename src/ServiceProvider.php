<?php
namespace anlutro\LaravelForm;

class ServiceProvider
{
	protected $defer = true;

	public function register()
	{
		$this->app->bindShared('anlutro\LaravelForm\Builder');
	}

	public function provides()
	{
		return ['anlutro\LaravelForm\Builder'];
	}
}
