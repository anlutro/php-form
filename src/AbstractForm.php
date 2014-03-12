<?php
namespace anlutro\LaravelForm;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class AbstractForm
{
	protected $form;
	protected $input;
	protected $model;
	protected $validator;
	protected $inputs = [];
	protected $transformers = [];
	protected $defaultInputType = 'text';

	public function __construct(Builder $form)
	{
		$this->form = $form;
	}

	public function setModel($model)
	{
		$this->model = $model;
	}

	public function value($name)
	{
		return $this->form->getOldInput($name) ?: $this->getTransformedOutput($name);
	}

	public function getTransformedOutput($name)
	{
		$data = $this->getValueFromModel($name);

		$method = 'output' . $this->nameToStudly($name);
		if (method_exists($this, $method)) {
			$data = $this->$method($data);
		}

		return $data;
	}

	protected function nameToStudly($name)
	{
		$snake = str_replace(array('.', '[]', '[', ']'), array('_', '', '_', ''), $name);
		return Str::studly($snake);
	}

	public function getValueFromModel($name)
	{
		if ($this->model === null) return null;
		$segments = explode('.', $this->transformKey($name));
		$data = $this->model;

		foreach ($segments as $key) {
			if (is_array($data)) {
				$data = array_key_exists($key, $data) ? $data[$key] : null;
			} elseif ($data instanceof Collection) {
				$data = $data->find($key);
			} elseif ($data instanceof \ArrayAccess) {
				$data = $data->offsetExists($key) ? $data->offsetGet($key) : null;
			} elseif (is_object($data)) {
				$data = isset($data->$key) ? $data->$key : null;
			} else {
				return $data;
			}
		}

		return $data;
	}

	public function transformKey($key)
	{
		return str_replace(array('.', '[]', '[', ']'), array('_', '', '.', ''), $key);
	}

	protected function checkInputSet()
	{
		if ($this->input === null) {
			$this->input = $this->form->getRequestData();
		}
	}

	public function getInput($input = null)
	{
		if ($input === null) {
			$this->checkInputSet();
			$input = $this->input;
		}

		// look for input methods to convert input
		foreach ($input as $key => &$value) {
			$method = 'input' . $this->nameToStudly($key);
			if (method_exists($this, $method)) {
				$value = $this->$method($value);
			}
		}

		// convert special input types
		foreach ($this->inputs as $key => $type) {
			if ($type == 'append') {
				$method = 'input' . Str::studly($key);
				$input[$key] = $this->$method();
			}

			if ($type == 'checkbox') {
				$input[$key] = array_key_exists($key, $input);
			} elseif ($type == 'checkboxes') {
				$input[$key] = array_key_exists($key, $input) ? $input[$key] : array();
			}
		}

		return $input;
	}

	public function input($name, array $attributes = array())
	{
		list($type, $attributes) = $this->parseInputArgs($name, $attributes);

		return $this->form->input($type, $name, $this->getTransformedOutput($name), $attributes);
	}

	public function checkbox($name, array $attributes = array())
	{
		list($type, $attributes) = $this->parseInputArgs($name, $attributes);

		return $this->form->checkbox($name, $this->getTransformedOutput($name), $attributes);
	}

	public function select($name, array $attributes = array())
	{
		$method = 'get' . Str::studly($name) . 'Options';
		$options = $this->$method();
		$selected = $this->getValueFromModel($name);
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

	protected function parseInputArgs($name, array $attributes)
	{
		if (!isset($this->inputs[$name])) {
			return [$this->defaultInputType, $attributes];
		}

		$type = $this->inputs[$name];

		if ($type == 'append') {
			throw new \InvalidArgumentException("Input name [$name] has type [$type] and cannot be rendered in the form.");
		}

		if ($type == 'multiselect') {
			$attributes['multiple'] = 'multiple';
			$type = 'select';
		}

		return [$type, $attributes];
	}

	public function isValid($input = null)
	{
		if (!$rules = $this->getValidationRules()) return true;

		if ($input === null) {
			$this->checkInputSet();
			$input = $this->input;
		}

		if ($this->validator === null) {
			$this->validator = $this->form->makeValidator($input, $rules);
		}

		return $this->validator->passes();
	}

	public function getErrors()
	{
		return $this->validator->errors();
	}

	public function getValidationRules() {}
}
