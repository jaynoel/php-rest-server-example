<?php

require_once __DIR__ . '/../lib/RestDatabase.class.php';
require_once __DIR__ . '/../lib/RestModelException.class.php';

/**
 * Base REST object
 */
abstract class RestObject
{
	public function __construct(array $data = null)
	{
		if($data)
			$this->populate($data);
	}
	
	protected static function db2object(array $dbData)
	{
		return $dbData;
	}
	
	protected function populate(array $data)
	{
		foreach($data as $attribute => $value)
			$this->$attribute = $value;
	}
	
	protected function sqliteString($str)
	{
		return "\"{$str}\"";
	}
}