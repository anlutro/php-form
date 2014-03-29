<?php
namespace anlutro\Form\Adapters;

interface SessionAdapterInterface
{
	public function hasOldInput();
	public function getOldInput($key = null);
	public function getToken();
}
