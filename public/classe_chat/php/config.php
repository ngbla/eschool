<?php
// Image dir

use App\Config;

$imageDir = "image";
/*
    Replace with: your database account
    $username 	= 'etspkgjv_poincaree_admin';
    $password 	= '4*mcmzGv#_.*';
    $host  		= 'localhost';
    $name    	= 'etspkgjv_eschool_poincaree';
    var_dump('http://'.$_SERVER['HTTP_HOST'].'/App/Config.php');
*/

//require_once('../../../App/Config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/App/Config.php');

//var_dump(Config::ENVI);
//echo dirname(__FILE__);


if (Config::ENVI=="local") {
    $username 	= Config::DB_USER;
    $password 	= Config::DB_PASSWORD;
    $host  		= Config::DB_HOST;
    $name    	= Config::DB_NAME;
}
elseif (Config::ENVI=="online") {
    $username 	= Config::DB_USER_ONLINE;
    $password 	= Config::DB_PASSWORD_ONLINE;
    $host  		= Config::DB_HOST_ONLINE;
    $name    	= Config::DB_NAME_ONLINE;
}
else {echo 'Erreur de connexion à la BD ...'; exit;}

//var_dump('user : '.$username.' pass:'.$password.' host: '.$host.' bd:'.$name);