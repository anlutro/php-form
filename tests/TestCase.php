<?php
namespace anlutro\LaravelForm\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
	protected function makeForm($class)
	{
		$self = get_class($this);
		$namespace = substr($self, 0, strrpos($self, '\\'));
		$class = $namespace . '\\' . $class;

		$url = $this->mockUrlGenerator();
		$html = new \Illuminate\Html\HtmlBuilder($url);

		$form = new $class($formBuilder = new \anlutro\LaravelForm\Builder(
			$html, m::mock('Illuminate\Validation\Factory')
		));

		$formBuilder->setSession($this->session = new Store('test', new NullSessionHandler));

		return $form;
	}

	protected function mockUrlGenerator()
	{
		$mock = m::mock('Illuminate\Routing\UrlGenerator');
		$mock->shouldReceive('current')->andReturn('/');
		return $mock;
	}
}
