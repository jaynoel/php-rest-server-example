<?php
require_once __DIR__ . '/RestObject.class.php';

/**
 * User object
 */
class RestUser extends RestObject
{
	const TABLE_NAME = 'users';
	
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 * @var int
	 */
	public $createdAt;

	/**
	 * @var int
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
	 * @var string
	 */
	public $email;
	
	public function __construct(array $data = null)
	{
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
			'email' => $dbData['email'],
		);
	}
	
	/**
	 * @return RestUser
	 */
	public function add()
	{
		$values = array(
			'created_at' => time(),
			'updated_at' => time(),
			'first_name' => $this->sqliteString($this->firstName),
			'last_name' => $this->sqliteString($this->lastName),
			'email' => $this->sqliteString($this->email)
		);

		$record = RestDatabase::insert(self::TABLE_NAME, $values);
		$data = self::db2object($record->fetchArray(SQLITE3_ASSOC));
		$this->populate($data);
		return $this;
	}
	
	/**
	 * @param int $id
	 * @return RestUser
	 */
	public static function get($id)
	{
		$result = RestDatabase::select(self::TABLE_NAME, array('id' => $id));
		$record = $result->fetchArray(SQLITE3_ASSOC);
		if(!$record)
			throw new RestModelException(RestModelException::OBJECT_NOT_FOUND, "User id [$id] not found", array('type' => 'User', 'id' => $id));
		
		$data = self::db2object($record);
		return new RestUser($data);
	}
	
	/**
	 * @param RestUser $user
	 * @return RestUser
	 */
	public function update(RestUser $user)
	{
		$values = array(
			'updated_at' => time(),
		);
		
		if(!is_null($user->firstName))
			$values['first_name'] = $this->sqliteString($user->firstName);
		if(!is_null($user->lastName))
			$values['last_name'] = $this->sqliteString($user->lastName);
		if(!is_null($user->email))
			$values['email'] = $this->sqliteString($user->email);

		$record = RestDatabase::update(self::TABLE_NAME, $this->id, $values);
		$data = self::db2object($record->fetchArray(SQLITE3_ASSOC));
		$this->populate($data);
		return $this;
	}
	
	/**
	 * @param int $id
	 */
	public static function delete($id)
	{
		RestDatabase::delete(self::TABLE_NAME, array('id' => $id));
	}
	
	/**
	 * @param RestUserFilter $filter
	 * @param RestFilterPager $pager
	 * @return RestUserListResponse
	 */
	public function search(RestUserFilter $filter, RestFilterPager $pager)
	{
	}
}