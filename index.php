<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 09:51
 */
spl_autoload_register(function ($class) {
	if (preg_match('/^Content\\\\Themes\\\\(\w*)/i', $class, $matches)){
		@include_once 'classes/' . str_replace("\\", "/", $class) . '/' . $matches[1] . '.php';
	}else{
		@include_once 'classes/' . str_replace("\\", "/", $class) . '.php';
	}
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
$themes = $Content->getThemes();

$adminMode = false;
$requestedPage = 'index.json';


if ($settings->prettyURL) {
	$exp = '#'.str_replace('/', '\\/', str_replace('index.php', '',  $_SERVER['PHP_SELF'])).'(\w*)(?:\/|)(.*)#is';
	if (preg_match($exp, $_SERVER['REQUEST_URI'], $match)) {
		header("Status: 200 OK", false, 200);
		if ($match[1] == 'edit') {
			$adminMode = true;
		}elseif (!empty($match[1])){
			$requestedPage = $match[1];
		}elseif (isset($match[2]) and !empty($match[2])) {
			$requestedPage = $match[2];
		}
	}
}else{
	if (isset($_REQUEST['page'])){
		$requestedPage = $_REQUEST['page'];
	}
	if (isset($_REQUEST['edit'])){
		$adminMode = true;
	}
}


if (isset($_REQUEST['request'])){
	$Content->processRequest();
}
//$isLoggedIn = \Auth\Login::checkAuth();
if ($adminMode){
	switch ($requestedPage){
		case 'pages':
			$Content->listPages();
			break;
		default:
			$page = $Content->addPageFromJSON($requestedPage);
			if ($page !== false)	{
				$Content->editPage($page);
			}else{
				die('<h1>Erreur : Impossible de charger la page <code><?php echo $requestedPage; ?></code> !');
			}
	}
}else{
	$page = $Content->addPageFromJSON($requestedPage);
	if ($page !== false)	{
		$page->toHTML();
	}else{
		?><h1>Erreur : Impossible de charger la page <code><?php echo $requestedPage; ?></code> !</h1><?php
	}
}
