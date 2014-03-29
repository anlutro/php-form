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

	protected function makeForm($class)
	{
		$self = get_class($this);
		$namespace = substr($self, 0, strrpos($self, '\\'));
		$class = $namespace . '\\' . $class;

		return new $class($this->makeFormBuilder());
	}

	protected function makeFormBuilder()
	{
		$formBuilder = new Builder();
		$this->session = new Store('test', new NullSessionHandler);
		$formBuilder->setSession(new LaravelSessionAdapter($this->session));
		$this->validator = m::mock('Illuminate\Validation\Factory');
		$formBuilder->setValidationAdapter(new LaravelValidationAdapter($this->validator));
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
