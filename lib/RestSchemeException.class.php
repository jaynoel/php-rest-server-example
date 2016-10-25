<?php
require_once __DIR__ . '/RestException.class.php';

/**
 * REST request
 */
class RestSchemeException extends RestException
{
	const MISSING_PARAM_COMMENT = 'MISSING_PARAM_COMMENT;Missing parameter [@parameter@] comment in action [@service@.@action@];parameter,service,action';
}