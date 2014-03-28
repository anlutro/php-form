<?php
namespace anlutro\LaravelForm\Tests;

use Mockery as m;

class TransformInputTest extends TestCase
{
	/** @test */
	public function simpleTransform()
	{
		$form = $this->makeForm('TransformInputFormStub');
		$input = $form->getInput(['foo' => 'bar']);
		$this->assertEquals('Bar', $input['foo']);
	}

	/** @test */
	public function dateTimeTransform()
	{
		$form = $this->makeForm('TransformInputFormStub');
		$input = $form->getInput(['date' => '01.01.12']);
		$dt = $input['date'];
		$this->assertInstanceOf('DateTime', $dt);
		$this->assertEquals('2012-01-01', $dt->format('Y-m-d'));
	}
}

class TransformInputFormStub extends \anlutro\LaravelForm\AbstractForm
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
