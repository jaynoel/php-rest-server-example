<?php
require_once __DIR__ . '/RestException.class.php';

/**
 * REST request
 */
class RestRequestException extends RestException
{
	const CONTROLLER_NOT_FOUND = 'CONTROLLER_NOT_FOUND';
	const ACTION_NOT_FOUND = 'ACTION_NOT_FOUND';
	const INVALID_JSON = 'INVALID_JSON';
	const MISSING_PARAMETER = 'MISSING_PARAMETER';
	const PARAMETER_WRONG_TYPE = 'PARAMETER_WRONG_TYPE';
	const ABSTRACT_TYPE = 'ABSTRACT_TYPE';
	const INVALID_MULTIREQUEST_TOKEN = 'INVALID_MULTIREQUEST_TOKEN';
}