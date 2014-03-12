<?php
namespace anlutro\LaravelForm\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
	protected function makeForm($class)
	{
		$self = get_class($this);
		$namespace = substr($self, 0, strrpos($self, '\\'));
		$class = $namespace . '\\' . $class;
		return new $class($this->makeFormBuilder($this->makeLaravelFormBuilder(), $this->mockValidationFactory()));
	}

	protected function makeFormBuilder($laravelForm, $valFact)
	{
		return new \anlutro\LaravelForm\Builder($laravelForm, $valFact);
	}

	protected function makeLaravelFormBuilder()
	{
		$url = $this->mockUrlGenerator();
		$html = $this->makeHtmlBuilder($url);
		$token = $this->getCsrfToken();
		return new \Illuminate\Html\FormBuilder($html, $url, $token);
	}

	protected function mockValidationFactory()
	{
		return m::mock('Illuminate\Validation\Factory');
	}

	protected function mockUrlGenerator()
	{
		$mock = m::mock('Illuminate\Routing\UrlGenerator');
		$mock->shouldReceive('current')->andReturn('/');
		return $mock;
	}

	protected function makeHtmlBuilder($url = null)
	{
		return new \Illuminate\Html\HtmlBuilder($url);
	}

	protected function getCsrfToken()
	{
		return 'foo';
	}
}
