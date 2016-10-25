<?php
/**
 * REST exception type
 */
class RestExceptionType
{
	/**
	 * @var string
	 */
	public $code;

	/**
	 * @var string
	 */
	public $message;

	/**
	 * @var array
	 */
	public $parameters;
	
	
	public function __construct($code, $message, $parameters)
	{
		parent::__construct($message);

		$this->code = $code;
		$this->message = $code;
		$this->parameters = $parameters;
	}
}