<?php
require_once __DIR__ . '/../lib/RestController.class.php';

require_once __DIR__ . '/../model/User.class.php';
require_once __DIR__ . '/../model/UsersList.class.php';
require_once __DIR__ . '/../model/filters/UserFilter.class.php';
require_once __DIR__ . '/../model/filters/Pager.class.php';


/**
 * @service user
 */
class UsersService extends RestController
{
	/**
	 * @param User $user
	 * @return User
	 */
	public function add(User $user)
	{
		return $user->add();
	}
	
	/**
	 * @param long $id
	 * @return User
	 * @throws ModelException::OBJECT_NOT_FOUND
	 */
	public function get($id)
	{
		return User::get($id);
	}

	/**
	 * @param long $id user id to update
	 * @param User $user
	 * @return User
	 * @throws ModelException::OBJECT_NOT_FOUND
	 */
	public function update($id, User $user)
	{
		$existingUser = User::get($id);
		return $existingUser->update($user);
	}

	/**
	 * @param long $id user id to delete
	 * @throws ModelException::OBJECT_NOT_FOUND
	 */
	public function delete($id)
	{
		User::delete($id);
	}
	
	/**
	 * @param UserFilter $filter
	 * @param Pager $pager
	 * @return UsersList
	 */
	public function search(UserFilter $filter = null, Pager $pager = null)
	{
		if(is_null($filter))
			$filter = new UserFilter();

		if(is_null($pager))
			$pager = new Pager();
			
		return $filter->search($pager);
	}
}