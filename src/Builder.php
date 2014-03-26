<?php
namespace anlutro\LaravelForm;

use Illuminate\Html\HtmlBuilder;
use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory;

class Builder
{
	protected $html;
	protected $session;
	protected $request;
	protected $validator;

	public function __construct(HtmlBuilder $html, Factory $validator)
	{
		$this->html = $html;
		$this->validator = $validator;
	}

	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	public function setSession(Store $session)
	{
		$this->session = $session;
	}

	public function open(array $attributes = array())
	{
		if (!isset($attributes['method'])) {
			$attributes['method'] = 'post';
		}

		$attributes['accept-charset'] = 'UTF-8';

		if (isset($attributes['files']) && $attributes['files']) {
			$attributes['enctype'] = 'multipart/form-data';
		}

		unset($attributes['files']);

		$append = $this->getAppendage($attributes);

		$attributes = $this->html->attributes($attributes);

		return '<form'.$attributes.'>'.$append;
	}

	protected function getAppendage(array $attributes)
	{
		$append = '';

		$method = strtoupper($attributes['method']);

		if ($method !== 'GET' && $method !== 'POST') {
			$append .= $this->input('hidden', '_method', $method);
		}

		if ($method !== 'GET') {
			$append .= $this->token();
		}

		return $append;
	}

	public function input($type, $name, $value, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $attributes['name'];

		if ($type == 'textarea') {
			$attributes = $this->html->attributes($attributes);
			return "<textarea $attributes>".e($value)."</textarea>";
		} else {
			$attributes['type'] = $type;
			$attributes['value'] = $value;
			$attributes = $this->html->attributes($attributes);
			return '<input '.$attributes.'>';
		}
	}

	public function checkbox($name, $checked, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $attributes['name'];

		if ($checked) {
			$attributes['checked'] = 'checked';
		} else {
			unset($attributes['checked']);
		}

		return $this->input('checkbox', $attributes);
	}

	public function select($name, array $options, $selected, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $attributes['name'];

		$attributes = $this->html->attributes($attributes);
		$html = "<select $attributes>";

		foreach ($options as $value => $label) {
			$html .= '<option value="'.$value.'">'.$label.'</option>';
		}

		return $html . '</select>';
	}

	public function label($name, $text, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $attributes['name'].'__label';
		if (!isset($attributes['for'])) $attributes['for'] = $name;

		$attributes = $this->html->attributes($attributes);
		return "<label $attributes>$text</label>";
	}

	public function submit($value = null, array $attributes = array())
	{
		return $this->input('submit', null, $value, $attributes);
	}

	public function token()
	{
		if ($this->session !== null) {
			return $this->input('hidden', '_token', $this->session->getToken());
		}
	}

	public function close()
	{
		return '</form>';
	}

	public function getOldInput($name)
	{
		if (isset($this->session)) {
			return $this->session->getOldInput($this->transformKey($name));
		}
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
