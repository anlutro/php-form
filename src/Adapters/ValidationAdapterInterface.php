<?php
namespace anlutro\Form\Adapters;

use anlutro\Form\AbstractForm;

interface ValidationAdapterInterface
{
	public function make(AbstractForm $form);
	public function isValid($validator);
	public function getErrors($validator);
}
