<?php
require_once __DIR__ . '/RestRequest.class.php';

/**
 * Builds the REST API scheme XML
 */
class RestSchemeRequest extends RestRequest
{
	public function __construct()
	{
	}
	
	/**
	 * {@inheritDoc}
	 * @see RestRequest::execute()
	 */
	public function execute()
	{
		return new RestSchemeResponse();
	}
}