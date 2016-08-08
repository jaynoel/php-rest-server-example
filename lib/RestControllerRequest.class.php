<?php
require_once __DIR__ . '/RestRequest.class.php';
require_once __DIR__ . '/RestRequestException.class.php';

/**
 * Accepts controller and action and executes them
 */
class RestControllerRequest extends RestRequest
{
	/**
	 * @var RestController
	 */
	private $controllerInstance;
	
	/**
	 * @var string
	 */
	private $action;
	
	/**
	 * @var array
	 */
	private $data;
	
	public function __construct($response, RestController $controllerInstance, $action, array $data)
	{
		parent::__construct($response);
		
		$this->controllerInstance = $controllerInstance;
		$this->action = $action;
		$this->data = $controllerInstance->buildArguments($action, $data);
	}
	
	/**
	 * {@inheritDoc}
	 * @see RestRequest::execute()
	 */
	public function execute()
	{
		try
		{
			$response = call_user_func_array(array($this->controllerInstance, $this->action), $this->data);
			$this->responseSerializer->setResponse($response);
		}
		catch(RestException $e)
		{
			$this->responseSerializer->setError($e);
		}
		
		return parent::execute();
	}
	
	/**
	 * @return the $controllerInstance
	 */
	public function getControllerInstance()
	{
		return $this->controllerInstance;
	}

	/**
	 * @return the $action
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return the $data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param multitype: $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
}