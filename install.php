<?php

if(!file_exists(__DIR__ . '/data'))
	mkdir(__DIR__ . '/data', 0664);

$db = new SQLite3(__DIR__ . '/data/database.db');
$db->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, created_at INTEGER, updated_at INTEGER, first_name TEXT, last_name TEXT, email TEXT, status INTEGER)');
$db->close();
