<?php
namespace anlutro\Form\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Session\Store;
use Illuminate\Html\HtmlBuilder;
use anlutro\Form\Builder;
use anlutro\Form\Adapters\LaravelSessionAdapter;
use anlutro\Form\Adapters\LaravelValidationAdapter;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
	protected $session;

	protected function makeForm($class, $formBuilder = null)
	{
		$self = get_class($this);
		$namespace = substr($self, 0, strrpos($self, '\\'));
		$class = $namespace . '\\' . $class;

		return new $class($formBuilder ?: $this->makeFormBuilder());
	}

	protected function makeFormBuilder($validator = true, $session = true)
	{
		$formBuilder = new Builder();

		if ($session) {
			$this->session = new Store('test', new NullSessionHandler);
			$formBuilder->setSessionAdapter(new LaravelSessionAdapter($this->session));
		} else {
			$this->session = null;
		}

		if ($validator) {
			$this->validator = m::mock('Illuminate\Validation\Factory');
			$formBuilder->setValidationAdapter(new LaravelValidationAdapter($this->validator));
		} else {
			$this->validator = null;
		}

		return $formBuilder;
	}

	protected function mockRequest($form, array $input)
	{
		$mockRequest = m::mock('Symfony\Component\HttpFoundation\Request');
		$mockRequest->request = m::mock('Symfony\Component\HttpFoundation\ParameterBag');
		$mockRequest->request->shouldReceive('all')->andReturn($input);
		$form->getBuilder()->setRequest($mockRequest);
	}
}
