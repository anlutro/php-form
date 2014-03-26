<?php
namespace anlutro\LaravelForm;

use Illuminate\Support\Collection;
use Illuminate\Html\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
	protected $errorClass;

	/**
	 * @see getValueAttribute
	 */
	public function value($name)
	{
		return $this->getValueAttribute($name);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function checkable($type, $name, $value, $checked, $options)
	{
		if ($checked === null) {
			$checked = $this->getCheckedState($type, $name, $value, $checked);
		}

		if ($checked) $options['checked'] = 'checked';

		return $this->input($type, $name, $value, $options);
	}

	/**
	 * Returns the 'checked' string if the input with the given name is checked.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	public function checked($name)
	{
		if ($this->getCheckedState($name)) {
			return 'checked';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getModelValueAttribute($name)
	{
		$key = $this->transformKey($name);

		$data = $this->model;

		foreach (explode('.', $key) as $segment) {
			if (is_array($data)) {
				$data = array_get($data, $segment);
			} elseif ($data instanceof \ArrayAccess) {
				if (!$data->offsetExists($segment)) return;
				$data = $data->offsetGet($segment);
			} elseif (is_object($data)) {
				$data = object_get($data, $segment);
			} else {
				return $data;
			}
		}

		return $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function input($type, $name, $value = null, $options = array())
	{
		if (!isset($options['name'])) {
			$options['name'] = $name;
		}

		$id = $this->getIdAttribute($name, $options);

		if (!in_array($type, $this->skipValueTypes)) {
			$value = $this->getValueAttribute($name, $value);
		}

		$merge = compact('type', 'value', 'id');

		$options = array_merge($options, $merge);

		if ($this->hasErrors($name)) {
			$class = $this->getErrorClass($type, $name);
			if (isset($options['class'])) {
				$options['class'] .= ' '.$class;
			} else {
				$options['class'] = $class;
			}
		}

		return '<input' . $this->html->attributes($options) . '>';
	}

	public function group($name, \Closure $callback)
	{
		return $this->openGroup($name) . $callback() . $this->closeGroup();
	}

	/**
	 * Open a form group (Bootstrap 3)
	 *
	 * @param  string $name
	 * @param  string $class
	 *
	 * @return string
	 */
	public function openGroup($name = null, $class = null)
	{
		$attributes['class'] = $class ? $class . ' form-group' : 'form-group';

		if ($name && $this->hasErrors($name)) {
			$attributes['class'] .= ' ' . $this->getErrorClass(null, $name);
		}

		return '<div '.$this->html->attributes($attributes).'>';
	}

	/**
	 * Close a form group (Bootstrap 3)
	 *
	 * @return string
	 */
	public function closeGroup()
	{
		return '</div>';
	}

	/**
	 * Get the error class for a specific input.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	public function error($name)
	{
		if ($this->hasErrors($name)) {
			return $this->getErrorClass();
		}
	}

	/**
	 * Determine if a certain input has errors associated with it.
	 *
	 * @param  string  $name
	 *
	 * @return boolean
	 */
	protected function hasErrors($name)
	{
		if (
			$this->errorClass === null ||
			$this->session === null ||
			!$this->session->has('errors')
		) {
			return false;
		}

		/**
		 * @var Illuminate\Support\MessageBag
		 */
		$errors = $this->session->get('errors');

		if (is_array($name)) {
			foreach ($name as $key) {
				if ($errors->has($this->transformKey($key))) return true;
			}
			return false;
		} else {
			return $errors->has($this->transformKey($name));
		}
	}

	/**
	 * Get the error class to use for inputs that have errors.
	 *
	 * @param  string $type
	 * @param  string $name
	 *
	 * @return string
	 */
	protected function getErrorClass($type = null, $name = null)
	{
		if ($this->errorClass instanceof \Closure) {
			$callback = $this->errorClass;
			return $callback($type, $name);
		} else {
			return $this->errorClass;
		}
	}

	/**
	 * Set the error class to be used for inuts that have errors.
	 *
	 * @param string|Closure $class
	 */
	public function setErrorClass($class)
	{
		$this->errorClass = $class;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIdAttribute($name, $attributes)
	{
		return array_key_exists('id', $attributes)
			? $attributes['id'] : $name;
	}
}
