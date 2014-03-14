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
	 * Sometimes you need custom behaviour that can't easily be mapped to
	 * methods automatically. Transformers to the rescue!
	 */
	protected $transformers = [
		'products.{key}.price' => 'getProductPrice',
	];

	/**
	 * This method gets called every time the form wants to output an input
	 * like products[5][price], with 5 being given as an argument. In this
	 * example we utilize this to access pivot data as well as a fallback.
	 */
	public function getProductPrice($key)
	{
		$product = $this->model->products->find($key);
		return $product->pivot->price ?: $product->default_price;
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
