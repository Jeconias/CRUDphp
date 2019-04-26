<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('./src/PlusCrud.php');

$c = \PlusCrud\PlusCrud::getInstance();

$c->setDBHost('localhost')->setDBName('hausnbizapi')->setDBUser('root')->setDBPass('')->run();


$c->delete('leadsstatus', array('id' => 1));

$c->select('leadsstatus', array('*'));






print_r($c->log());