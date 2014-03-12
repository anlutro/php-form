<?php
namespace anlutro\LaravelForm;

use Illuminate\Html\HtmlBuilder;
use Illuminate\Html\FormBuilder;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class Builder
{
	protected $form;
	protected $request;
	protected $validator;

	public function __construct(FormBuilder $form, Factory $validator)
	{
		$this->form = $form;
		$this->validator = $validator;
	}

	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	public function input($type, $name, $value, array $attributes = array())
	{
		if ($type == 'textarea') {
			return $this->form->textarea($name, $value, $attributes);
		} else {
			return $this->form->input($type, $name, $value, $attributes);
		}
	}

	public function select($name, array $options, $selected, array $attributes = array())
	{
		return $this->form->select($name, $options, $selected, $attributes);
	}

	public function label($name, $text, array $attributes = array())
	{
		return $this->form->label($name, $text, $attributes);
	}

	public function open(array $attributes = array())
	{
		return $this->form->open($attributes);
	}

	public function submit($value, array $attributes = array())
	{
		return $this->form->submit($value, $attributes);
	}

	public function close()
	{
		return $this->form->close();
	}

	public function getRequestData()
	{
		return $this->request !== null ? $this->request->input() : [];
	}

	public function makeValidator(array $input, array $rules, array $messages = array(), array $customAttributes = array())
	{
		return $this->validator->make($input, $rules, $messages, $customAttributes);
	}
}
