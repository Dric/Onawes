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

	public function toHTMLHeader($cssFiles = null){
		global $settings;
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