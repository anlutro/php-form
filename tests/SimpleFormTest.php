<?php
namespace anlutro\LaravelForm\Tests;

use Mockery as m;

class SimpleFormTest extends TestCase
{
	public function tearDown()
	{
		m::close();
	}

	/** @test */
	public function openAndClose()
	{
		$form = $this->makeForm('SimpleFormStub');
		$this->assertContains('<form', $form->open());
		$this->assertContains('</form', $form->close());
	}

	/** @test */
	public function submit()
	{
		$form = $this->makeForm('SimpleFormStub');
		$this->assertContains('<input', $form->submit());
		$this->assertContains('type="submit"', $form->submit());
	}

	/** @test */
	public function input()
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

	/** @test */
	public function label()
	{
		$form = $this->makeForm('SimpleFormStub');
		$str = $form->label('foo', 'This is a label');
		$this->assertContains('<label', $str);
		$this->assertContains('for="foo"', $str);
		$this->assertContains('>This is a label</label>', $str);
	}

	/** @test */
	public function inputType()
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

	/** @test */
	public function select()
	{
		$form = $this->makeForm('FormSelectStub');
		$str = $form->select('foo');
		$this->assertContains('<select', $str);
		$this->assertContains('name="foo"', $str);
		$this->assertContains('<option value="1">opt1', $str);
		$this->assertContains('<option value="2">opt2', $str);
	}

	/** @test */
	public function selectWithSelected()
	{
		$form = $this->makeForm('FormSelectStub');
		$form->setModel(['foo' => 2]);
		$str = $form->select('foo');
		$this->assertContains('<select', $str);
		$this->assertContains('name="foo"', $str);
		$this->assertContains('<option value="1">opt1', $str);
		$this->assertContains('<option value="2" selected="selected">opt2', $str);
	}

	/** @test */
	public function checkbox()
	{
		$form = $this->makeForm('SimpleFormStub');
		$str = $form->checkbox('checkbox[1]');
		$this->assertContains('<input', $str);
		$this->assertContains('name="checkbox[1]"', $str);
		$this->assertContains('type="checkbox"', $str);
	}

	/** @test */
	public function checkboxChecked()
	{
		$form = $this->makeForm('SimpleFormStub');
		$form->setModel(['checkbox' => [1 => true, 2 => false]]);
		$str = $form->checkbox('checkbox[1]');
		$this->assertContains('checked="checked"', $str);
		$str = $form->checkbox('checkbox[2]');
		$this->assertNotContains('checked="checked"', $str);
	}

	/** @test */
	public function checkboxInput()
	{
		$form = $this->makeForm('CheckboxFormStub');
		$input = $form->getInput([]);
		$this->assertEquals(['checkbox' => false], $input);
		$input = $form->getInput(['checkbox' => 1]);
		$this->assertEquals(['checkbox' => 1], $input);
	}

	/** @test */
	public function checkboxesInput()
	{
		$form = $this->makeForm('CheckboxesFormStub');
		$input = $form->getInput([]);
		$this->assertEquals(['checkboxes' => []], $input);
		$input = $form->getInput(['checkboxes' => [1 => 1, 3 => 1]]);
		$this->assertEquals(['checkboxes' => [1 => 1, 3 => 1]], $input);
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
class CheckboxFormStub extends \anlutro\LaravelForm\AbstractForm
{
	protected $inputs = ['checkbox' => 'checkbox'];
}
class CheckboxesFormStub extends \anlutro\LaravelForm\AbstractForm
{
	protected $inputs = ['checkboxes' => 'checkboxes'];
}
