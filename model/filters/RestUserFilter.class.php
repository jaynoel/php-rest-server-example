<?php
require_once __DIR__ . '/RestFilter.class.php';

/**
 * User filter
 */
class RestUserFilter extends RestFilter
{
	/**
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;
	
	/**
	 * @see RestFilter::search()
	 * @return RestUserListResponse
	 */
	public function search(RestFilterPager $pager)
	{
		$where = array();
		if(!is_null($this->createdAtGreaterThanOrEqual))
			$where[] = "created_at >= $this->createdAtGreaterThanOrEqual";

		if(!is_null($this->updatedAtGreaterThanOrEqual))
			$where[] = "updated_at >= $this->updatedAtGreaterThanOrEqual";
		
		$result = RestDatabase::search(RestUser::TABLE_NAME, $where, $pager->pageSize, $pager->pageIndex);
		
		$list = new RestUserListResponse();
		$list->objects = array();

		$record = $result->fetchArray(SQLITE3_ASSOC);
		while($record)
		{	
			$data = RestUser::db2object($record);
			$list->objects[] = new RestUser($data);
			
			$record = $result->fetchArray(SQLITE3_ASSOC);
		}
		
		$list->totalCount = count($list->objects);
		if($list->totalCount == $pager->pageSize)
		{
			$list->totalCount = RestDatabase::count(RestUser::TABLE_NAME, $where);
		}
		
		return $list;
	}
}