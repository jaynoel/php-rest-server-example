<?php

class RestDatabase
{
	/**
	 * @var SQLite3
	 */
	private static $db = null;

	private static function init()
	{
		if(is_null(self::$db))
			self::$db = new SQLite3(__DIR__ . '/../data/database.db');
	}

	public static function close()
	{
		if(!is_null(self::$db))
		{
			self::$db->close();
			self::$db = null;
		}
	}
	
	public static function insert($table, array $values)
	{
		self::init();

		$columns = implode(', ', array_keys($values));
		$values = implode(', ', $values);
		
		self::$db->exec("INSERT INTO $table ($columns) VALUES ($values)");
		$rowId = self::$db->lastInsertRowID();
		return self::$db->query("SELECT * FROM  $table WHERE id = $rowId");
	}
	
	public static function update($table, $id, array $values)
	{
		self::init();

		$sets = array();
		foreach($values as $column => $value)
			$sets[] = "$column = $value";
				
		$setClause = implode(', ', $sets);

		self::$db->exec("UPDATE $table SET $setClause WHERE id = $id");
		return self::$db->query("SELECT * FROM  $table WHERE id = $id");
	}
	
	public static function select($table, array $where = null, array $columns = null)
	{
		self::init();

		$columnsClause = is_null($columns) ? '*' : implode(', ', $columns);
		$whereClause = '';
		
		if($where)
		{
			$wheres = array();
			foreach($where as $column => $value)
				$wheres[] = "$column = $value";
			
			$whereClause = 'WHERE ' . implode(' AND ', $wheres);
		}
		
		return self::$db->query("SELECT $columnsClause FROM $table $whereClause");
	}
	
	public static function search($table, array $where, $pageSize, $pageIndex, array $columns = null)
	{
		self::init();

		$offset = ($pageIndex - 1) * $pageSize;
		$columnsClause = is_null($columns) ? '*' : implode(', ', $columns);
		$whereClause = 'WHERE ' . implode(' AND ', $where);
		$limitClause = "LIMIT $pageSize";
		if($offset)
			$limitClause .= ", $offset";
		
		return self::$db->query("SELECT $columnsClause FROM $table $whereClause $limitClause");
	}
	
	public static function count($table, array $where)
	{
		self::init();

		$whereClause = 'WHERE ' . implode(' AND ', $where);
		
		$result = self::$db->query("SELECT count(id) as cnt FROM $table $whereClause");
		$record = $result->fetchArray(SQLITE3_ASSOC);
		return $record['cnt'];
	}
	
	public static function delete($table, array $where)
	{
		self::init();

		$wheres = array();
		foreach($where as $column => $value)
			$wheres[] = "$column = $value";
		
		$whereClause = 'WHERE ' . implode(' AND ', $wheres);
		
		return self::$db->query("DELETE FROM $table $whereClause");
	}
}