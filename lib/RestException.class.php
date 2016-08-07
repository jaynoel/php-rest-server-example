<?php
/**
 * REST request
 */
class RestException extends Exception
{
	/**
	 * @var string
	 */
	private $restCode;

	/**
	 * @var array
	 */
	private $arguments;
	
	public function __construct($code, $message, $arguments)
	{
		parent::__construct($message);

		$this->restCode = $code;
		$this->arguments = $arguments;
	}
	
	/**
	 * @return array
	 */
	public function getArguments()
	{
		return $this->arguments;
	}
	
	/**
	 * @return string
	 */
	public function getRestCode()
	{
		return $this->restCode;
	}
}