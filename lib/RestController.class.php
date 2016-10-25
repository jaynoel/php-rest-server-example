<?php
/**
 * REST base controller
 */
abstract class RestController
{
	/**
	 * @return array
	 */
	public function buildArguments($action, array $data)
	{
		$arguments = array();
		
		$method = new ReflectionMethod($this, $action);
		foreach($method->getParameters() as $parameter)
		{
			/* @var $parameter ReflectionParameter */
			if(!isset($data[$parameter->name]))
			{
				if(!$parameter->isOptional())
					throw new RestRequestException(RestRequestException::MISSING_PARAMETER, array('parameter' => $parameter->name));
				
				$arguments[] = $parameter->getDefaultValue();
				continue;
			}
			
			$type = $parameter->getClass();
			if(is_null($type))
			{
				if($parameter->isArray())
				{
					if(!is_array($data[$parameter->name]))
						throw new RestRequestException(RestRequestException::PARAMETER_WRONG_TYPE, array('parameter' => $parameter->name, 'type' => 'array'));
					
					$arrayType = $this->getArrayType($method->getDocComment(), $parameter->name);
					if($arrayType)
					{
						$objectsArray = array();
						foreach($data[$parameter->name] as $objectData)
							$objectsArray[] = $this->buildObject($parameter->name, $arrayType, $objectData);

						$arguments[] = $objectsArray;
						continue;
					}
				}
				
				$arguments[] = $data[$parameter->name];
				continue;
			}

			if(!is_array($data[$parameter->name]))
			{
				throw new RestRequestException(RestRequestException::PARAMETER_WRONG_TYPE, array('parameter' => $parameter->name, 'type' => $type->name));
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
	protected function buildObject($name, ReflectionClass $type, array $data)
	{
		$class = $type->name;
		if(isset($data['objectType']))
		{
			if(!is_subclass_of($data['objectType'], $class))
				throw new RestRequestException(RestRequestException::PARAMETER_WRONG_TYPE, array('parameter' => $name, 'type' => $class));
			
			$class = $data['objectType'];
		}
		elseif($type->isAbstract())
		{
			throw new RestRequestException(RestRequestException::ABSTRACT_TYPE, array('parameter' => $name, 'type' => $class));
		}
		
		return new $class($data);
	}
	
	protected function getArrayType($comment, $argumentName)
	{
		$matches = null;
		if(!preg_match("/@param array<([^>]+)> \${$argumentName}/", $comment, $matches))
			return null;
			
		return new ReflectionClass($matches[1]);
	}
}