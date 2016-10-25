<?php
require_once __DIR__ . '/RestResponseSerializer.class.php';

/**
 * REST response
 */
class RestXmlResponseSerializer extends RestResponseSerializer
{
	/**
	 * @param SimpleXMLElement $xml
	 * @param string $property
	 * @param object $value
	 * @return SimpleXMLElement
	 */
	private function appendProperty(SimpleXMLElement $xml, $property, $value)
	{
		if($property == 'objectType')
			return $xml->addAttribute('objectType', $value);
		
		if(is_object($value))
		{
			$child = $xml->addChild($property);
			foreach(get_object_vars($value) as $subProperty => $propertyValue)
			{
				$this->appendProperty($child, $subProperty, $propertyValue);
			}
			return $child;
		}
		
		if(is_array($value))
		{
			$child = $xml->addChild($property);
			$type = 'array';
			$index = 0;
			foreach($value as $key => $item)
			{
				$itemXml = $this->appendProperty($child, 'item', $item);
				if(!is_numeric($key) || $key != $index)
				{
					$type = 'map';
					$itemXml->addAttribute('key', $key);
				}
				$index++;
			}
			$child->addAttribute('objectType', $type);
			return $child;
		}
		
		return $xml->addChild($property, $value);
	}
	
	/**
	 * {@inheritDoc}
	 * @see RestResponse::serialize()
	 */
	protected function serialize()
	{
		header("Content-Type: application/xml");
		
		$xml = new SimpleXMLElement('<xml/>');
		if($this->result)
		{
			$this->appendProperty($xml, 'result', $this->result);
		}
		
		if($this->error)
		{
			$this->appendProperty($xml, 'error', $this->error);
		}
		
		return $xml->asXML();
	}
}