<?php

/**
 * Filter pager
 */
class Pager extends RestObject
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