<?php
require_once __DIR__ . '/../lib/RestObject.class.php';
require_once __DIR__ . '/enums/UserStatus.enum.php';
require_once __DIR__ . '/ModelException.class.php';

/**
 * User object
 */
class User extends RestObject
{
	const TABLE_NAME = 'users';
	
	/**
	 * @var long
	 */
	public $id;
	
	/**
	 * @var long
	 */
	public $createdAt;

	/**
	 * @var long
	 */
	public $updatedAt;
	
	/**
	 * @var string
	 */
	public $firstName;
	
	/**
	 * @var string
	 */
	public $lastName;
	
	/**
	 * @var UserStatus
	 */
	public $status = UserStatus::ACTIVE;
	
	public function __construct(array $data = null)
	{
		unset($data['status']);
		parent::__construct($data);
	}

	public static function db2object(array $dbData)
	{
		return array(
			'id' => $dbData['id'],
			'createdAt' => $dbData['created_at'],
			'updatedAt' => $dbData['updated_at'],
			'firstName' => $dbData['first_name'],
			'lastName' => $dbData['last_name'],
			'status' => $dbData['status'],
		);
	}
	
	/**
	 * @return User
	 */
	public function add()
	{
		$values = array(
			'created_at' => time(),
			'updated_at' => time(),
			'first_name' => $this->sqliteString($this->firstName),
			'last_name' => $this->sqliteString($this->lastName),
			'status' => $this->sqliteString($this->status)
		);

		$record = RestDatabase::insert(self::TABLE_NAME, $values);
		$data = self::db2object($record->fetchArray(SQLITE3_ASSOC));
		$this->populate($data);
		return $this;
	}
	
	/**
	 * @param long $id
	 * @return User
	 */
	public static function get($id)
	{
		$result = RestDatabase::select(self::TABLE_NAME, array('id' => $id));
		$record = $result->fetchArray(SQLITE3_ASSOC);
		if(!$record)
			throw new ModelException(ModelException::OBJECT_NOT_FOUND, array('type' => 'User', 'id' => $id));
		
		$data = self::db2object($record);
		return new User($data);
	}
	
	/**
	 * @param User $user
	 * @return User
	 */
	public function update(User $user)
	{
		$values = array(
			'updated_at' => time(),
		);
		
		if(!is_null($user->firstName))
			$values['first_name'] = $this->sqliteString($user->firstName);
		if(!is_null($user->lastName))
			$values['last_name'] = $this->sqliteString($user->lastName);
		if(!is_null($user->status))
			$values['status'] = $this->sqliteString($user->status);

		$record = RestDatabase::update(self::TABLE_NAME, $this->id, $values);
		$data = self::db2object($record->fetchArray(SQLITE3_ASSOC));
		$this->populate($data);
		return $this;
	}
	
	/**
	 * @param long $id
	 */
	public static function delete($id)
	{
		RestDatabase::delete(self::TABLE_NAME, array('id' => $id));
	}
	
	/**
	 * @param UserFilter $filter
	 * @param RestFilterPager $pager
	 * @return UserList
	 */
	public function search(UserFilter $filter, Pager $pager)
	{
	}
}