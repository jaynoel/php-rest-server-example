<?php
require_once __DIR__ . '/RestObject.class.php';

/**
 * REST response
 */
abstract class RestResponseSerializer
{
	/**
	 * @var any
	 */
	protected $objectType = 'RestResponse';

	/**
	 * @var any
	 */
	protected $result = null;
	
	/**
	 * @var RestException
	 */
	protected $error;

	/**
	 * @var array<RestException>
	 */
	protected $warnings;
	
	/**
	 * @param $response
	 */
	public function setResponse($response = null)
	{
		$this->result = $response;
	}
	
	public function getResponse()
	{
		return $this->result;
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