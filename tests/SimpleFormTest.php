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

	public function testInputType()
	{
		$form = $this->makeForm('FormInputTypeStub');
		$str = $form->input('foo');
		$this->assertContains('<input', $str);
		$this->assertContains('name="foo"', $str);
		$this->assertContains('type="checkbox"', $str);
		$str = $form->input('bar');
		$this->assertContains('<textarea', $str);
		$this->assertContains('name="bar"', $str);
	}

	public function testSelect()
	{
		$form = $this->makeForm('FormSelectStub');
		$str = $form->select('foo');
		$this->assertContains('<select', $str);
		$this->assertContains('name="foo"', $str);
		$this->assertContains('<option value="1">opt1', $str);
		$this->assertContains('<option value="2">opt2', $str);
	}
}

class SimpleFormStub extends \anlutro\LaravelForm\AbstractForm {}
class FormInputTypeStub extends \anlutro\LaravelForm\AbstractForm {
	protected $inputs = ['foo' => 'checkbox', 'bar' => 'textarea'];
}
class FormSelectStub extends \anlutro\LaravelForm\AbstractForm {
	public function getFooOptions()
	{
		return [1 => 'opt1', 2 => 'opt2'];
	}
}
