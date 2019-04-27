<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('./crud/PlusCrud.php');

$c = \PlusCrud\PlusCrud::getInstance();
$c->setDBHost('localhost');
$c->setDBName('hausnbizapi');
$c->setDBUser('root');
$c->setDBPass('');
$c->run();

print_r($c->log());