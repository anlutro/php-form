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

interface ValidationAdapterInterface
{
	/**
	 * Create a new validator instance.
	 *
	 * The returned value of this method will be passed to isValid() and getErrors().
	 *
	 * @param  AbstractForm $form
	 *
	 * @return mixed
	 */
	public function make(AbstractForm $form);

	/**
	 * Determine if validation passed or failed.
	 *
	 * @param  mixed $validator An instance returned from make().
	 *
	 * @return boolean
	 */
	public function isValid($validator);

	/**
	 * Get a validator's errors.
	 *
	 * @param  mixed $validator An instance returned from make().
	 *
	 * @return \Illuminate\Support\MessageBag
	 */
	public function getErrors($validator);
}
