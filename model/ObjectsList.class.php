<?php
require_once __DIR__ . '/../lib/RestObject.class.php';

/**
 * List base object
 */
class ObjectsList extends RestObject
{
	/**
	 * @var int
	 */
	public $totalCount;
}