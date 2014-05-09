<?php
namespace anlutro\Form\Tests;

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
		$form->setAction('http://www.foo.com');
		$this->assertContains('action="http://www.foo.com"', $form->open());
		$form->setMethod('post');
		$this->assertContains('method="post"', $form->open());
		$form->setMethod('get');
		$this->assertContains('method="get"', $form->open());
	}

	/** @test */
	public function openWithFilesTrue()
	{
		$form = $this->makeForm('SimpleFormStub');
		$str = $form->open(['files' => true]);
		$this->assertContains('<form method="post" accept-charset="UTF-8" enctype="multipart/form-data">', $str);
	}

	/** @test */
	public function openWithSpoofedMethod()
	{
		$form = $this->makeForm('SimpleFormStub');
		$str = $form->open(['method' => 'delete']);
		$this->assertContains('method="post"', $str);
		$this->assertContains('<input name="_method" id="_method" type="hidden" value="DELETE">', $str);
	}

	/** @test */
	public function openContainsCsrfToken()
	{
		$form = $this->makeForm('SimpleFormStub');
		$this->session->start();
		$str = 'input name="_token" id="_token" type="hidden"';
		$this->assertContains($str, $form->open(['method' => 'post']));
		$this->assertContains($this->session->getToken(), $form->open(['method' => 'post']));
		$this->assertContains($str, $form->open(['method' => 'delete']));
		$this->assertContains($str, $form->open(['method' => 'post']));
		$this->assertContains($str, $form->open(['method' => 'patch']));
		$this->assertNotContains($str, $form->open(['method' => 'get']));
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

	/** @test */
	public function oldInput()
	{
		$form = $this->makeForm('SimpleFormStub');
		$form->setModel(['foo' => 'baz']);
		$this->session->flashInput(['foo' => 'bar']);
		$this->assertContains('value="bar"', $form->input('foo'));
	}

	/** @test */
	public function oldInputAndCheckboxes()
	{
		$form = $this->makeForm('CheckboxesFormStub');
		$form->setModel(['checkboxes' => [1 => false, 2 => true, 3 => false]]);
		$this->session->flashInput(['checkboxes' => [1 => '1', 3 => '1']]);
		$this->assertContains('checked', $form->checkbox('checkboxes[1]'));
		$this->assertNotContains('checked', $form->checkbox('checkboxes[2]'));
		$this->assertContains('checked', $form->checkbox('checkboxes[3]'));
	}

	/** @test */
	public function nesteObjectsAndArrays()
	{
		$obj = new \StdClass;
		$obj->stuff = new \Illuminate\Support\Collection([5 => ['bar' => 'baz']]);
		$model = ['foo' => $obj];
		$form = $this->makeForm('SimpleFormStub');
		$form->setModel($model);
		$this->assertContains('value="baz"', $form->input('foo[stuff][5][bar]'));
	}
}

class SimpleFormStub extends \anlutro\Form\AbstractForm {}
class FormInputTypeStub extends \anlutro\Form\AbstractForm {
	protected $inputs = ['foo' => 'checkbox', 'bar' => 'textarea'];
}
class FormSelectStub extends \anlutro\Form\AbstractForm {
	public function getFooOptions()
	{
		return [1 => 'opt1', 2 => 'opt2'];
	}
}
class CheckboxFormStub extends \anlutro\Form\AbstractForm
{
	protected $inputs = ['checkbox' => 'checkbox'];
}
class CheckboxesFormStub extends \anlutro\Form\AbstractForm
{
	protected $inputs = ['checkboxes' => 'checkboxes'];
}
