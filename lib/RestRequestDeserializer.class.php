<?php
require_once __DIR__ . '/RestException.class.php';
require_once __DIR__ . '/RestRequestException.class.php';

require_once __DIR__ . '/RestRequest.class.php';
require_once __DIR__ . '/RestInvalidRequest.class.php';
require_once __DIR__ . '/RestSchemeRequest.class.php';
require_once __DIR__ . '/RestControllerRequest.class.php';

require_once __DIR__ . '/RestXmlResponseSerializer.class.php';
require_once __DIR__ . '/RestJsonResponseSerializer.class.php';

require_once __DIR__ . '/RestController.class.php';

$controllersDir = realpath(__DIR__ . '/../controllers');
$controllerFiles = scandir($controllersDir);
foreach ($controllerFiles as $controllerFile)
{
	if($controllerFile[0] != '.')
		require_once "$controllersDir/$controllerFile";
}

/**
 * Parse the HTTP request
 */
class RestRequestDeserializer
{
	/**
	 * @var string
	 */
	static $responseSerializerClass;
	
	/**
	 * @var bool
	 */
	static $isMultirequest;
	
	/**
	 * @return RestRequest
	 */
	public static function deserialize()
	{
		try
		{
			return self::parse();
		}
		catch (RestException $e)
		{
			return self::getInvalidRequest($e);
		}
	}
	
	/**
	 * @return RestRequest
	 */
	private static function getControllerInstance($controller)
	{
		$controllerClassName = "Rest{$controller}Controller";
		if(!class_exists($controllerClassName) || !is_subclass_of($controllerClassName, 'RestController'))
			throw new RestRequestException(RestRequestException::CONTROLLER_NOT_FOUND, "Controller [$controller] not found", array('controller' => $controller));
		
		return new $controllerClassName();
	}
	
	/**
	 * @return RestResponseSerializer
	 */
	public static function getResponseSerializer()
	{
		return new self::$responseSerializerClass(self::$isMultirequest);
	}
	
	/**
	 * @return RestRequest
	 */
	public static function getInvalidRequest(RestException $e)
	{
		return new RestInvalidRequest(self::getResponseSerializer(), $e);
	}
	
	/**
	 * @return RestRequest
	 */
	public static function getControllerRequest($controller, $action, $data)
	{
		$controllerInstance = self::getControllerInstance($controller);

		if(!method_exists($controllerInstance, $action))
			throw new RestRequestException(RestRequestException::ACTION_NOT_FOUND, "Action [$controller.$action] not found", array('controller' => $controller, 'action' => $action));
		
		return new RestControllerRequest(self::getResponseSerializer(), $controllerInstance, $action, $data);
	}
	
	/**
	 * @return RestRequest
	 */
	private static function parse()
	{
		$path = $_SERVER['PHP_SELF'];
		$path = str_replace('/index.php', '', $path);
		
		if(!$path)
			return new RestSchemeRequest();
		
		$pathParts = explode('/', trim($path, '/'));
		$controller = array_shift($pathParts);
		
		$action = null;
		self::$isMultirequest = false;
		if($controller == 'multirequest')
		{
			$action = 'execute';
			self::$isMultirequest = true;
		}
		else
		{
			$action = array_shift($pathParts);
		}

		$pathParams = array();
		while(count($pathParts) > 1)
		{
			$paramName = array_shift($pathParts);
			$paramValue = array_shift($pathParts);
			$pathParams[$paramName] = $paramValue;
		}
		
		$post = null;
		if(isset($_SERVER['CONTENT_TYPE']))
		{
			if(strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') === 0)
			{
				$requestBody = file_get_contents("php://input");
				if(preg_match('/^[\{\[].*[\}\]]$/', $requestBody))
				{
					$post = json_decode($requestBody, true);
					if(!$post)
						throw new RestRequestException(RestRequestException::INVALID_JSON, "Invalid JSON");
				}
			}
			elseif(strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/form-data') === 0 && isset($_POST['json']))
			{
				$post = json_decode($_POST['json'], true);
				if(!$post)
					throw new RestRequestException(RestRequestException::INVALID_JSON, "Invalid JSON");
			}
		}
		if(!$post)
		{
			$post = $_POST;
		}
		
		$data = array_replace_recursive($post, $_FILES, $_GET, $pathParams);
		
		if(		(isset($data['format']) && strtolower($data['format']) == 'xml') 
				|| strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/xml') === 0 
				|| strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'text/xml') === 0)
		{
			self::$responseSerializerClass = 'RestXmlResponseSerializer';
		}
		else
		{
			self::$responseSerializerClass = 'RestJsonResponseSerializer';
		}

		return self::getControllerRequest($controller, $action, $data);
	}
}