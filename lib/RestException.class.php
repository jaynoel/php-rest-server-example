<?php
/**
 * REST exception
 */
class RestException extends Exception
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
	
	
	public function __construct($type, array $parameters = array())
	{
		list($code, $message) = $this->format($type, $parameters);
		parent::__construct($message);

		$this->code = $code;
		$this->message = $message;
		$this->parameters = $parameters;
	}
	
	public function wrapToken(&$token)
	{
		$token = "@$token@";
	}
	
	private function format($type, array $parameters)
	{
		list($code, $message) = explode(';', $type);
		$keys = array_keys($parameters);
		array_walk($keys, array($this, 'wrapToken'));
		$message = str_replace($keys, $parameters, $message);
		return array($code, $message);
	}
	
	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}