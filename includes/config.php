<?php // configuration file
session_start();
date_default_timezone_set('Europe/Rome');


error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE);
@ini_set('display_errors', 'On');
@ini_set('short_open_tag', true);

define('DEBUG','true');
define('CHARSET','UTF-8');
$hostName = $_SERVER["HTTP_HOST"];
$completeUrl = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];

define('SITE_ID',"Pitchfork scores");
define('SITE_ROOT',dirname(dirname(__FILE__)));

$DB_HOST = "localhost";
$DB_NAME = "[[YOUR DB NAME]]";
$DB_USR = "[[YOUR DB USER]]";
$DB_PWD = "[[YOUR DB PASSWORD]]";

$db = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8","$DB_USR","$DB_PWD");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("SET NAMES 'utf8';");
$db->exec("SET CHARACTER SET utf8");

function __autoload( $class )
{
	$load = SITE_ROOT.'/classes/'.$class.'.php';
	if( file_exists( $load ) )
	{
		include_once( $load );
	}
	else
	{
		die( "Can't find a file for class: $class - $load \n" );
	}
}

?>
