<?php
namespace anlutro\Form\Adapters;

use Illuminate\Session\Store;

class LaravelSessionAdapter implements SessionAdapterInterface
{
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
