<?php
require_once __DIR__ . '/RestException.class.php';

/**
 * REST request
 */
class RestRequestException extends RestException
{
	const SERVICE_NOT_DEFINED = 'SERVICE_NOT_DEFINED;No service defined';
	const SERVICE_NOT_FOUND = 'SERVICE_NOT_FOUND;Service [@service@] not found;service';
	const ACTION_NOT_FOUND = 'ACTION_NOT_FOUND;Action [@service@.@action@] not found;service,action';
	const INVALID_JSON = 'INVALID_JSON;Invalid JSON';
	const MISSING_PARAMETER = 'MISSING_PARAMETER;Missing parameter [@parameter@];parameter';
	const PARAMETER_WRONG_TYPE = 'PARAMETER_WRONG_TYPE;Wrong parameter [@parameter@] type, expected to be [@type@];parameter,type';
	const ABSTRACT_TYPE = 'ABSTRACT_TYPE;Type [@type@] is abstract and should not be used;type';
	const INVALID_MULTIREQUEST_TOKEN = 'INVALID_MULTIREQUEST_TOKEN;Invalid multirequest token [@token@];token';
}