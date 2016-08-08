<?php
require_once __DIR__ . '/../lib/RestController.class.php';

class RestMultirequestController extends RestController
{
	/**
	 * @param array<RestControllerRequest> $requests
	 * @return array<RestRequest>
	 */
	public function execute(array $requests)
	{
		$responses = array();
		foreach($requests as $index => $requet)
		{
			/* @var $requet RestControllerRequest */
			try 
			{
				if($index)
				{
					$tokenizedData = $requet->getData();
					$data = $this->replaceTokens($tokenizedData, $responses);
					$requet->setData($data);
				}

				$response = $requet->execute();
			}
			catch(RestException $e)
			{
				$response = RestRequestDeserializer::getInvalidRequest($e);
			}
			catch(Exception $e)
			{
				var_dump($e);
			}

			$responses[] = $response;
		}
		
		return $responses;
	}

	public function replaceToken(array $tokens, $response)
	{
		if(!count($tokens))
			return $response;
		
		$token = array_shift($tokens);
		if($response instanceof RestObject)
			return $this->replaceToken($tokens, $response->$token);

		if(is_array($response))
			return $this->replaceToken($tokens, $response['$token']);

		throw new Exception("Wrong number of tokens");
	}

	public function replaceValueToken($value, array $responses)
	{
		if(is_array($value))
		{
			return $this->replaceTokens($value, $responses);
		}
		
		if($value instanceof RestObject)
		{
			return $this->replaceObjectTokens($value, $responses);
		}
		
		if(preg_match('/^\{results:(\d+):(.+)\}$/', $value, $matches))
		{
			$responseIndex = intval($matches[1]) - 1;
			if(!isset($responses[$responseIndex]))
				throw new RestRequestException(RestRequestException::INVALID_MULTIREQUEST_TOKEN, "Invalid multirequest token [$value]", array('value' => $value));
			
			$tokens = explode(':', $matches[2]);
			try 
			{
				return $this->replaceToken($tokens, $responses[$responseIndex]->getResponse());
			}
			catch(Exception $e)
			{
				throw new RestRequestException(RestRequestException::INVALID_MULTIREQUEST_TOKEN, "Invalid multirequest token [$value]", array('value' => $value));
			}
		}
		
		return $value;
	}

	/**
	 * @return array
	 */
	public function replaceObjectTokens(RestObject $tokenizedObject, array $responses)
	{
		$matches = null;
		foreach(get_object_vars($tokenizedObject) as $property => $value)
		{
			$tokenizedObject->$property = $this->replaceValueToken($value, $responses);
		}
		
		return $tokenizedObject;
	}

	/**
	 * @return array
	 */
	public function replaceTokens(array $tokenizedData, array $responses)
	{
		$data = array();
		$matches = null;
		foreach($tokenizedData as $value)
		{
			$data[] = $this->replaceValueToken($value, $responses);
		}
		
		return $data;
	}

	/**
	 * @return array
	 */
	public function buildArguments($action, array $data)
	{
		$requests = array();
		
		foreach($data as $requestData)
		{
			$requests[] = RestRequestDeserializer::getControllerRequest($requestData['service'], $requestData['action'], $requestData);
		}
	
		return array($requests);
	}
	
}