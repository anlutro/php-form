<?php
namespace anlutro\Form\Adapters;

use anlutro\Form\AbstractForm;
use Illuminate\Validation\Factory;

class LaravelValidationAdapter implements ValidationAdapterInterface
{
	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	public function make(AbstractForm $form)
	{
		$input = $form->getRawInput();

		if (!method_exists($form, 'getValidationRules')) {
			return true;
		}

		$rules = $form->getValidationRules();

		if (empty($rules)) {
			return true;
		}

		return $this->factory->make($input, $rules);
	}

	public function isValid($validator)
	{
		return $validator->passes();
	}

	public function getErrors($validator)
	{
		return $validator->errors();
	}
}
