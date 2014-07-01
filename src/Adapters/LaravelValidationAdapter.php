<?php
/**
 * PHP Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  php-form
 */

namespace anlutro\Form\Adapters;

use anlutro\Form\AbstractForm;
use Illuminate\Validation\Factory;

class LaravelValidationAdapter implements ValidationAdapterInterface
{
	protected $factory;

	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * @param AbstractForm $form
	 *
	 * @return \Illuminate\Validation\Validator|null
	 */
	public function make(AbstractForm $form)
	{
		$input = $form->getRawInput();

		if (!method_exists($form, 'getValidationRules')) {
			return null;
		}

		$rules = $form->getValidationRules();

		if (empty($rules)) {
			return null;
		}

		return $this->factory->make($input, $rules);
	}

	/**
	 * @param \Illuminate\Validation\Validator $validator
	 *
	 * @return boolean
	 */
	public function isValid($validator)
	{
		return $validator->passes();
	}

	/**
	 * @param \Illuminate\Validation\Validator $validator
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getErrors($validator)
	{
		return $validator->errors();
	}
}
