<?php
namespace anlutro\LaravelForm;

use Illuminate\Support\Collection;
use Illuminate\Html\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{
	/**
	 * @see getValueAttribute
	 */
	public function value($name)
	{
		return $this->getValueAttribute($name);
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

		return '<input'.$this->html->attributes($options).'>';
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

		return $errors->has($this->transformKey($name));
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
}
