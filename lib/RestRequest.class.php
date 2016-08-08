<?php
require_once __DIR__ . '/RestResponseSerializer.class.php';

/**
 * REST request
 */
abstract class RestRequest
{
	/**
	 * @var RestResponseSerializer
	 */
	protected $responseSerializer;
	
	public function __construct(RestResponseSerializer $responseSerializer)
	{
		$this->responseSerializer = $responseSerializer;
	}
	
	/**
	 * Executes the request
	 * @return RestResponseSerializer
	 */
	public function execute()
	{
		return $this->responseSerializer;
	}
}