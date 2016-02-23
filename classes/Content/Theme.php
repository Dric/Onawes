<?php

/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 24/01/2016
 * Time: 14:58
 */
namespace Content;

use FileSystem\File;
use FileSystem\Fs;
use Template;

class Theme {
	protected $title = 'Administration';
	/**
	 * @var string[]
	 */
	protected $cssFiles = array();
	protected $path = null;
	protected $urlThemeBase = null;

	public function __construct(){
		global $settings;
		$this->path = $settings->absolutePath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Content'.DIRECTORY_SEPARATOR.'Themes'.DIRECTORY_SEPARATOR.str_replace('Content\\Themes\\', '', get_called_class());
		$this->urlThemeBase = $settings->absoluteURL.'/classes/Content/Themes/'.str_replace('Content\\Themes\\', '', get_called_class());
		$this->populateCssFiles();
	}

	public function toHTMLHeader(Menu $menu = null, $title = null, $cssFiles = null){
		global $settings;
		if (empty($title)) $title = $this->title;
		if (empty($cssFiles)) $cssFiles = $this->cssFiles;
		?>
		<!DOCTYPE html>
		<html lang="fr">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta name="description" content="Onawes">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
				<!--[if lt IE 9]>
				<script src="<?php echo $settings->absoluteURL; ?>/js/html5shiv.js"></script>
				<script src="<?php echo $settings->absoluteURL; ?>/js/respond.min.js"></script>
				<![endif]-->
				<title><?php echo $title; ?></title>

				<!-- The CSS -->
				<?php foreach ($cssFiles as $cssFile){ ?>
					<link href="<?php echo $cssFile; ?>" rel="stylesheet">
				<?php } ?>
				<?php Template::cssHeader(); ?>
				<link href="<?php echo $settings->absoluteURL; ?>/css/animate.css" rel="stylesheet">
				<?php
				// On ajoute le contenu de $header
				foreach (Template::HTMLHeader() as $headerLine){
					echo $headerLine.PHP_EOL;
				}
				?>
				<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-57x57.png">
				<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-60x60.png">
				<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-72x72.png">
				<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-76x76.png">
				<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-114x114.png">
				<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-120x120.png">
				<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-144x144.png">
				<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-152x152.png">
				<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-180x180.png">
				<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-32x32.png" sizes="32x32">
				<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/android-chrome-192x192.png" sizes="192x192">
				<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-96x96.png" sizes="96x96">
				<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-16x16.png" sizes="16x16">
				<link rel="manifest" href="<?php echo $settings->absoluteURL; ?>/img/favicons/manifest.json">
				<link rel="shortcut icon" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon.ico">
				<meta name="msapplication-TileColor" content="#ffc40d">
				<meta name="msapplication-TileImage" content="<?php echo $settings->absoluteURL; ?>/img/favicons/mstile-144x144.png">
				<meta name="msapplication-config" content="<?php echo $settings->absoluteURL; ?>/img/favicons/browserconfig.xml">
				<meta name="theme-color" content="#ffffff">
			</head>
			<body>
				<div id="wrapper">
				<!-- Si javascript n'est pas activé, on prévient l'utilisateur que ça peut merder... -->
				<noscript>
					<div class="alert alert-info">
						<p>Ce site ne fonctionnera pas sans Javascript !</p>
					</div>
					<style>
						.tab-content>.tab-pane{
							display: block;
						}
					</style>
				</noscript>
				<div id="page-content-wrapper" class="container">
					<div class="content-header row">
						<div class="col-md-12">
							<h1>
								<a href="<?php echo $settings->absoluteURL; ?>"><?php echo $settings->scriptTitle; ?></a>
							</h1>
						</div>
					</div>
					<div class="page-content inset row">
		<?php
	}

	public function toHTMLFooter(){
		global $settings;
		?>
					</div>
				</div>
				<footer>
					<?php Template::footer(); ?>
					<?php if ($settings->debug) echo ' | Mode debug activé | '; ?>
					<abbr class="tooltip-top" title="Oh No, Another Website Editor System !">Onawes</abbr> 2016
				</footer>
			</div>
			<?php Template::jsFooter(); ?>
			</body>
		</html>
		<?php
	}

	public function getTitleURL(){
		global $settings;
		$url = strtolower(str_replace('Content\\Themes\\', '', get_called_class()));
		if ($settings->prettyURL){
			return $settings->absoluteURL.DIRECTORY_SEPARATOR.$url;
		}else{
			return $settings->absoluteURL.DIRECTORY_SEPARATOR.'?page='.$url;
		}
	}

	public function populateCssFiles(){
		$fs = new Fs($this->path);
		$cssFiles = $fs->getFilesInDir(null,'css', array('extension'), true);
		/** @var File $cssFile */
		foreach ($cssFiles as $cssFile){
			if (!in_array($cssFile->baseName, $this->cssFiles))	$this->cssFiles[] = $this->urlThemeBase.'/'.$cssFile->baseName;
		}
	}

	/**
	 * @return null|string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return null|string
	 */
	public function getUrlThemeBase() {
		return $this->urlThemeBase;
	}
}