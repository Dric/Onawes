<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:41
 */

namespace Content;


use Alerts\Alert;
use Content\Blocks\HTMLBlock;
use FileSystem\File;
use FileSystem\Fs;
use simple_html_dom;
use Template;

class ContentManager {

	protected $contentDir;
	/**
	 * @var Page
	 */
	protected $page = null;

	protected $blockTypes = array();

	protected $cssFiles = array();

	protected $url = null;

	public function __construct(){
		global $settings;
		$this->contentDir = $settings->absolutePath.DIRECTORY_SEPARATOR.$settings->contentDir;
		$this->populateBlockTypes();
		$this->populateCssFiles();
	}

	/**
	 * Save content page to disk
	 *
	 * @param Page $page
	 *
	 * @return bool
	 */
	protected function saveContent(Page $page){
		$fs = new Fs($this->contentDir);
		// A backup is made of the file when saving
		return $fs->writeFile($page->getFileName(), $page->toJSON(), false, true);
	}

	/**
	 * Populate Page from file
	 *
	 * @param string $fileName File Name
	 *
	 * @return bool
	 */
	public function addPageFromFile($fileName){
		// reading file
		$fs = new Fs($this->contentDir);
		$fileContent = $fs->readFile($fileName, 'string');
		// parsing html
		$parsed = new simple_html_dom();
		$parsed->load($fileContent);
		// Creating page
		$page = new Page($fileName);
		foreach ($parsed->find('.row') as $rowNode){
			// creating rows
			$row = new Row($rowNode->id, $rowNode->class, $rowNode->tag);
			/** @var simple_html_dom $rowBlock */
			foreach ($rowNode->children as $rowBlock){
				// Creating blocks
				$block = new HTMLBlock($rowBlock->id, $rowBlock->class, $rowBlock->tag);

				// Setting block widths
				$CSSClasses = explode(' ', $rowBlock->class);
				foreach ($CSSClasses as $class){
					$re = "/^col-(\\w{2})-(\\d+)/i";
					preg_match($re, $class, $matches);
					if (!empty($matches)){
						$block->setWidth($matches[1], (int)$matches[2]);
					}
				}
				// Finding title tag - this is the first tag in block
				$titleTag = $rowBlock->firstChild();
				if (in_array($titleTag->tag, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))){
					$re = "/h(\d)/i";
					preg_match($re, $titleTag->tag, $matches);
					$block->setTitle($matches[1], $titleTag->innertext);
					// Removing title from content
					$rowBlock->firstChild()->outertext = '';
				}
				// Setting block content
				$block->setContent($rowBlock->innertext);
				$row->addBlock($block);
			}

			// Adding row to page
			$page->addRow($row);
		}
		$this->page = $page;
		$this->saveContent($page);
		return $page;
	}

	public function addPageFromJSON($fileName){
		// reading file
		$fs = new Fs($this->contentDir);
		$fileContent = $fs->readFile($fileName, 'string');
		if (!$fileContent){
			return false;
		}
		// Parsing JSON
		$jsonArray = json_decode($fileContent, true);
		$page = new Page($fileName);
		if (!empty($jsonArray['title'])) $page->setTitle($jsonArray['title']);
		if (!empty($jsonArray['cssFile'])) $page->setCssFile($jsonArray['cssFile']);
		foreach ($jsonArray['rows'] as $jsonRow){
			$row = new Row ($jsonRow['id'], $fileName);
			$row->setTitle($jsonRow['title']);
			$row->setTag($jsonRow['tag']);
			foreach ($jsonRow['CSSClasses'] as $class){
				$row->addCSSClass($class);
			}
			if (isset($jsonRow['blocks'])) {
				foreach ($jsonRow['blocks'] as $jsonBlock) {
					$block = new HTMLBlock ($jsonBlock['id'], $row->getId());
					$block->setTitle($jsonBlock['titleLevel'], $jsonBlock['title']);
					$block->setTag($jsonBlock['tag']);
					foreach ($jsonBlock['CSSClasses'] as $class) {
						$block->addCSSClass($class);
					}
					foreach ($jsonBlock['widths'] as $width => $size) {
						$block->setWidth($width, $size);
					}
					$block->setContent($jsonBlock['content']);
					$row->addBlock($block);
				}
			}
			$page->addRow($row);
		}
		return $page;
	}


	public function editPage(Page $page){
		global $settings, $cssFiles;
		$rowPosition = null;
		$refRow = null;
		$fileName = $page->getFileName();
		if (isset($_REQUEST['addRow'])){
			if (isset($_REQUEST['refRow'])){
				if (in_array($_REQUEST['addRow'], array('before', 'after'))) $rowPosition =  $_REQUEST['addRow'];
				$refRow = \Sanitize::SanitizeForDb($_REQUEST['refRow'], false);
			}
		}
		$page->toHTMLHeader(true);
		?><h2>Edition de la page <code><?php echo $page->getTitle(); ?></code></h2><?php

		?>
		<div class="row">
			<div class="col-md-12" id="page_<?php echo $page->getFileName(); ?>">
				<div class="panel panel-default">
					<div class="panel-body">
						<form class="well form-horizontal" action="<?php echo $settings->editURL; ?>#page_<?php echo $page->getFileName(); ?>">
							<div class="form-group">
								<label class="col-sm-5 control-label" for="cssFile">Style à appliquer</label>
								<div class="col-sm-5">
									<select class="form-control" id="cssFile" name="cssFile" required>
										<?php
										foreach ($cssFiles as $cssFile){
											?><option <?php if ($cssFile == $page->getCssFile()) echo 'selected'; ?>><?php echo $cssFile; ?></option><?php
										}
										?>
									</select>
								</div>
							</div>
							<input type="hidden" name="fileName" value="<?php echo $fileName; ?>">
							<input type="hidden" name="edit">
							<button type="submit" class="btn btn-primary" name="request" value="savePage">Enregistrer</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php
		$nbRows = 0;
		/** @var Row $row */
		foreach ($page->getRows() as $index => $row){
			// Nouvelle ligne avant le bloc référent
			if ($refRow == $row->getId() and $rowPosition == 'before'){
				$nbRows++;
				$addRow = new Row('newRow', $fileName);
				$addRow->setContentForm($fileName, $page->getRowPosition($row->getId()));
				// even (impair) number
				if ($nbRows%2 != 1){
					?><div class="clearfix"></div><?php
				}
			}
			$nbRows++;
			$row->setContentForm($fileName, $page->getRowPosition($row->getId()));
			// even (impair) number
			if ($nbRows%2 != 1){
				?><div class="clearfix"></div><?php
			}
			// Nouveau row après le bloc référent
			if ($refRow == $row->getId() and $rowPosition == 'after'){
				$nbRows++;
				$addRow = new Row('newRow', $fileName);
				$addRow->setContentForm($fileName, $page->getRowPosition($row->getId()) + 1);
				// even (impair) number
				if ($nbRows%2 != 1){
					?><div class="clearfix"></div><?php
				}
			}
		}
		\Template::addCSSToHeader('<link href="'.$settings->absoluteURL.'/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" rel="stylesheet">');
		\Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>');
		\Template::addJsToFooter('<script>$(\'textarea\').pagedownBootstrap();</script>');
		$page->toHTMLFooter();
	}

	public function processRequest(){
		if (!isset($_REQUEST['fileName']) and !isset($_REQUEST['page'])){
			new Alert('error','Erreur : la page n\'est pas renseignée.');
			return false;
		}
		var_dump($_REQUEST);
		$fileName = (isset($_REQUEST['fileName'])) ? $_REQUEST['fileName'] : $_REQUEST['page'] ;
		$position = (isset($_REQUEST['position'])) ? (int)$_REQUEST['position'] : null;
		$page = $this->addPageFromJSON($fileName);

		switch ($_REQUEST['request']){
			case 'savePage':
				if (isset($_REQUEST['cssFile'])){
					$page->setCssFile(\Sanitize::SanitizeForDb($_REQUEST['cssFile'], false));
				}
				break;
			case 'saveRow':
				if (!isset($_REQUEST['rowId'])){
					new Alert('error','Ajout de ligne : l\'ID de la ligne n\'est pas renseignée.');
					return false;
				}
				$rowId = \Sanitize::SanitizeForDb($_REQUEST['rowId'], false);
				$rowToSave = new Row($rowId, $fileName);
				if ($rowId == 'newRow' and (!isset($_REQUEST['row_'.$rowToSave->getId().'_newId']) or $_REQUEST['row_'.$rowToSave->getId().'_newId'] == 'newRow')){
					new Alert('error', 'Erreur : Vous devez indiquer une ID différente de <code>newRow</code>pour cette ligne');
					return false;
				}
				if (isset($_REQUEST['row_'.$rowToSave->getId().'_newId'])){
					$newRowId = str_replace(' ', '_', \Sanitize::SanitizeForDb($_REQUEST['row_'.$rowToSave->getId().'_newId'], false));
					$rowToSave->setId($newRowId);
				}
				$page->addRow($rowToSave, $rowId, $position);
				break;
			case 'delRow':
				if (!isset($_REQUEST['rowId'])){
					new Alert('error','Suppression de ligne : l\'ID de la ligne n\'est pas renseignée.');
					return false;
				}
				$rowId = \Sanitize::SanitizeForDb($_REQUEST['rowId'], false);
				$page->removeRow($rowId);
				break;
			case 'moveRow':
				if (!isset($_REQUEST['refRow'])){
					new Alert('error','Déplacement de ligne : l\'ID de la ligne n\'est pas renseignée.');
					return false;
				}
				$rowId = \Sanitize::SanitizeForDb($_REQUEST['refRow'], false);
				$rowMove = 'before';
				if (in_array($_REQUEST['moveRow'], array('before', 'after'))) $rowMove =  $_REQUEST['moveRow'];
				$page->moveRow($rowId, $rowMove);
				break;
			case 'saveBlock':
				if (!isset($_REQUEST['blockFullId'])){
					new Alert('error','Ajout de bloc : l\'ID du block n\'est pas renseignée.');
					return false;
				}
				list($rowId, $blockId) = explode('-', \Sanitize::SanitizeForDb($_REQUEST['blockFullId'], false));
				if (empty($rowId) or empty($blockId)) {
					new Alert('error','Ajout de bloc : l\'ID du block ou de la ligne n\'est pas renseignée correctement.');
					return false;
				}
				$blockToSave = new HTMLBlock($blockId, $rowId);
				if ($blockId == 'newBlock' and (!isset($_REQUEST['block_'.$blockToSave->getFullId().'_newId']) or $_REQUEST['block_'.$blockToSave->getFullId().'_newId'] == 'newBlock')){
					new Alert('error', 'Erreur : Vous devez indiquer une ID différente de <code>newBlock</code>pour ce block');
					return false;
				}
				if (isset($_REQUEST['block_'.$blockToSave->getFullId().'_titleLevel'])){
					if (isset($_REQUEST['block_'.$blockToSave->getFullId().'_title'])){
						$blockToSave->setTitle((int)$_REQUEST['block_'.$blockToSave->getFullId().'_titleLevel'], \Sanitize::SanitizeForDb($_REQUEST['block_'.$blockToSave->getFullId().'_title'], false));
					}else{
						$blockToSave->setTitleLevel((int)$_REQUEST['block_'.$blockToSave->getFullId().'_titleLevel']);
					}
				}
				if (isset($_REQUEST['block_'.$blockToSave->getFullId().'_tag'])){
					$blockToSave->setTag(\Sanitize::SanitizeForDb($_REQUEST['block_'.$blockToSave->getFullId().'_tag'], false));
				}
				foreach ($blockToSave->getWidths() as $width => $size){
					if (isset($_REQUEST['block_'.$blockToSave->getFullId().'_width_'.$width])){
						$blockToSave->setWidth($width, (int)$_REQUEST['block_'.$blockToSave->getFullId().'_width_'.$width]);
					}
				}
				if (isset($_REQUEST['block_'.$blockToSave->getFullId().'_content'])){
					$blockToSave->setContent(\Sanitize::SanitizeForDb($_REQUEST['block_'.$blockToSave->getFullId().'_content'], false));
				}
				if (isset($_REQUEST['block_'.$blockToSave->getFullId().'_newId'])){
					$blockId = str_replace(' ', '_', \Sanitize::SanitizeForDb($_REQUEST['block_'.$blockToSave->getFullId().'_newId'], false));
				}
				$page->getRows()[$rowId]->addBlock($blockToSave, $blockId, $position);
				break;
			case 'delBlock':
				if (!isset($_REQUEST['blockFullId'])){
					new Alert('error','Suppression de bloc : l\'ID du block n\'est pas renseigné.');
					return false;
				}
				list($rowId, $blockId) = explode('-', \Sanitize::SanitizeForDb($_REQUEST['blockFullId'], false));
				if (empty($rowId) or empty($blockId)) {
					new Alert('error','Suppression de bloc : l\'ID du block ou de la ligne n\'est pas renseignée correctement.');
					return false;
				}
				$page->getRows()[$rowId]->removeBlock($blockId);
				break;
			case 'moveBlock':
				if (!isset($_REQUEST['refBlock'])){
					new Alert('error','Déplacement de bloc : l\'ID du block n\'est pas renseigné.');
					return false;
				}
				list($rowId, $blockId) = explode('-', \Sanitize::SanitizeForDb($_REQUEST['refBlock'], false));
				if (empty($rowId) or empty($blockId)) {
					new Alert('error','Déplacement de bloc : l\'ID du block ou de la ligne n\'est pas renseignée correctement.');
					return false;
				}
				$blockMove = 'before';
				if (in_array($_REQUEST['moveBlock'], array('before', 'after'))) $blockMove =  $_REQUEST['moveBlock'];
				$page->getRows()[$rowId]->moveBlock($blockId, $blockMove);
				break;
		}
		$ret = $this->saveContent($page);
		if ($ret === true){
			new Alert('success', 'Page sauvegardée !');
		}else {
			new Alert('error', 'Page non sauvegardée !');
		}
		return $ret;
	}

	protected function populateBlockTypes(){
		global $settings;
		$fs = new Fs($settings->absolutePath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Content'.DIRECTORY_SEPARATOR.'Blocks');
		$blockClasses = $fs->getFilesInDir(null,'php', array('extension'), true);
		/** @var File $blockTypeRaw */
		foreach ($blockClasses as $blockTypeRaw){
			$this->blockTypes[] = str_replace('Block', '', $blockTypeRaw->name);
		}
	}

	/**
	 * @return array
	 */
	public function getBlockTypes() {
		return $this->blockTypes;
	}

	protected function populateCssFiles(){
		global $settings;
		$fs = new Fs($settings->absolutePath.DIRECTORY_SEPARATOR.'css');
		$cssFiles = $fs->getFilesInDir(null,'css', array('extension'), true);
		/** @var File $cssFile */
		foreach ($cssFiles as $cssFile){
			$this->cssFiles[] = $cssFile->baseName;
		}
	}

	/**
	 * @return array
	 */
	public function getCssFiles() {
		return $this->cssFiles;
	}
}