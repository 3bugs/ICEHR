<?php
require_once 'jwt.php';

// encode
define('SECRET_KEY', 'icehr_and_2fellows');
$token = array();
$token['id'] = 1;
$token['name'] = 'Promlert';
$jwt = JWT::encode($token, SECRET_KEY);
$len = strlen($jwt);

// decode
$token = JWT::decode($jwt, SECRET_KEY);
$id = $token->id;
$name = $token->name;
echo "ID: $id, Name: $name, Len: $len";