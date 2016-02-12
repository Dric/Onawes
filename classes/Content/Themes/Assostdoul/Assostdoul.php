<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 24/01/2016
 * Time: 20:23
 */

namespace Content\Themes;

use Content\Theme;
use Template;

class Assostdoul extends Theme{

	public function toHTMLHeader($cssFiles = null){
		global $settings;
		if (empty($cssFiles)) $cssFiles = $this->cssFiles;
		Template::header($cssFiles, $this->title);
		?>
		<body data-spy="scroll" data-target=".navbar" data-offset="60">
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
					<div class="">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navigation-menu" aria-expanded="false">
								<span class="sr-only">Menu</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="<?php echo $settings->absoluteURL; ?>" >Association St Doul'</a>
						</div>

						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse" id="navigation-menu">
							<ul class="nav navbar-nav">
								<li><a href="#news">Actualités</a></li>
								<li><a href="#sections">Sections</a></li>
								<li><a href="#gymnase">Gymnase</a></li>
								<li><a href="#contact">Contact</a></li>
							</ul>
						</div><!-- /.navbar-collapse -->
					</div><!-- /.container-fluid -->
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
		<div class="row">
			<div class="col-md-4 col-md-offset-4" id="alert">
			</div>
		</div>
		<div class="space-top">
		<div class="page-content inset">
		<?php
	}
}