<?php

/**
 * Filter pager
 */
class RestFilterPager extends RestObject
{
	/**
	 * @var int
	 */
	public $pageIndex = 1;
	
	/**
	 * @var int
	 */
	public $pageSize = 500;
}