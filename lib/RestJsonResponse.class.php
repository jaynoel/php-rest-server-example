<?php
require_once __DIR__ . '/RestResponse.class.php';

/**
 * REST response
 */
class RestJsonResponse extends RestResponse
{
	/**
	 * {@inheritDoc}
	 * @see RestResponse::serialize()
	 */
	protected function serialize()
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
			
		return json_encode($json);
	}
}