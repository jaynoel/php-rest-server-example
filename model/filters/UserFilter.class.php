<?php
require_once __DIR__ . '/Filter.class.php';

/**
 * User filter
 */
class UserFilter extends Filter
{
	/**
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $createdAtLessThanOrEqual;
	
	/**
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;
	
	/**
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;
	
	/**
	 * @see RestFilter::search()
	 * @return UserList
	 */
	public function search(Pager $pager)
	{
		$where = array();
		if(!is_null($this->createdAtGreaterThanOrEqual))
			$where[] = "created_at >= $this->createdAtGreaterThanOrEqual";

		if(!is_null($this->createdAtLessThanOrEqual))
			$where[] = "created_at <= $this->createdAtLessThanOrEqual";
		
		if(!is_null($this->updatedAtGreaterThanOrEqual))
			$where[] = "updated_at >= $this->updatedAtGreaterThanOrEqual";

		if(!is_null($this->updatedAtLessThanOrEqual))
			$where[] = "updated_at <= $this->updatedAtLessThanOrEqual";
		
		$result = RestDatabase::search(User::TABLE_NAME, $where, $pager->pageSize, $pager->pageIndex);
		
		$list = new UsersList();
		$list->objects = array();

		$record = $result->fetchArray(SQLITE3_ASSOC);
		while($record)
		{	
			$data = User::db2object($record);
			$list->objects[] = new User($data);
			
			$record = $result->fetchArray(SQLITE3_ASSOC);
		}
		
		$list->totalCount = count($list->objects);
		if($list->totalCount == $pager->pageSize)
		{
			$list->totalCount = RestDatabase::count(User::TABLE_NAME, $where);
		}
		
		return $list;
	}
}