<?php
require_once __DIR__ . '/../lib/RestController.class.php';

require_once __DIR__ . '/../model/RestUser.class.php';
require_once __DIR__ . '/../model/RestUserListResponse.class.php';
require_once __DIR__ . '/../model/filters/RestUserFilter.class.php';
require_once __DIR__ . '/../model/filters/RestFilterPager.class.php';

class RestUserController extends RestController
{
	/**
	 * @param RestUser $user
	 * @return RestUser
	 */
	public function add(RestUser $user)
	{
		return $user->add();
	}
	
	/**
	 * @param int $id
	 * @return RestUser
	 */
	public function get($id)
	{
		return RestUser::get($id);
	}

	/**
	 * @param int $id
	 * @param RestUser $user
	 * @return RestUser
	 */
	public function update($id, RestUser $user)
	{
		$existingUser = RestUser::get($id);
		return $existingUser->update($user);
	}

	/**
	 * @param int $id
	 */
	public function delete($id)
	{
		RestUser::delete($id);
	}
	
	/**
	 * @param RestUserFilter $filter
	 * @param RestFilterPager $pager
	 * @return RestUserListResponse
	 */
	public function search(RestUserFilter $filter = null, RestFilterPager $pager = null)
	{
		if(is_null($filter))
			$filter = new RestUserFilter();

		if(is_null($pager))
			$pager = new RestFilterPager();
			
		return $filter->search($pager);
	}
}