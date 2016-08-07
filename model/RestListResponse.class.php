<?php
require_once __DIR__ . '/RestObject.class.php';

/**
 * List base object
 */
class RestListResponse extends RestObject
{
	/**
	 * @var int
	 */
	public $totalCount;
}