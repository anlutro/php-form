<?php
/**
 * PHP Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  php-form
 */

namespace anlutro\Form\Adapters;

interface SessionAdapterInterface
{
	/**
	 * Determine if the session has any old input.
	 *
	 * @return boolean
	 */
	public function hasOldInput();

	/**
	 * Get old input from the session.
	 *
	 * @param string|null $key Name of the input. If null, return all old input.
	 *
	 * @return mixed
	 */
	public function getOldInput($key = null);

	/**
	 * Get the session's CSRF token.
	 *
	 * @return mixed
	 */
	public function getToken();
}
