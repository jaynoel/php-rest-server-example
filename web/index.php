<?php
require_once __DIR__ . '/../lib/RestRequestDeserializer.class.php';
require_once __DIR__ . '/../lib/RestDatabase.class.php';

$request = RestRequestDeserializer::deserialize();
$response = $request->execute();
echo $response;

RestDatabase::close();