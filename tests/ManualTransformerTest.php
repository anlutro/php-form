<?php
namespace anlutro\LaravelForm\Tests;

use Mockery as m;

class ManualTransformerTest extends TestCase
{
	/** @test */
	public function outputIsTransformedCorrectly()
	{
		$form = $this->makeForm('ManualTransformFormStub');
		$form->setModel(['foo' => [1 => ['bar' => 'baz']]]);
		$this->assertEquals('BAZ1', $form->value('foo[1][bar]'));
	}
}

class ManualTransformFormStub extends \anlutro\LaravelForm\AbstractForm
{
	protected $transformers = [
		'foo.{key}.bar' => 'getTransformed',
	];

	public function getTransformed($value, $key)
	{
		return strtoupper($value) . $key;
	}
}
