<?php
require_once __DIR__ . '/RestRequest.class.php';
require_once __DIR__ . '/RestException.class.php';

/**
 * Handles exception 
 */
class RestInvalidRequest extends RestRequest
{
	public function __construct(RestResponseSerializer $responseSerializer, RestException $exception)
	{
		parent::__construct($responseSerializer);
		
		$responseSerializer->setError($exception);
	}
}