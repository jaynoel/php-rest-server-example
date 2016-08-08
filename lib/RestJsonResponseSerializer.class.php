<?php
require_once __DIR__ . '/RestResponseSerializer.class.php';

/**
 * REST response
 */
class RestJsonResponseSerializer extends RestResponseSerializer
{
	public function getJson()
	{
		$json = array(
			'result' => $this->response
		);

		if(!is_null($this->error))
		{
			$json['error'] = array(
				'code' => $this->error->getRestCode(),
				'message' => $this->error->getMessage(),
				'arguments' => $this->error->getArguments(),
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
		if($this->isMultirequest)
		{
			$json = array();
			foreach($this->response as $response)
			{
				/* @var $response RestJsonResponseSerializer */
				$json[] = $response->getJson();
			}
		}
		else
		{
			$json = $this->getJson();
		}
		return json_encode($json);
	}
}