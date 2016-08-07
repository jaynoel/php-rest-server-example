<?php
require_once __DIR__ . '/RestResponse.class.php';

/**
 * REST request
 */
abstract class RestRequest
{
	/**
	 * @var RestResponse
	 */
	protected $response;
	
	public function __construct(RestResponse $response)
	{
		$this->response = $response;
	}
	
	/**
	 * Executes the request
	 * @return RestResponse
	 */
	public function execute()
	{
		return $this->response;
	}
}