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
$args = array(
	'absolutePath'  => realpath(dirname(__FILE__)),
	'absoluteURL'   => rtrim($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].str_replace('index.php', '', $_SERVER['PHP_SELF']), '/')
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
