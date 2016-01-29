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
	protected $title = 'Administration';
	protected $cssFiles = array('css/onawes.css');
	/**
	 * @var Menu
	 */
	protected $menu = null;

	public function toHTMLHeader($cssFiles = null){
		global $settings;
		$this->setSidebarMenu();
		if (empty($cssFiles)) $cssFiles = $this->cssFiles;
		Template::header($cssFiles, $this->title);
		?>
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
					<footer>
						<?php Template::footer(); ?>
						<?php if ($settings->debug) echo ' | Mode debug activé | '; ?>
						<abbr class="tooltip-top" title="Oh No, Another Website Editor System !">Onawes</abbr> 2016
					</footer>
				</div>
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

	public function populateCssFiles(){
		global $settings;
		$this->cssFiles = array($settings->absoluteURL.'/css/onawes.css');
	}

}