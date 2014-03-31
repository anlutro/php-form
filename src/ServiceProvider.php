<?php
/**
 * PHP Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  php-form
 */

namespace anlutro\Form;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	protected $defer = true;

	public function register()
	{
		$this->app->bind(
			'anlutro\Form\Adapters\SessionAdapterInterface',
			'anlutro\Form\Adapters\LaravelSessionAdapter'
		);

		$this->app->bind(
			'anlutro\Form\Adapters\ValidationAdapterInterface',
			'anlutro\Form\Adapters\LaravelValidationAdapter'
		);

		$this->app->bindShared('anlutro\Form\Builder', function($app)
		{
			$builder = new Builder();

			$builder->setSessionAdapter($app->make('anlutro\Form\Adapters\SessionAdapterInterface'));

			// validation service provider is deferred so we have to do this
			// instead of $app->bound('validator')
			if (in_array(
				'Illuminate\Validation\ValidationServiceProvider',
				$app['config']->get('app.providers'))
			) {
				$builder->setValidationAdapter($app->make('anlutro\Form\Adapters\ValidationAdapterInterface'));
			}

			// set the current request if there is one
			if ($app->bound('request')) {
				$builder->setRequest($app['request']);
			}

			// set the request again whenever the request is refreshed
			$app->rebinding('request', function($app, $request) {
				$app['anlutro\Form\Builder']->setRequest($request);
			});

			return $builder;
		});
	}

	public function provides()
	{
		return ['anlutro\Form\Builder'];
	}
}
