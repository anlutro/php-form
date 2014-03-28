<?php
/**
 * Laravel 4 Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-form
 */

namespace anlutro\LaravelForm;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class representation of a form.
 */
class AbstractForm
{
	/**
	 * @var \anlutro\LaravelForm\Builder
	 */
	protected $form;

	/**
	 * The input from the current request.
	 *
	 * @var array
	 */
	protected $input;

	/**
	 * The form model data.
	 *
	 * @var mixed
	 */
	protected $model;

	/**
	 * The form action URL.
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * The validation factory.
	 *
	 * @var \Illuminate\Validation\Factory
	 */
	protected $validator;

	/**
	 * The inputs defined on the form.
	 *
	 * @var array
	 */
	protected $inputs = [];

	/**
	 * Manually mapped input/output transformers.
	 *
	 * @var array
	 */
	protected $transformers = [];

	/**
	 * The default input type.
	 *
	 * @var string
	 */
	protected $defaultInputType = 'text';

	/**
	 * @param \anlutro\LaravelForm\Builder $form
	 */
	public function __construct(Builder $form)
	{
		$this->form = $form;
	}

	/**
	 * Set the form model.
	 *
	 * @param mixed $model
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

	/**
	 * Set the form action.
	 *
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * Get the value of an input field.
	 *
	 * @param  string $name Name of the input
	 *
	 * @return mixed
	 */
	public function value($name)
	{
		return $this->form->getOldInput($name) ?: $this->getTransformedOutput($name);
	}

	/**
	 * Get the form's input.
	 *
	 * @param  array $input If left out, fetches input from the request.
	 *
	 * @return array
	 */
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

	/**
	 * Render an input field.
	 *
	 * @param  string $name
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function input($name, array $attributes = array())
	{
		list($type, $attributes) = $this->parseInputArgs($name, $attributes);

		return $this->form->input($type, $name, $this->getTransformedOutput($name), $attributes);
	}

	/**
	 * Render a checkbox.
	 *
	 * @param  string $name
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function checkbox($name, $value = '1', array $attributes = array())
	{
		list($type, $attributes) = $this->parseInputArgs($name, $attributes);

		return $this->form->checkbox($name, $value, (bool) $this->getTransformedOutput($name), $attributes);
	}

	/**
	 * Render a select input.
	 *
	 * Options are fetched from the form's get{name}Options() method.
	 *
	 * @param  string $name
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function select($name, array $attributes = array())
	{
		$method = 'get' . Str::studly($name) . 'Options';
		$options = $this->$method();
		$selected = $this->getValueFromModel($name);
		return $this->form->select($name, $options, $selected, $attributes);
	}

	/**
	 * Render a form label.
	 *
	 * @param  string $name
	 * @param  string $text
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function label($name, $text, array $attributes = array())
	{
		return $this->form->label($name, $text, $attributes);
	}

	/**
	 * Open the form.
	 *
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function open(array $attributes = array())
	{
		if ($this->action !== null) {
			$attributes['action'] = $this->action;
		}

		return $this->form->open($attributes);
	}

	/**
	 * Render a submit button.
	 *
	 * @param  string $value
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function submit($value = null, array $attributes = array())
	{
		return $this->form->submit($value, $attributes);
	}

	/**
	 * Close the form.
	 *
	 * @return string
	 */
	public function close()
	{
		return $this->form->close();
	}

	/**
	 * Determine if input is valid.
	 *
	 * @param  array   $input  Leave out to fetch from the current request
	 *
	 * @return boolean
	 */
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

	/**
	 * Get validation errors.
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getErrors()
	{
		return $this->validator->errors();
	}

	/**
	 * Get the form's validation rules.
	 *
	 * @return array
	 */
	public function getValidationRules() {}

	/**
	 * Get the transformed output of an input field.
	 *
	 * @param  string $name
	 *
	 * @return mixed
	 */
	protected function getTransformedOutput($name)
	{
		$key = $this->transformKey($name);

		if ($args = $this->hasTransformer($key)) {
			return $this->callTransformer($args);
		}

		$data = $this->getValueFromModel($key);

		$method = 'output' . $this->nameToStudly($name);
		if (method_exists($this, $method)) {
			$data = $this->$method($data);
		}

		return $data;
	}

	/**
	 * Transform an input name from form name to StudlyCase.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	protected function nameToStudly($name)
	{
		$snake = str_replace(array('.', '[]', '[', ']'), array('_', '', '_', ''), $name);
		return Str::studly($snake);
	}

	/**
	 * Get the value of an input from the model.
	 *
	 * @param  string $name
	 *
	 * @return mixed
	 */
	protected function getValueFromModel($name)
	{
		if ($this->model === null) return null;

		$segments = explode('.', $name);

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

	/**
	 * Determine if a field has a transformer or not.
	 *
	 * @param  string  $match
	 *
	 * @return array|null     @todo
	 */
	protected function hasTransformer($match)
	{
		foreach ($this->transformers as $key => $value) {
			$pattern = '/'.preg_replace('/\{(\w+?)\}/', '(.+)', str_replace('.', '\.', $key)).'/';
			if (preg_match($pattern, $match, $matches)) {
				$matches[0] = $this->getValueFromModel($match);
				return [$key, $matches];
			}
		}
	}

	/**
	 * Call a transformer method.
	 *
	 * @param  array $args
	 *
	 * @return mixed
	 */
	protected function callTransformer($args)
	{
		$method = $this->transformers[$args[0]];
		return call_user_func_array([$this, $method], $args[1]);
	}

	/**
	 * Transform a key from form notation to dot notation.
	 *
	 * @param  string $key
	 *
	 * @return string
	 */
	protected function transformKey($key)
	{
		return str_replace(array('.', '[]', '[', ']'), array('_', '', '.', ''), $key);
	}

	/**
	 * Ensure that input is set.
	 *
	 * @return void
	 */
	protected function checkInputSet()
	{
		if ($this->input === null) {
			$this->input = $this->form->getRequestData();
		}
	}

	/**
	 * Parse the arguments of an input method.
	 *
	 * @param  string $name
	 * @param  array  $attributes
	 *
	 * @return array  [input type, attributes]
	 */
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
}
