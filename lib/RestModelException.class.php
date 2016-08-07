<?php
require_once __DIR__ . '/RestException.class.php';

/**
 * REST request
 */
class RestModelException extends RestException
{
	const OBJECT_NOT_FOUND = 'OBJECT_NOT_FOUND';
}