<?php
namespace anlutro\LaravelForm\Tests;

use Mockery as m;

class SimpleFormTest extends TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function testOpenAndClose()
	{
		$form = $this->makeForm('SimpleFormStub');
		$this->assertContains('<form', $form->open());
		$this->assertContains('</form', $form->close());
	}

	public function testInput()
	{
		$model = new \StdClass; $model->foo = 'bar';
		$form = $this->makeForm('SimpleFormStub');
		$form->setModel($model);
		$str = $form->input('foo');
		$this->assertContains('<input', $str);
		$this->assertContains('type="text"', $str);
		$this->assertContains('name="foo"', $str);
		$this->assertContains('value="bar"', $str);
	}

	public function testLabel($value='')
	{
		$form = $this->makeForm('SimpleFormStub');
		$str = $form->label('foo', 'This is a label');
		$this->assertEquals('<label for="foo">This is a label</label>', $str);
	}
}

class SimpleFormStub extends \anlutro\LaravelForm\AbstractForm {}
