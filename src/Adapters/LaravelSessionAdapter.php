<?php
/**
 * PHP Form Builder
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  php-form
 */

namespace anlutro\Form\Adapters;

use Illuminate\Session\Store;

class LaravelSessionAdapter implements SessionAdapterInterface
{
	protected $session;

	public function __construct(Store $session)
	{
		$this->session = $session;
	}

	public function hasOldInput()
	{
		return $this->session->hasOldInput();
	}

	public function getOldInput($key = null)
	{
		return $this->session->getOldInput($key);
	}

	public function getToken()
	{
		return $this->session->getToken();
	}
}
