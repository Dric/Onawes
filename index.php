<?php
ini_set("display_errors", 1);
error_reporting(-1);
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 09:51
 */
spl_autoload_register(function ($class) {
	if (preg_match('/^Content\\\\Themes\\\\(\w*)/i', $class, $matches)) {
		@include_once 'classes/' . str_replace("\\", "/", $class) . '/' . $matches[1] . '.php';
	}elseif($class == 'PHPMailer'){
		@include_once 'classes'.DIRECTORY_SEPARATOR.'PHPMailer'.DIRECTORY_SEPARATOR.'class.'.strtolower($class).'.php';
	}else{
		@include_once 'classes/' . str_replace("\\", "/", $class) . '.php';
	}
});

// Définition de quelques variables
$isHTTPS = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'];
$absURL = rtrim((($isHTTPS) ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
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

$adminMode = false;
$requestedPage = null;

if ($settings->prettyURL) {
	/*
	 * Test it on regex101.com
	 * regex = (edit|)(?:\/|)([a-z-A-Z.]*)(?:\/|)(\?.*|)
	 * tests :
	 *  * edit/index.json/?request=delPage&test=true
	 *  * edit
	 *  * edit/index.json/
	 *  * edit/?request=delPage&test=true
	 *  * index.json
	 *  * ?request=delPage&test=true
	 * Returns (for 1st test) :
	 * 1.	`edit`
	 * 2.	`index.json`
	 * 3.	`?request=delPage&test=true`
	 */
	$exp = '#'.str_replace('/', '\\/', str_replace('index.php', '',  $_SERVER['SCRIPT_NAME'])).'(edit|)(?:\/|)([a-z-A-Z.]*)(?:\/|)(\?.*|)#is';
	if (preg_match($exp, $_SERVER['REQUEST_URI'], $match)) {
		header("Status: 200 OK", false, 200);
		if (!empty($match[1]) and $match[1] == 'edit'){
			$adminMode = true;
		}
		if (isset($match[2]) and !empty($match[2])) {
			$requestedPage = str_replace('/', '', $match[2]);
		}
		// $match[3] is the same as $_REQUEST
	}
}else{
	if (isset($_REQUEST['page'])){
		$requestedPage = $_REQUEST['page'];
	}
	if (isset($_REQUEST['edit'])){
		$adminMode = true;
	}
}

//session_start();
$Content = new \Content\ContentManager();
$blockTypes = $Content->getBlockTypes();
$cssFiles = $Content->getCssFiles();
$themes = $Content->getThemes();

if (isset($_REQUEST['ajax'])){
	header('Content-Type: application/json');
	switch ($_REQUEST['ajax']){
		case 'showMediaManager':
			$Content->ajaxMediaManager();
			break;
		case 'reloadGallery':
			$Content->ajaxShowGallery();
			break;
		case 'uploadFile':
			$Content->ajaxUploadFile();
			break;
		case 'deleteFile':
			$Content->ajaxDeleteFile();
			break;
	}
	exit();
}

$themePHPClass = 'Content\\Themes\\'.$Content->getSiteSettings()['theme'];
$theme = new $themePHPClass();
if (empty($requestedPage)){
	$requestedPage = $Content->getSiteSettings()['mainPage'];
}

if (isset($_REQUEST['request'])){
	$Content->processRequest();
}
//$isLoggedIn = \Auth\Login::checkAuth();
if ($adminMode){
	switch ($requestedPage){
		case 'homePage':
			$Content->editHome();
			break;
		case 'pages':
			$Content->listPages();
			break;
		case 'site':
			$Content->editSite();
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
		$page->toHTML($theme);
	}else{
		?><h1>Erreur : Impossible de charger la page <code><?php echo $requestedPage; ?></code> !</h1><?php
	}
}
