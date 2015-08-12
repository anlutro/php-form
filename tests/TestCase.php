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

	protected function makeRequest($form, array $input, $json = false)
	{
		$body = '';
		$request = [];

		if ($json) {
			$body = json_encode($input);
		} else {
			$request = $input;
		}

		$request = new \Symfony\Component\HttpFoundation\Request([], $request, [], [], [], [], $body);
		if ($json) {
			$request->headers->set('content-type', 'application/json');
		}
		$form->getBuilder()->setRequest($request);
	}

	protected function mockRequest($form, array $input, $json = false)
	{
		return $this->makeRequest($form, $input, $json);
	}
}
