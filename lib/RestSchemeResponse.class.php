<?php
require_once __DIR__ . '/RestResponseSerializer.class.php';
require_once __DIR__ . '/RestSchemeException.class.php';

/**
 * REST response
 */
class RestSchemeResponse extends RestResponseSerializer
{
	private $enums = array();
	private $classes = array();
	private $exceptions = array('RestRequestException');
	
	private function addType($type, $class = null)
	{
		if(class_exists($type) && !isset($this->classes[$type]))
		{
			if(is_null($class))
				$class = new ReflectionClass($type);
			
			if($class->implementsInterface('IRestEnum'))
			{
				if(!isset($this->enums[$type]))
					$this->enums[$type] = $class;
				
				return;
			}
			
			$parentClass = $class->getParentClass();
			if($parentClass && $parentClass->getName() != 'RestObject')
				$this->addType($parentClass->getName(), $parentClass);
				
			$this->classes[$type] = $class;
			
			foreach($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
			{
				/* @var $property ReflectionProperty */
				$comment = $property->getDocComment();
				if(preg_match('/@var\s+([^\s]+)/mi', $comment, $matches))
				{
					$this->addType($matches[1]);
				}
			}
		}
	}
	
	private function appendParameter(SimpleXMLElement $xml, ReflectionParameter $parameter, $type, $description)
	{
		$xml->addAttribute('name', $parameter->getName());
		$xml->addAttribute('type', $type);
		$xml->addAttribute('description', $description);
		
		if($parameter->isOptional())
		{
			$xml->addAttribute('optional', true);
			$xml->addAttribute('defaultValue', $parameter->getDefaultValue());
		}
	}

	private function appendAction($serviceName, SimpleXMLElement $xml, ReflectionMethod $action)
	{
		$actionName = $action->getName();
		
		$xml->addAttribute('name', $actionName);
		$xml->addAttribute('enableInMultiRequest', true);
		
		$comment = $action->getDocComment();
		$matches = null;
		preg_match_all('/^\s*\*\s*@param\s+([^\s]+)\s+(\$[^\s]+)(\s+([^\r\n]+))?\r?\n/miU', $comment, $matches);
		
		foreach($action->getParameters() as $parameter)
		{
			/* @var $parameter ReflectionParameter */
			$parameterName = $parameter->getName();
			$index = array_search("\${$parameterName}", $matches[2]);
			if($index === false)
				throw new RestSchemeException(RestSchemeException::MISSING_PARAM_COMMENT, array('parameter' => $parameterName, 'service' => $serviceName, 'action' => $actionName));

			$type = $matches[1][$index];
			$description = $matches[4][$index];
			$this->appendParameter($xml->addChild('param'), $parameter, $type, $description);
			$this->addType($type);
		}

		if(preg_match('/@return\s+([^\s]+)/mi', $comment, $matches))
		{
			$result = $xml->addChild('result');
			$type = $matches[1];
			$result->addAttribute('type', $type);
			$this->addType($type);
		}
		
		if(preg_match_all('/^\s*\*\s*@throws\s+([^\s]+)/mi', $comment, $matches))
		{
			foreach($matches[1] as $error)
			{
				$throws = $xml->addChild('throws');
				list($exception, $code) = explode('::', $error);
				$throws->addAttribute('name', $code);
				
				if(class_exists($exception) && !in_array($exception, $this->exceptions))
					$this->exceptions[] = $exception;
			}
		}
	}

	private function appendService(SimpleXMLElement $xml, $name, $serviceClassName)
	{
		$xml->addAttribute('id', $name);
		$xml->addAttribute('name', $name);
		$class = new ReflectionClass($serviceClassName);
		foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			/* @var $method ReflectionMethod */
			if(!$method->isStatic() && $method->getDeclaringClass() == $class)
				$this->appendAction($name, $xml->addChild('action'), $method);
		}
	}

	private function appendProperty(SimpleXMLElement $xml, $type, ReflectionProperty $property)
	{
		$xml->addAttribute('name', $property->name);
		$matches = null;
		if(isset($this->enums[$type]))
		{
			$xml->addAttribute('type', 'int');
			$xml->addAttribute('enumType', $type);
		}
		elseif(preg_match('/array<([^>]+)>/i', $type, $matches))
		{
			$xml->addAttribute('type', 'array');
			$xml->addAttribute('arrayType', $matches[1]);
		}
		elseif(preg_match('/map<string,\s*([^>]+)>/i', $type, $matches))
		{
			$xml->addAttribute('type', 'map');
			$xml->addAttribute('mapType', $matches[1]);
		}
		else
		{
			$xml->addAttribute('type', $type);
		} 
	}

	private function appendClass(SimpleXMLElement $xml, $name, ReflectionClass $class)
	{
		$xml->addAttribute('name', $name);

		$parentClass = $class->getParentClass();
		if($parentClass && $parentClass->getName() != 'RestObject')
			$xml->addAttribute('base', $parentClass->getName());
		
		foreach($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			/* @var $property ReflectionProperty */
			if($property->getDeclaringClass() != $class || $property->name == 'objectType')
				continue;
			
			$comment = $property->getDocComment();
			if(preg_match('/@var\s+([^\s]+)/mi', $comment, $matches))
			{
				$type = $matches[1];
				$this->appendProperty($xml->addChild('property'), $type, $property);
			}
		}
	}

	private function appendEnum(SimpleXMLElement $xml, $name, ReflectionClass $class)
	{
		$xml->addAttribute('name', $name);
		$xml->addAttribute('type', 'int');
		foreach($class->getConstants() as $name => $value)
		{
			$const = $xml->addChild('const');
			$const->addAttribute('name', $name);
			$const->addAttribute('value', $value);
		}
	}
	
	private function appendError(SimpleXMLElement $xml, $error)
	{
		list($code, $message, $parameters) = explode(';', "$error;");
		$xml->addAttribute('name', $code);
		$xml->addAttribute('code', $code);
		$xml->addAttribute('message', $message);
		
		$parameters = explode(',', $parameters);
		foreach($parameters as $parameter)
		{
			$child = $xml->addChild('parameter');
			$child->addAttribute('name', $parameter);
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see RestResponse::serialize()
	 */
	protected function serialize()
	{
		header("Content-Type: application/xml");
		
		$xml = new SimpleXMLElement('<xml/>');
		$enums = $xml->addChild('enums');
		$classes = $xml->addChild('classes');
		$services = $xml->addChild('services');
		$errors = $xml->addChild('errors');
		
		foreach(RestRequestDeserializer::getControllers() as $name => $serviceClassName)
		{
			$this->appendService($services->addChild('service'), $name, $serviceClassName);
		}
		
		foreach($this->classes as $name => $class)
		{
			$this->appendClass($classes->addChild('class'), $name, $class);
		}
		
		foreach($this->enums as $name => $class)
		{
			$this->appendEnum($enums->addChild('enum'), $name, $class);
		}
		
		foreach($this->exceptions as $exception)
		{
			$class = new ReflectionClass($exception);
			foreach($class->getConstants() as $error)
				$this->appendError($errors->addChild('error'), $error);
		}
		
		return $xml->asXML();
	}
}