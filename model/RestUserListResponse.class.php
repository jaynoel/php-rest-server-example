<?php
require_once __DIR__ . '/RestListResponse.class.php';

/**
 * User list
 */
class RestUserListResponse extends RestListResponse
{
	/**
	 * @var array<RestUser>
	 */
	public $objects;
}