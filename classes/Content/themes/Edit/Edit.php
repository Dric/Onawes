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
	protected static $title = 'Administration';
	protected static $cssFile = 'onawes.css';
	/**
	 * @var Menu
	 */
	protected static $menu = null;

	public static function header($cssFile = null){
		global $settings;
		self::setSidebarMenu();
		if (empty($cssFile)) $cssFile = self::$cssFile;
		Template::header($cssFile, self::$title);
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
					<?php echo self::$menu->toHTML(); ?>
				</div>
				<!-- /#sidebar-wrapper -->
				<div id="page-content-wrapper">
					<div class="container-fluid">
						<div class="content-header row">
							<div class="col-md-12">
								<h1>
									<a href="<?php echo self::getTitleURL(); ?>"><?php echo $settings->scriptTitle; ?></a> <a href="<?php echo $settings->absoluteURL; ?>" title="Revenir au site" class="btn btn-sm btn-default"><i class="fa fa-link"></i></a>
								</h1>
							</div>
						</div>
						<div class="page-content inset row">
		<?php
	}

	public static function footer(){
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

	protected static function setSidebarMenu(){
		global $settings;
		$menu = new Menu();
		$menu->setId('sidebarMenu');
		$menu->addCSSClass('sidebar-nav');
		$titleItem = new MenuItem('menuTitle', 'Administration', self::getTitleURL(), 'sidebarMenu');
		$titleItem->addCSSClass('sidebar-brand');
		$menu->addItem($titleItem);
		$link = ($settings->prettyURL) ? self::getTitleURL().'/pages' : self::getTitleURL().'&page=pages' ;
		$menu->addItem(new MenuItem('pages', 'Pages', $link, 'sidebar-nav'));

		self::$menu = $menu;
	}

	protected static function getTitleURL(){
		global $settings;
		if ($settings->prettyURL){
			return $settings->absoluteURL.DIRECTORY_SEPARATOR.'/edit';
		}else{
			return $settings->absoluteURL.DIRECTORY_SEPARATOR.'?edit';
		}
	}

}