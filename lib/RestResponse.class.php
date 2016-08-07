<?php
require_once __DIR__ . '/../model/RestObject.class.php';

/**
 * REST response
 */
abstract class RestResponse
{
	/**
	 * @var RestObject
	 */
	protected $response = null;

	/**
	 * @var RestException
	 */
	protected $error;

	/**
	 * @var array<RestException>
	 */
	protected $warnings;
	
	/**
	 * @param RestObject $response
	 */
	public function setResponse(RestObject $response = null)
	{
		$this->response = $response;
	}
	
	/**
	 * @param RestException $error
	 */
	public function setError(RestException $error)
	{
		$this->error = $error;
	}

	abstract protected function serialize();
	
	/**
	 * Return the serialized output
	 * @return string
	 */
	public function __toString()
	{
		return $this->serialize();
	}
}