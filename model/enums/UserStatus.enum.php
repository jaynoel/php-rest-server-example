<?php
require_once __DIR__ . '/../../lib/IRestEnum.interface.php';

class UserStatus implements IRestEnum
{
	const ACTIVE = 0;
	const DISABLED = 1;
}
