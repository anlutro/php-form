<?php
namespace anlutro\LaravelForm\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Session\Store;
use Illuminate\Html\HtmlBuilder;
use anlutro\LaravelForm\Builder;
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
		$url = $this->mockUrlGenerator();
		$html = new HtmlBuilder($url);
		$this->validator = m::mock('Illuminate\Validation\Factory');
		$formBuilder = new Builder($html, $this->validator);
		$this->session = new Store('test', new NullSessionHandler);
		$formBuilder->setSession($this->session);
		return $formBuilder;
	}

	protected function mockUrlGenerator()
	{
		$mock = m::mock('Illuminate\Routing\UrlGenerator');
		$mock->shouldReceive('current')->andReturn('/');
		return $mock;
	}

	protected function mockRequest($form, array $input)
	{
		$mockRequest = m::mock('Illuminate\Http\Request');
		$mockRequest->shouldReceive('input')->andReturn($input);
		$form->getBuilder()->setRequest($mockRequest);
	}
}
