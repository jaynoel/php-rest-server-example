<?php
require_once __DIR__ . '/../lib/RestException.class.php';

/**
 * Model exception
 */
class ModelException extends RestException
{
	const OBJECT_NOT_FOUND = 'OBJECT_NOT_FOUND;@type@ id [@id@] not found;type,id';
}