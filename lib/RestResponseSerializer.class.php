<?php
require_once __DIR__ . '/../model/RestObject.class.php';

/**
 * REST response
 */
abstract class RestResponseSerializer
{
	/**
	 * @var bool
	 */
	protected $isMultirequest;
	
	/**
	 * @var any
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
	
	public function __construct($isMultirequest)
	{
		$this->isMultirequest = $isMultirequest;
	}
	
	/**
	 * @param $response
	 */
	public function setResponse($response = null)
	{
		$this->response = $response;
	}
	
	public function getResponse()
	{
		return $this->response;
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