<?php
namespace anlutro\Form\Tests;

use Mockery as m;

class TransformInputTest extends TestCase
{
	/** @test */
	public function simpleTransform()
	{
		$form = $this->makeForm('TransformInputFormStub');
		$this->mockRequest($form, ['foo' => 'bar']);
		$input = $form->getInput();
		$this->assertEquals('Bar', $input['foo']);
	}

	/** @test */
	public function dateTimeTransform()
	{
		$form = $this->makeForm('TransformInputFormStub');
		$this->mockRequest($form, ['date' => '01.01.12']);
		$input = $form->getInput();
		$dt = $input['date'];
		$this->assertInstanceOf('DateTime', $dt);
		$this->assertEquals('2012-01-01', $dt->format('Y-m-d'));
	}
}

class TransformInputFormStub extends \anlutro\Form\AbstractForm
{
	public function inputFoo($value)
	{
		return ucfirst($value);
	}

	public function inputDate($value)
	{
		return \DateTime::createFromFormat('d.m.y', $value);
	}
}
