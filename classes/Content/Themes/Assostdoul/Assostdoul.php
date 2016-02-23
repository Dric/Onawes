<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 24/01/2016
 * Time: 20:23
 */

namespace Content\Themes;

use Content\Menu;
use Content\Theme;
use Template;

class Assostdoul extends Theme{

	protected $title = 'Association St Doul';

	public function toHTMLHeader(Menu $menu = null, $title = '', $cssFiles = null){
		global $settings;
		Template::addJsToFooter('<script src="'.$this->getUrlThemeBase().'/js/assostdoul.js"></script>');
		if (empty($title)) $title = $this->title;
		if (empty($cssFiles)) $cssFiles = $this->cssFiles;
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
				<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-57x57.png">
				<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-60x60.png">
				<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-72x72.png">
				<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-76x76.png">
				<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-114x114.png">
				<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-120x120.png">
				<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-144x144.png">
				<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-152x152.png">
				<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/apple-touch-icon-180x180.png">
				<link rel="icon" type="image/png" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/favicon-32x32.png" sizes="32x32">
				<link rel="icon" type="image/png" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/android-chrome-192x192.png" sizes="192x192">
				<link rel="icon" type="image/png" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/favicon-96x96.png" sizes="96x96">
				<link rel="icon" type="image/png" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/favicon-16x16.png" sizes="16x16">
				<link rel="manifest" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/manifest.json">
				<link rel="mask-icon" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
				<link rel="shortcut icon" href="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/favicon.ico">
				<meta name="msapplication-TileColor" content="#da532c">
				<meta name="msapplication-TileImage" content="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/mstile-144x144.png">
				<meta name="msapplication-config" content="<?php echo $this->getUrlThemeBase(); ?>/img/favicons/browserconfig.xml">
				<meta name="theme-color" content="#000000">

			</head>
			<body data-spy="scroll" data-target="#navigationMenu" data-offset="60">
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
					<header>
						<div id="nav-wrapper" class="">
							<nav class="navbar navbar-default navbar-fixed-top container">
								<!-- Brand and toggle get grouped for better mobile display -->
								<div class="navbar-header">
									<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navigationMenu" aria-expanded="false">
										<span class="sr-only">Menu</span>
										<span class="fa fa-bars"></span>
									</button>
									<a class="navbar-brand" href="<?php echo $settings->absoluteURL; ?>" >Association St Doul'</a>
								</div>

								<!-- Collect the nav links, forms, and other content for toggling -->
								<div class="collapse navbar-collapse" id="navigationMenu">
									<?php
									if (!empty($menu)) {
										$menu->addCSSClass('nav navbar-nav');
										$menu->toHTML();
									}
									?>

								</div><!-- /.navbar-collapse -->
							</nav>
						</div>
					</header>
					<div id="page-content-wrapper" class="container">
						<div id="logo" class="row">
								<div class="col-md-3">
									<a href="<?php echo $settings->absoluteURL; ?>"><img id="asso-logo" alt="Association St Doul" src="<?php echo $this->urlThemeBase; ?>/img/logo.jpg" /></a>
								</div>
								<div class="col-md-6" id="logo-text">
									<h1><a href="<?php echo $settings->absoluteURL; ?>" title="Association St Doul - Association sportive Volley-Futsal-Badminton">Association St Doul'</a><br/><small>Association sportive à St Doulchard</small></h1>
									<div id="sports-logos-anchor"></div>
									<div id="sports-logos">
										<a href="<?php echo Template::createURL(array('page' => 'futsal')); ?>" title="Section Futsal"><img alt="Futsal" src="<?php echo $this->urlThemeBase; ?>/img/logo-futsal.png" /></a>
										<a href="<?php echo Template::createURL(array('page' => 'volley')); ?>" title="Section VolleyBall"><img alt="Volley" src="<?php echo $this->urlThemeBase; ?>/img/logo-volley.png" /></a>
										<a href="<?php echo Template::createURL(array('page' => 'badminton')); ?>" title="Section Badminton"><img alt="Badminton" src="<?php echo $this->urlThemeBase; ?>/img/logo-badminton.png" /></a>
									</div>
								</div>
						</div>
						<main class="page-content inset space-top">
		<?php
	}

	public function toHTMLFooter(){
		global $settings;
		?>
						</main>
					</div>
					<footer>
						<?php Template::footer(); ?>
						<div class="col-md-4 col-md-offset-8 text-right">©2004-<?php echo date('Y', time())?> <a title="" data-original-title="" href="<?php echo $settings->absoluteURL; ?>">AssoStDoul.fr</a> <a data-original-title="Administration" href="<?php echo Template::createURL(array('edit' => true)); ?>" class="tooltip-top" title=""><i class="fa fa-cog"></i></a> - <a class="tooltip-top" title="Oh No, Another Website Editor System !" href="https://github.com/Dric/Onawes">Onawes</a></div>
					</footer>
				</div>
				<?php Template::jsFooter(); ?>
			</body>
		</html>
		<?php
	}
}