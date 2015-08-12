<?php
namespace anlutro\Form\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;

class BuilderTest extends TestCase
{
	/** @test */
	public function decodesJson()
	{
		$builder = $this->makeFormBuilder();
		$request = new \Symfony\Component\HttpFoundation\Request([], [], [], [], [], [], '{"foo":"bar"}');
		$request->headers->set('content-type', 'application/json');
		$builder->setRequest($request);
		$this->assertEquals(['foo' => 'bar'], $builder->getRequestData());
	}
}
