<?php
class SimpleForm extends anlutro\LaravelForm\AbstractForm
{
	/**
	 * Define what types of inputs your form contains. The default is text, so
	 * you can leave those out if you like.
	 *
	 * Boolean inputs like checkboxes are automatically transformed into true
	 * or false for you - no need for hidden inputs.
	 */
	protected $inputs = [
		'date' => 'text',
		'type' => 'select',
		'groups' => 'multiselect',
		'is_active' => 'checkbox',
	];

	/**
	 * A simple example - you want your model's "date" attribute to be displayed
	 * in the format of m/d/Y in the form.
	 *
	 * This method is only be called if there is no old input data for the field
	 * "date" present. The first argument is the value as retrieved from the
	 * model. Be aware that it could be null!
	 */
	public function outputDate($date)
	{
		if ($date) return $date->format('m/d/Y');
	}

	/**
	 * And when receiving a request with form data, you want it represented as
	 * as DateTime object.
	 */
	public function inputDate($input)
	{
		return DateTime::createFromFormat('m/d/Y', $input);
	}

	/**
	 * If you need nested arrays in forms, you need to map methods to fields
	 * manually as shown below. Leave out the words "input" and "output"
	 */
	protected $transformers = [
		'user[name]' => 'UserName',
		'user[friends][{key}][name]' => 'UserFriendName'
	];

	/**
	 * An example of how to implement the user[friends][key][name] field. In
	 * this case this is equivalent to the default behaviour, but you get the
	 * point.
	 *
	 * We leave out ouputUserFriendName to leave behaviour as default
	 */
	public function outputUserFriendName($key)
	{
		return $this->model->user->friends[$key]->name;
	}

	/**
	 * The form builder uses laravel's validation library under the hood. If
	 * this method returns a non-empty array, the methods isValid and getErrors
	 * will let you determine the validation state.
	 *
	 * Remember to use dot notation for nested arrays.
	 */
	public function getValidationRules()
	{
		return [
			'date' => ['required', 'date_format:m/d/Y'],
		];
	}
}
