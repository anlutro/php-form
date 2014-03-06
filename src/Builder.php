<?php
namespace anlutro\LaravelForm;

use Illuminate\Html\HtmlBuilder;
use Illuminate\Html\FormBuilder;
use Illuminate\Session\Store;

class Builder
{
	public function __construct(
		FormBuilder $form
	) {
		$this->form = $form;
	}

	public function input($type, $name, $value, array $attributes = array())
	{
		return $this->form->input($type, $name, $value, $attributes);
	}

	public function label($name, $text, array $attributes = array())
	{
		return $this->form->label($name, $text, $attributes);
	}

	public function open(array $attributes = array())
	{
		return $this->form->open($attributes);
	}

	public function close()
	{
		return $this->form->close();
	}
}
