<?php

/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 24/01/2016
 * Time: 14:58
 */
namespace Content;

class Theme {
	static protected $title = 'Administration';
	static protected $cssFile = 'onawes.css';

	public static function header($cssFile = null){
		global $settings;
		$titleLink = $settings->editURL;
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
				<div id="page-content-wrapper" class="container">
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
			<?php Template::jsFooter(); ?>
		</body>
		<?php
	}

	protected static function getTitleURL(){
		global $settings;
		$url = strtolower(str_replace('Themes\\', '', get_called_class()));
		if ($settings->prettyURL){
			return $settings->absoluteURL.DIRECTORY_SEPARATOR.$url;
		}else{
			return $settings->absoluteURL.DIRECTORY_SEPARATOR.'?page='.$url;
		}
	}
}