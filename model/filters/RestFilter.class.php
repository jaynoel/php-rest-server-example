<?php

/**
 * Base REST filter
 */
abstract class RestFilter extends RestObject
{
	abstract public function search(RestFilterPager $pager);
}