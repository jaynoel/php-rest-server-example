<?php

/**
 * Base REST filter
 */
abstract class Filter extends RestObject
{
	abstract public function search(Pager $pager);
}