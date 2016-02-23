<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 24/01/2016
 * Time: 13:16
 */

namespace Content\Themes;

use Content\Menu;
use Content\MenuItem;
use Template;
use Content\Theme;

class Edit extends Theme{
	protected $title = 'Onawes - Administration';
	/**
	 * @var Menu
	 */
	protected $menu = null;

	public function toHTMLHeader(Menu $menu = null, $title = '', $cssFiles = null){
		global $settings;
		Template::addCSSToHeader('<link href="'.$settings->absoluteURL.'/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" rel="stylesheet">');
		Template::addCSSToHeader('<link href="'.$settings->absoluteURL.'/js/bootstrap-fileinput/css/fileinput.min.css" rel="stylesheet">');
		Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>');
		Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/bootstrap-fileinput/js/fileinput.min.js"></script>');
		Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/bootstrap-fileinput/js/fileinput_locale_fr.js"></script>');
		Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/jquery.eqheight.js"></script>');
		Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/bootstrap-slider/bootstrap-slider.min.js"></script>');
		Template::addJsToFooter('<script type="text/javascript" src="'.$this->getUrlThemeBase().'/js/edit.js"></script>', 1000);
		$this->setSidebarMenu();
		if (empty($title)) $title = $this->title;
		if (empty($cssFiles)) $cssFiles = $this->cssFiles;
		//Template::header($cssFiles, $title);
		?>
		<!DOCTYPE html>
		<html lang="fr">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="description" content="<?php echo $settings->scriptTitle; ?>">
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
				<!-- Sidebar -->
				<div id="sidebar-wrapper">
					<?php echo $this->menu->toHTML(); ?>
				</div>
				<!-- /#sidebar-wrapper -->
				<div id="page-content-wrapper">
					<div class="container-fluid">
						<div class="content-header row">
							<div class="col-md-12">
								<a href="#menu-toggle" class="btn btn-default btn-sm visible-sm visible-xs" id="menu-toggle"><i class="fa fa-bars"></i></a>
								<h1>
									<a href="<?php echo self::getTitleURL(); ?>"><?php echo $settings->scriptTitle; ?></a> <a href="<?php echo $settings->absoluteURL; ?>" title="Revenir au site" class="btn btn-sm btn-default"><i class="fa fa-link"></i></a>
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
				</div>
				<footer>
					<?php Template::footer(); ?>
					<?php if ($settings->debug) echo ' | Mode debug activé | '; ?>
					<img src="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-16x16.png" alt="Onawes logo"/> <a class="tooltip-top" title="Oh No, Another Website Editor System !" href="https://github.com/Dric/Onawes">Onawes</a> 2016
				</footer>
			</div>
		<?php Template::jsFooter(); ?>
		</body>
		<?php
	}

	protected function setSidebarMenu(){
		global $settings;
		$menu = new Menu();
		$menu->setId('sidebarMenu');
		$menu->addCSSClass('sidebar-nav');
		$titleItem = new MenuItem('menuTitle', 'Administration', $this->getTitleURL(), 'sidebarMenu');
		$titleItem->addCSSClass('sidebar-brand');
		$menu->addItem($titleItem);
		$menu->addItem(new MenuItem('pages', 'Pages', Template::createURL(array('edit'=>true, 'page'=>'pages')), 'sidebar-nav'));
		$menu->addItem(new MenuItem('siteSettings', 'Paramètres du site', Template::createURL(array('edit'=>true, 'page'=>'site')), 'sidebar-nav'));
		$menu->addItem(new MenuItem('news', 'Gestion des articles', Template::createURL(array('edit'=>true, 'page'=>'news')), 'sidebar-nav'));
		$menu->addItem(new MenuItem('logoff', 'Déconnexion', Template::createURL(array('logoff'=>'true')), 'sidebar-nav'));
		$this->menu = $menu;
	}

	public function getTitleURL(){
		global $settings;
		if ($settings->prettyURL){
			return $settings->absoluteURL.'/edit';
		}else{
			return $settings->absoluteURL.'/?edit';
		}
	}

	public function toHTMLEditManual(){
		global $settings;
		$this->toHTMLHeader();
		?>
		<div class="row">
			<div class="col-md-12">
				<h2>Administration de <?php echo $settings->scriptTitle; ?></h2>
				<br><br>
				<ul>
					<li>Si vous vous trompez dans une modification, vous pouvez toujours restaurer le fichier de backup (sauvegarde) et revenir comme avant vos sauvegardes, sous réserve que vous n'ayez pas sauvegardé plusieurs fois la page.</li>
					<li>Merci de votre attention !</li>
				</ul>
			</div>
		</div>
		<?php
		$this->toHTMLFooter();
	}
}