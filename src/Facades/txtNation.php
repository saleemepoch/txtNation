<?php namespace saleemepoch\txtNation\Facades;

/**
 * Class Facade
 * @package saleemepoch\txtNation\Facades
 * @see saleemepoch\txtNation\SMSMessage
 */

use Illuminate\Support\Facades\Facade;

class txtNation extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'txtNation';
	}
}
