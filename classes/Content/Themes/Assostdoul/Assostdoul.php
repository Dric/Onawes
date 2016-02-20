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
		Template::header($cssFiles, $title);
		?>
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
	<div class="container">
		<div id="logo" class="row">
				<div class="col-md-3">
					<a href="<?php echo $settings->absoluteURL; ?>"><img id="asso-logo" alt="Association St Doul" src="<?php echo $this->urlThemeBase; ?>/img/logo.jpg" /></a>
				</div>
				<div class="col-md-6" id="logo-text">
					<h1><a href="<?php echo $settings->absoluteURL; ?>" title="Association St Doul - Association sportive Volley-Futsal-Badminton">Association St Doul'</a><br/><small>Association sportive à St Doulchard</small></h1>
					<div id="sports-logos-anchor"></div>
					<div id="sports-logos">
						<a href="<?php echo Template::createURL(array('page' => 'futsal')); ?>" title="Section Futsal"><img src="<?php echo $this->urlThemeBase; ?>/img/logo-futsal.png" /></a>
						<a href="<?php echo Template::createURL(array('page' => 'volley')); ?>" title="Section VolleyBall"><img src="<?php echo $this->urlThemeBase; ?>/img/logo-volley.png" /></a>
						<a href="<?php echo Template::createURL(array('page' => 'badminton')); ?>" title="Section Badminton"><img src="<?php echo $this->urlThemeBase; ?>/img/logo-badminton.png" /></a>
					</div>
				</div>
		</div>
		<div class="page-content inset space-top">
		<?php
	}
}