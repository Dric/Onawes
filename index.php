<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 09:51
 */
spl_autoload_register(function ($class) {
	@include_once 'classes/' . str_replace("\\", "/", $class) . '.php';
});
// Définition de quelques variables
$isHTTPS = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'];
$absURL = rtrim((($isHTTPS) ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].str_replace('index.php', '', $_SERVER['PHP_SELF']), '/');

$args = array(
	'absolutePath'  => realpath(dirname(__FILE__)),
	'absoluteURL'   => $absURL,
  'editURL'       => $absURL.'/edit'
);

/**
 * Chargement des paramètres
 * @var Settings $settings
 */
if ('classes/LocalSettings.php'){
	$settings = new LocalSettings($args);
}else{
	$settings = new Settings($args);
}
//session_start();

$Content = new \Content\ContentManager();
$blockTypes = $Content->getBlockTypes();
$cssFiles = $Content->getCssFiles();
$adminMode = false;
$requestedPage = 'index.json';


$exp = '#'.str_replace('/', '\\/', str_replace('index.php', '',  $_SERVER['PHP_SELF'])).'(\w*)(?:\/|)(.*)#is';
if (preg_match($exp, $_SERVER['REQUEST_URI'], $match)) {
	header("Status: 200 OK", false, 200);
	if ($match[1] == 'edit') $adminMode = true;
	if (isset($match[2]) and !empty($match[2])) $requestedPage = $match[2];
}
if (isset($_REQUEST['edit'])){
	$adminMode = true;
}

if (isset($_REQUEST['request'])){
	$Content->processRequest();
}
//$isLoggedIn = \Auth\Login::checkAuth();

$page = $Content->addPageFromJSON($requestedPage);
if ($page !== false)	{
	if ($adminMode){
		$Content->editPage($page);
	}else{
		$page->toHTML();
	}

}
