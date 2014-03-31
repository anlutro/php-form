<?php
/**
 * PHP Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  php-form
 */

namespace anlutro\Form;

use Symfony\Component\HttpFoundation\Request;
use anlutro\Form\Adapters\SessionAdapterInterface;
use anlutro\Form\Adapters\ValidationAdapterInterface;
use Illuminate\Support\Str;

/**
 * Stateless form HTML builder.
 */
class Builder
{
	/**
	 * @var \anlutro\Form\Adapters\SessionAdapterInterface
	 */
	protected $session;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * @var \anlutro\Form\Adapters\ValidationAdapterInterface
	 */
	protected $validation;

	/**
	 * Set the request instance.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Set the session instance.
	 *
	 * @param \anlutro\Form\Adapters\SessionAdapterInterface $session
	 */
	public function setSessionAdapter(SessionAdapterInterface $session)
	{
		$this->session = $session;
	}

	/**
	 * Set the form builder's validation adapter.
	 *
	 * @param \anlutro\Form\Adapters\ValidationAdapterInterface $validation
	 */
	public function setValidationAdapter(ValidationAdapterInterface $validation)
	{
		$this->validation = $validation;
	}

	public function getValidationAdapter()
	{
		return $this->validation;
	}

	/**
	 * Open a form.
	 *
	 * @param  array  $attributes
	 *
	 * @return string
	 */
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

		$append = '';

		$method = strtoupper($attributes['method']);

		if ($method !== 'GET' && $method !== 'POST') {
			$attributes['method'] = 'post';
			$append .= $this->input('hidden', '_method', $method);
		}

		if ($method !== 'GET') {
			$append .= $this->token();
		}

		$attributes = $this->attributes($attributes);

		return '<form'.$attributes.'>'.$append;
	}

	/**
	 * Render an input field.
	 *
	 * @param  string $type
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function input($type, $name, $value, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $this->nameToId($attributes['name']);

		if ($type == 'textarea') {
			$attributes = $this->attributes($attributes);
			return "<textarea{$attributes}>".e($value)."</textarea>";
		} else {
			$attributes['type'] = $type;
			$attributes['value'] = $value;
			$attributes = $this->attributes($attributes);
			return '<input'.$attributes.'>';
		}
	}

	/**
	 * Render a checkbox.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  boolean $checked
	 * @param  array   $attributes
	 *
	 * @return string
	 */
	public function checkbox($name, $value = '1', $checked = false, array $attributes = array())
	{
		if ($checked) {
			$attributes['checked'] = 'checked';
		} else {
			unset($attributes['checked']);
		}

		return $this->input('checkbox', $name, $value, $attributes);
	}

	/**
	 * Render a radio input.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  boolean $checked
	 * @param  array   $attributes
	 *
	 * @return string
	 */
	public function radio($name, $value = '1', $checked = false, array $attributes = array())
	{
		if ($checked) {
			$attributes['checked'] = 'checked';
		} else {
			unset($attributes['checked']);
		}

		return $this->input('radio', $name, $value, $attributes);
	}

	/**
	 * Render a select field.
	 *
	 * @param  string $name
	 * @param  array  $options
	 * @param  mixed  $selected
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function select($name, array $options, $selected, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $this->nameToId($attributes['name']);

		$attributes = $this->attributes($attributes);
		$html = "<select{$attributes}>";

		foreach ($options as $value => $label) {
			if ($value == $selected) {
				$selectedAttribute = ' selected="selected"';
			} else {
				$selectedAttribute = '';
			}
			$html .= '<option value="'.$value.'"'.$selectedAttribute.'>'.$label.'</option>';
		}

		return $html . '</select>';
	}

	/**
	 * Render a label field.
	 *
	 * @param  string $name
	 * @param  string $text
	 * @param  array  $attributes
	 *
	 * @return string
	 */
	public function label($name, $text, array $attributes = array())
	{
		if (!isset($attributes['name'])) $attributes['name'] = $name;
		if (!isset($attributes['id'])) $attributes['id'] = $this->nameToId($attributes['name'].'__label');
		if (!isset($attributes['for'])) $attributes['for'] = $name;

		$attributes = $this->attributes($attributes);
		return "<label{$attributes}>{$text}</label>";
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
		return $this->input('submit', null, $value, $attributes);
	}

	/**
	 * Render the CSRF token input.
	 *
	 * @return string
	 */
	public function token()
	{
		if ($this->session !== null) {
			return $this->input('hidden', '_token', $this->session->getToken());
		}
	}

	/**
	 * Close a form.
	 *
	 * @return string
	 */
	public function close()
	{
		return '</form>';
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param  array  $attributes
	 * @return string
	 */
	public function attributes($attributes)
	{
		$html = array();

		foreach ((array) $attributes as $key => $value) {
			if (is_numeric($key)) {
				$key = $value;
			}

			if ($value !== null) {
				$html[] = $key.'="'.e($value).'"';
			}
		}

		return empty($html) ? '' : ' '.implode(' ', $html);
	}

	/**
	 * Determine if there is old input present in the session.
	 *
	 * @return boolean
	 */
	public function hasOldInput()
	{
		return isset($this->session) && $this->session->hasOldInput();
	}

	/**
	 * Get old input.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	public function getOldInput($name)
	{
		if (isset($this->session)) {
			return $this->session->getOldInput($this->transformKey($name));
		}
	}

	/**
	 * Transform a key from form notation to dot notation.
	 *
	 * @param  string $key
	 *
	 * @return string
	 */
	public function transformKey($key)
	{
		return str_replace(array('.', '[]', '[', ']'), array('_', '', '.', ''), $key);
	}

	/**
	 * Transform a key from form notation to snake case.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	public function nameToId($name)
	{
		return str_replace(array('.', '[]', '[', ']'), array('_', '', '_', ''), $name);
	}

	/**
	 * Get request data.
	 *
	 * @return array
	 */
	public function getRequestData()
	{
		return $this->request !== null ? $this->request->request->all() : [];
	}

	/**
	 * Make a new validator instance.
	 *
	 * @param  array  $input
	 * @param  array  $rules
	 * @param  array  $messages
	 * @param  array  $customAttributes
	 *
	 * @return \Illuminate\Validation\Validator
	 */
	public function makeValidator(array $input, array $rules, array $messages = array(), array $customAttributes = array())
	{
		if ($this->validator === null) {
			throw new \RuntimeException('Cannot make validator as validation factory is not set');
		}

		return $this->validator->make($input, $rules, $messages, $customAttributes);
	}
}
