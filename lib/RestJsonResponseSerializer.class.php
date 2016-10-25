<?php
require_once __DIR__ . '/RestResponseSerializer.class.php';

/**
 * REST response
 */
class RestJsonResponseSerializer extends RestResponseSerializer
{
	public function getJson()
	{
		$json = array();
		
		if(is_array($this->result))
		{
			$json['result'] = array();
			foreach ($this->result as $index => $response)
			{
				if($response instanceof RestJsonResponseSerializer)
				{
					$json['result'][$index] = $response->getJson();
				}
				else
				{
					$json['result'][$index] = $response;
				} 
			}
		}
		else
		{
			$json['result'] = $this->result;
		}

		if(!is_null($this->error))
		{
			$json['error'] = array(
				'code' => $this->error->code,
				'message' => $this->error->message,
				'parameters' => $this->error->parameters,
			);
		}
			
		return $json;
	}
	
	/**
	 * {@inheritDoc}
	 * @see RestResponse::serialize()
	 */
	protected function serialize()
	{
		header("Content-Type: application/json");
		$json = $this->getJson();
		return json_encode($json);
	}
}