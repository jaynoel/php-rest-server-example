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
		$this->data = $this->buildArguments($data);
	}
	
	/**
	 * @return array
	 */
	private function buildArguments(array $data)
	{
		$arguments = array();
		
		$method = new ReflectionMethod($this->controllerInstance, $this->action);
		foreach($method->getParameters() as $parameter)
		{
			/* @var $parameter ReflectionParameter */
			if(!isset($data[$parameter->name]))
			{
				if(!$parameter->isOptional())
					throw new RestRequestException(RestRequestException::MISSING_PARAMETER, "Missing parameter [{$parameter->name}]", array('parameter' => $parameter->name));
				
				$arguments[] = $parameter->getDefaultValue();
				continue;
			}
			
			$type = $parameter->getClass();
			if(is_null($type))
			{
				if($parameter->isArray() && !is_array($data[$parameter->name]))
				{
					throw new RestRequestException(RestRequestException::PARAMETER_WRONG_TYPE, "Parameter [{$parameter->name}] expected to be array", array('parameter' => $parameter->name, 'type' => 'array'));
				}
				
				$arguments[] = $data[$parameter->name];
				continue;
			}

			if(!is_array($data[$parameter->name]))
			{
				throw new RestRequestException(RestRequestException::PARAMETER_WRONG_TYPE, "Parameter [{$parameter->name}] expected to be {$type->name}", array('parameter' => $parameter->name, 'type' => $type->name));
			}

			$arguments[] = $this->buildObject($parameter->name, $type, $data[$parameter->name]);
		}
		
		return $arguments;
	}
	
	/**
	 * @param string $name
	 * @param ReflectionClass $type
	 * @param array $data
	 * @return RestObject
	 */
	private function buildObject($name, ReflectionClass $type, array $data)
	{
		$class = $type->name;
		if(isset($data['objectType']))
		{
			if(!is_subclass_of($data['objectType'], $class))
				throw new RestRequestException(RestRequestException::PARAMETER_WRONG_TYPE, "Parameter [$name] expected to be $class", array('parameter' => $name, 'type' => $class));
			
			$class = $data['objectType'];
		}
		elseif($type->isAbstract())
		{
			throw new RestRequestException(RestRequestException::ABSTRACT_TYPE, "Parameter [$name] type [$class] is abstract", array('parameter' => $name, 'type' => $class));
		}
		
		return new $class($data);
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
			$this->response->setResponse($response);
		}
		catch(RestException $e)
		{
			$this->response->setError($e);
		}
		
		return parent::execute();
	}

}