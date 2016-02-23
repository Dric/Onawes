<?php

// PHP Classes auto-loading...
spl_autoload_register(function ($class) {
	if (preg_match('/^Content\\\\Themes\\\\(\w*)/i', $class, $matches)) {
		@include_once 'classes/' . str_replace("\\", "/", $class) . '/' . $matches[1] . '.php';
	}elseif($class == 'PHPMailer'){
		@include_once 'classes'.DIRECTORY_SEPARATOR.'PHPMailer'.DIRECTORY_SEPARATOR.'class.'.strtolower($class).'.php';
	}else{
		@include_once 'classes/' . str_replace("\\", "/", $class) . '.php';
	}
});

// Determining dynamic settings...
$isHTTPS = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'];
$absURL = rtrim((($isHTTPS) ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
$args = array(
	'absolutePath'  => realpath(dirname(__FILE__)),
	'absoluteURL'   => $absURL
);

// Loading settings...
if (file_exists(realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'LocalSettings.php')){
	/** @var Settings $settings */
	$settings = new LocalSettings($args);
}else{
	// This is a new install...
	if (isset($_REQUEST['createLocalSettings'])){
		if (\Components\Installer::createLocalSettings() === true){
			header('location: '.$args['absoluteURL']);
			exit();
		};
	}
	$settings = new Settings($args);
	\Components\Installer::doSetup();
}

// Defaulting vars...
$adminMode = false;
$requestedPage = null;

// Processing pretty URL if activated...
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
	// No pretty url, but we must return the requested page...
	if (isset($_REQUEST['page'])){
		$requestedPage = $_REQUEST['page'];
	}
	if (isset($_REQUEST['edit'])){
		$adminMode = true;
	}
}

session_start();

// Populating stuff...
$Content = new \Content\ContentManager();

// Loading Theme...
$themePHPClass = (isset($Content->getSiteSettings()['theme'])) ? 'Content\\Themes\\'.$Content->getSiteSettings()['theme'] : 'Content\\Themes\\Home';
$theme = new $themePHPClass();

// Let's process Login/logoff...
if (isset($_REQUEST['tryLogin'])){
	Security::tryLogin();
}elseif(isset($_REQUEST['logoff'])){
	Security::deleteCookie();
	header('location: '.$settings->absoluteURL);
}

// Checking if authenticated...
if ($adminMode and !Security::isLoggedIn()){
	Security::loginForm();
}

// We've got an asynchronous admin request here !
if (isset($_REQUEST['ajax']) and Security::isLoggedIn()){
	// Let's be sure web browser understand the response is a json array...
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


// Loading default page if none requested...
if (empty($requestedPage) and !$adminMode){
	$requestedPage = $Content->getSiteSettings()['mainPage'];
}

// We've got a request here !
if (isset($_REQUEST['request'])){
	$Content->processRequest();
}

// Displaying content...
if ($adminMode){
	// Displaying admin pages...
	switch ($requestedPage){
		case 'homePage':
		case null:
			$Content->editHome();
			break;
		case 'pages':
			$Content->listPages();
			break;
		case 'site':
			$Content->editSite();
			break;
		case 'news':
			$Content->editNews();
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
	// Displaying front pages...
	if (strpos($requestedPage, '.json') === false){
		$requestedPage .= '.json';
	}
	$page = $Content->addPageFromJSON($requestedPage);
	if ($page !== false)	{
		$page->toHTML($theme);
	}else{
		// No page found !
		?><h1>Erreur : Impossible de charger la page <code><?php echo $requestedPage; ?></code> !</h1><?php
	}
}
