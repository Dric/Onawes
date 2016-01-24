<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:39
 */

namespace Content;


use Alerts\Alert;
use DOMDocument;
use FileSystem\Fs;
use simple_html_dom_node;
use Template;

class Page {

	/**
	 * Page file name
	 * @var string
	 */
	protected $fileName = null;
	/**
	 * @var string
	 */
	protected $title = null;
	/**
	 * @var Row[]
	 */
	protected $rows = array();

	/**
	 * @var string
	 */
	protected $cssFile = null;

	protected $theme = null;

	/**
	 * @var int[]
	 */
	protected $rowsOrder = array();

	protected $CSSClasses = array();

	public function __construct($fileName, $title = null){
		$this->fileName = $fileName;
		if (empty($title)) $this->title = $this->fileName;
	}
	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;
	}

	/**
	 * Display HTML content of page
	 */
	public function toHTML(){
		$this->toHTMLHeader();
		/** @var Row $row */
		foreach ($this->rows as $row){
			$row->toHTML();
		}
		$this->toHTMLFooter();
	}

	public function toHTMLHeader(){
		global $settings, $adminMode;
		if ($adminMode){
			$subtitle = 'Administration';
			$titleLink = $settings->editURL;
			$cssFile = 'onawes.css';
		}else{
			$subtitle = null;
			$titleLink = null;
			$cssFile = $this->cssFile;
		}
		Template::header($cssFile, $subtitle);
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
							<a href="<?php echo (!empty($titleLink)) ? $titleLink : $settings->absoluteURL; ?>"><?php echo $settings->scriptTitle; ?></a> <?php if ($adminMode){ ?><a href="<?php echo $settings->absoluteURL; ?>" title="Revenir au site" class="btn btn-sm btn-default"><i class="fa fa-link"></i></a><?php } ?>
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

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'CSSClasses'  => $this->CSSClasses,
			'cssFile'     => $this->cssFile,
			'theme'       => $this->theme
		);
		foreach ($this->rowsOrder as $rowId => $position){
			$array['rows'][$rowId] = $this->rows[$rowId]->toArray();
		}
		return $array;
	}

	/**
	 * @param string $element
	 *
	 * @return array|null|string
	 */
	public function getElementCSS($element = null) {
		if (empty($element)) return $this->CSSClasses;
		if (isset($this->CSSClasses[$element])) return $this->CSSClasses[$element];

		new Alert('debug','L\'élément <code>'.$element.'</code> n\'est pas référencé !');
		return null;
	}

	/**
	 * Ajoute un élément avec sa classe
	 * @param string $element
	 * @param string $CSSClass
	 */
	public function addElementCSS($element, $CSSClass) {
		$this->CSSClasses[$element] = $CSSClass;
	}



	/**
	 * @param Row  $row
	 * @param string $replaceRowId Row Id to replace in case of a row renaming
	 * @param int    $position
	 *
	 * @return bool
	 */
	public function addRow(Row $row, $replaceRowId = null, $position = null) {
		if ($row->getId() == 'newRow'){
			if (is_null($position)){
				new Alert('error', 'Erreur : Ajout de nouvelle ligne - la position n\'est pas renseignée !');
				return false;
			}
			foreach ($this->rowsOrder as $rowId => $pos){
				if ($pos >= $position){
					$this->rowsOrder[$rowId] = $pos + 1;
				}
			}
			$row->setId($replaceRowId);
			$this->rows[$row->getId()] = $row;
			$this->rowsOrder[$row->getId()] = $position;
		}elseif (!(empty($replaceRowId))){
			$newPosition = (is_null($position)) ? $this->rowsOrder[$replaceRowId] : $position;
			unset($this->rows[$replaceRowId]);
			unset($this->rowsOrder[$replaceRowId]);
			if (!is_null($position)){
				if (in_array($position, $this->rowsOrder)){
					foreach ($this->rowsOrder as $rowId => $pos){
						if ($pos >= $position){
							$this->rowsOrder[$rowId] = $pos + 1;
						}
					}
				}
			}
			$this->rows[$row->getId()] = $row;
			$this->rowsOrder[$row->getId()] = $newPosition;
		}else{
			$this->rows[$row->getId()] = $row;
			$this->rowsOrder[$row->getId()] = (is_null($position)) ? array_search($row->getId(), array_keys($this->rows))+1 : $position;
			/*
			 * @from <http://stackoverflow.com/a/3145647>
			 * Checking if duplicates values exist
			 */
			if (in_array($position, $this->rowsOrder) and count($this->rowsOrder) !== count(array_unique($this->rowsOrder))){
				foreach ($this->rowsOrder as $rowId => $pos){
					if ($pos >= $position and $rowId != $row->getId()){
						$this->rowsOrder[$rowId] = $pos + 1;
					}
				}
			}

		}
		asort($this->rowsOrder);
		return true;
	}

	public function removeRow($rowId){
		unset($this->rows[$rowId]);
		$position = $this->rowsOrder[$rowId];
		unset($this->rowsOrder[$rowId]);
		foreach ($this->rowsOrder as $rowOrderID => $pos){
			if ($pos > $position){
				$this->rowsOrder[$rowOrderID] = $pos - 1;
			}
		}
		asort($this->rowsOrder);
		return true;
	}

	/**
	 * Move a row
	 *
	 * @param string $rowId Row ID
	 * @param string $moveDirection Possible values : `before` or `after`
	 *
	 * @return bool
	 */
	public function moveRow($rowId, $moveDirection){
		$oldPosition = $this->rowsOrder[$rowId];
		if ($moveDirection == 'before'){
			$position = $oldPosition - 1;
		}elseif($moveDirection == 'after'){
			$position = $oldPosition + 1;
		}else{
			new Alert('error', 'Erreur : Impossible de déplacer la ligne car la direction de déplacement est incorrecte (<code>'.$moveDirection.'</code>)!');
			return false;
		}
		$rowToSwitch = array_search($position, $this->rowsOrder);
		if (!$rowToSwitch){
			new Alert('error', 'Erreur : Impossible de déplacer la ligne.');
			return false;
		}
		$this->rowsOrder[$rowId] = $position;
		$this->rowsOrder[$rowToSwitch] = $oldPosition;
		asort($this->rowsOrder);
		return true;
	}
	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * This function returns rows sorted by position
	 * @return Row[]
	 */
	public function getRows() {
		$ret = array();
		// `$this->rowsOrder` is always sorted by position when adding a row
		foreach ($this->rowsOrder as $rowId => $position){
			$ret[$rowId] = $this->rows[$rowId];
		}
		return $ret;
	}

	public function getRowPosition($rowId){
		return (isset($this->rowsOrder[$rowId])) ? $this->rowsOrder[$rowId] : 0;
	}

	/**
	 * @return string
	 */
	public function getCssFile() {
		return $this->cssFile;
	}

	/**
	 * @param string $cssFile
	 *
	 * @return bool
	 */
	public function setCssFile($cssFile) {
		global $settings;
		$fs = new Fs($settings->absolutePath.DIRECTORY_SEPARATOR.'css');
		if ($fs->fileExists($cssFile)){
			$this->cssFile = $cssFile;
			return true;
		}else{
			new Alert('error', 'Erreur : le fichier CSS <code>'.$cssFile.'</code> n\'existe pas !');
			return false;
		}
	}

	/**
	 * @return null
	 */
	public function getTheme() {
		return $this->theme;
	}

	/**
	 * @param null $theme
	 */
	public function setTheme($theme) {
		$this->theme = $theme;
	}
}