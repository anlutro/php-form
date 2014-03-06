<?php
namespace anlutro\LaravelForm;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class AbstractForm
{
	protected $form;
	protected $inputs = [];
	protected $transformers = [];
	protected $defaultInputType = 'text';

	public function __construct(
		Builder $form
	) {
		$this->form = $form;
	}

	public function setModel($model)
	{
		$this->model = $model;
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

	public function getInput(array $input)
	{
		foreach ($input as $key => &$value) {
			$method = 'input' . $this->nameToStudly($key);
			if (method_exists($this, $method)) {
				$value = $this->$method($value);
			}
		}

		return $input;
	}

	public function input($name, array $attributes = array())
	{
		list($type, $attributes) = $this->parseInputArgs($name, $attributes);

		return $this->form->input($type, $name, $this->getTransformedOutput($name), $attributes);
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

	protected function parseInputArgs($name, array $attributes)
	{
		if (!isset($this->inputs[$name])) {
			return [$this->defaultInputType, $attributes];
		}

		$type = $this->inputs[$name];

		if ($type === 'multiselect') {
			$attributes['multiple'] = 'multiple';
			$type = 'select';
		}

		return $type;
	}
}
