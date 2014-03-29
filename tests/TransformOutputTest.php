<?php
namespace anlutro\Form\Tests;

use Mockery as m;

class Test extends TestCase
{
	/** @test */
	public function simpleTransform()
	{
		$model = new \StdClass; $model->foo = 'bar';
		$form = $this->makeForm('TransformOutputFormStub');
		$form->setModel($model);
		$str = $form->input('foo');
		$this->assertContains('value="BAR"', $str);
	}
}

class TransformOutputFormStub extends \anlutro\Form\AbstractForm
{
	public function outputFoo($value)
	{
		return strtoupper($value);
	}
}
