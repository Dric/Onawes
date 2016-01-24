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
use Content\Themes\Edit;
use FileSystem\File;
use FileSystem\Fs;
use Template;

class ContentManager {

	protected $contentDir;
	/**
	 * @var Page
	 */
	protected $page = null;

	protected $blockTypes = array();

	protected $cssFiles = array();

	protected $themes = array();

	protected $url = null;

	protected $widthsLabels = array(
		'xs'  => 'Smartphone',
		'sm'  => 'Tablette',
		'md'  => 'PC',
		'lg'  => 'Grand écran'
	);

	protected $levelTitlesLabels = array(
		0 => '',
		1 => 'Plus grand titre',
		2 => 'Grand titre',
		3 => 'Titre moyen',
		4 => 'Petit titre',
		5 => 'Très petit titre',
		6 => 'Titre de moindre importance'
	);

	public function __construct(){
		global $settings;
		$this->contentDir = $settings->absolutePath.DIRECTORY_SEPARATOR.$settings->contentDir;
		$this->populateBlockTypes();
		$this->populateCssFiles();
		$this->populateThemes();
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
	 * Create Page object from json file
	 * @param string $fileName JSON file
	 *
	 * @return bool|Page
	 */
	public function addPageFromJSON($fileName){
		// reading file
		$fs = new Fs($this->contentDir);
		$fileContent = $fs->readFile($fileName, 'string');
		if (!$fileContent){
			new Alert('error', 'Erreur : la page <code>'.$fileName.'</code> est illisible ou introuvable !');
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
					$blockType = $this->getBlockPHPClass($jsonBlock['type']);
					/** @var Block $block */
					$block = new $blockType($jsonBlock['id'], $row->getId());
					$block->setTitle($jsonBlock['titleLevel'], $jsonBlock['title']);
					$block->setTag($jsonBlock['tag']);
					foreach ($jsonBlock['CSSClasses'] as $class) {
						$block->addCSSClass($class);
					}
					foreach ($jsonBlock['widths'] as $width => $size) {
						$block->setWidth($width, $size);
					}
					// Custom blocktypes properties
					if (!empty($jsonBlock['properties'])){
						foreach ($jsonBlock['properties'] as $property => $value){
							$block->{'set'.ucfirst($property)}($value);
						}
					}
					$row->addBlock($block);
				}
			}
			$page->addRow($row);
		}
		return $page;
	}

	/**
	 * Returns the class name of block type
	 *
	 * @param string $type Type of block
	 *
	 * @return string
	 */
	protected function getBlockPHPCLass($type){
		return '\\Content\\Blocks\\' . $type . 'Block';
	}

	public function listPages(){
		global $settings;
		$fs = new Fs($this->contentDir);
		$JSONPages = $fs->getFilesInDir(null,'json', array('extension'), true);
		/** @var File $JSONPage */
		foreach ($JSONPages as $JSONPage){

		}
		Edit::header();
		?>
		<h2>Liste des pages</h2><?php

		?>
		<div class="row">
			<div class="col-md-12">

			</div>
		</div>
		<?php
		Edit::footer();
	}

	/**
	 * @param Page $page
	 */
	public function editPage(Page $page){
		global $settings, $cssFiles, $themes;
		$rowPosition = null;
		$refRow = null;
		/*if (empty($page->getTheme()) or !in_array($page->getTheme(), $themes)){
			new Alert('error', 'Erreur : le thème de la page est introuvable ou n\'est pas défini ! Le thème par défaut sera utilisé !');
			$theme = 'Home';
		}else{
			$theme = $page->getTheme();
		}*/
		$fileName = $page->getFileName();
		if (isset($_REQUEST['addRow'])){
			if (isset($_REQUEST['refRow'])){
				if (in_array($_REQUEST['addRow'], array('before', 'after'))) $rowPosition =  $_REQUEST['addRow'];
				$refRow = \Sanitize::SanitizeForDb($_REQUEST['refRow'], false);
			}
		}
		//$themeClass = '\\Themes\\'.$theme;
		Edit::header();
		//$page->toHTMLHeader();
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
				$this->editRow($addRow, $fileName, $page->getRowPosition($row->getId()));
				// even (impair) number
				if ($nbRows%2 != 1){
					?><div class="clearfix"></div><?php
				}
			}
			$nbRows++;
			$this->editRow($row, $fileName, $page->getRowPosition($row->getId()));
			// even (impair) number
			if ($nbRows%2 != 1){
				?><div class="clearfix"></div><?php
			}
			// Nouveau row après le bloc référent
			if ($refRow == $row->getId() and $rowPosition == 'after'){
				$nbRows++;
				$addRow = new Row('newRow', $fileName);
				$this->editRow($addRow, $fileName, $page->getRowPosition($row->getId()) + 1);
				// even (impair) number
				if ($nbRows%2 != 1){
					?><div class="clearfix"></div><?php
				}
			}
		}
		\Template::addCSSToHeader('<link href="'.$settings->absoluteURL.'/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" rel="stylesheet">');
		\Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>');
		\Template::addJsToFooter('<script>$(\'textarea\').pagedownBootstrap();</script>');
		Edit::footer();
	}

	/**
	 * Display an edit form for the row
	 *
	 * @param Row    $row
	 * @param string $fileName File Name where is saved the row
	 * @param        $rowPosition
	 *
	 * @internal param int $position
	 */
	public function editRow(Row $row, $fileName, $rowPosition){
		global $settings;
		$blockPosition = null;
		$refBlock = null;
		if (isset($_REQUEST['addBlock'])){
			if (isset($_REQUEST['refBlock'])){
				if (in_array($_REQUEST['addBlock'], array('before', 'after'))) $blockPosition =  $_REQUEST['addBlock'];
				$refBlock = \Sanitize::SanitizeForDb($_REQUEST['refBlock'], false);
			}
		}
		?>
		<div class="row">
			<div class="col-md-12" id="row_<?php echo $row->getId(); ?>">
				<div class="panel panel-default">
					<div class="panel-body">
						<form class="well form-horizontal" action="<?php echo $settings->editURL; ?>#row_<?php echo $row->getId(); ?>">
							<h3><?php if (!$row->isUnsaved()) { ?>Ligne <code><?php echo $row->getTitle(); ?></code><?php } else { ?>Ajouter une nouvelle ligne<?php }?></h3>
							<div class="form-group">
								<label class="col-sm-5 control-label" for="row_<?php echo $row->getId(); ?>_newId">ID</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="row_<?php echo $row->getId(); ?>_newId" name="row_<?php echo $row->getId(); ?>_newId" value="<?php echo $row->getId(); ?>" required>
								</div>
							</div>
							<input type="hidden" name="fileName" value="<?php echo $fileName; ?>">
							<input type="hidden" name="rowId" value="<?php echo $row->getId(); ?>">
							<input type="hidden" name="position" value="<?php echo $rowPosition; ?>">
							<input type="hidden" name="edit">
							<button type="submit" class="btn btn-primary" name="request" value="saveRow">Enregistrer</button>
							<?php if ($row->getId() != 'newRow'){ ?>
								<button type="submit" name="request" value="delRow" class="btn btn-danger">Supprimer</button>
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Ajouter une ligne <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addRow=before&refRow=<?php echo $row->getId(); ?>#row_newRow">Avant cette ligne</a></li>
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addRow=after&refRow=<?php echo $row->getId(); ?>#row_newRow">Après cette ligne</a></li>
									</ul>
								</div>
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Déplacer <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveRow&moveRow=before&refRow=<?php echo $row->getId(); ?>#row_<?php echo $row->getId(); ?>">Vers le haut</a></li>
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveRow&moveRow=after&refRow=<?php echo $row->getId(); ?>#row_<?php echo $row->getId(); ?>">Vers le bas</a></li>
									</ul>
								</div>
							<?php } ?>
						</form>
						<div class="row">
							<?php
							$nbBlocks = 0;
							if (empty($row->getBlocks()) and !$row->isUnsaved()) {
								$addBlock = new Block('newBlock', $row->getId());
								$this->editBlock($addBlock, $fileName, 1);
							}elseif(!empty($row->getBlocks())){
								foreach ($row->getBlocks() as $index => $block){
									// Nouveau block avant le bloc référent
									if ($refBlock == $block->getFullId() and $blockPosition == 'before'){
										$nbBlocks++;
										$blocType = $this->getBlockPHPCLass($block->getType());
										$addBlock = new $blocType('newBlock', $row->getId());
										$this->editBlock($addBlock, $fileName, $row->getBlockPosition($block->getBlockId()));
										// even (impair) number
										if ($nbBlocks%2 != 1){
											?><div class="clearfix"></div><?php
										}
									}
									$nbBlocks++;
									$this->editBlock($block, $fileName, $row->getBlockPosition($block->getBlockId()));
									// even (impair) number
									if ($nbBlocks%2 != 1){
										?><div class="clearfix"></div><?php
									}
									// Nouveau block après le bloc référent
									if ($refBlock == $block->getFullId() and $blockPosition == 'after'){
										$nbBlocks++;
										$addBlock = new Block('newBlock', $row->getId());
										$this->editBlock($addBlock, $fileName, $row->getBlockPosition($block->getBlockId()) + 1);
										// even (impair) number
										if ($nbBlocks%2 != 1){
											?><div class="clearfix"></div><?php
										}
									}
								}
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display an edit form for the block
	 *
	 * @param string $fileName File Name where is saved the block
	 */
	public function editBlock(Block $block, $fileName, $position){
		global $settings, $blockTypes;
		?>
		<div class="col-lg-6" id="block_<?php echo $block->getFullId(); ?>">
			<form class="well <?php if ($block->IsUnsaved()) { ?>well-warning<?php } ?> form-horizontal" action="<?php echo $settings->editURL; ?>#block_<?php echo $block->getFullId(); ?>">
				<?php $block->getExcerpt(); ?>
				<?php
				if ($block->isUnsaved()){
					?>
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_newId">ID</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="block_<?php echo $block->getFullId(); ?>_newId" name="block_<?php echo $block->getFullId(); ?>_newId" value="<?php echo $block->getBlockId(); ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_type">Type de bloc</label>
						<div class="col-sm-5">
							<select class="form-control" id="block_<?php echo $block->getFullId(); ?>_type" name="block_<?php echo $block->getFullId(); ?>_type" required>
								<?php
								foreach ($blockTypes as $blockType){
									?><option <?php if ($blockType == $block->getType()) echo 'selected'; ?>><?php echo $blockType; ?></option><?php
								}
								?>
							</select>
						</div>
					</div>
					<?php
				}else{
				?>
				<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $block->getFullId(); ?>_editPanel" aria-expanded="false" aria-controls="CollapseEditPanel">
					Modifier
				</button>
				<div class="collapse" id="<?php echo $block->getFullId(); ?>_editPanel">
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_title">Titre</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="block_<?php echo $block->getFullId(); ?>_tag" name="block_<?php echo $block->getFullId(); ?>_title" value="<?php echo $block->getTitle(); ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_newId">ID</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="block_<?php echo $block->getFullId(); ?>_newId" name="block_<?php echo $block->getFullId(); ?>_newId" value="<?php echo $block->getBlockId(); ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_tag">Tag</label>
						<div class="col-sm-3">
							<select class="form-control" id="block_<?php echo $block->getFullId(); ?>_tag" name="block_<?php echo $block->getFullId(); ?>_tag" required>
								<?php
								foreach ($block->getAllowedTags() as $allowedTag){
									?><option <?php if ($allowedTag == $block->getTag()) echo 'selected'; ?>><?php echo $allowedTag; ?></option><?php
								}
								?>
							</select>
						</div>
					</div>
					Tailles :
					<?php
					foreach ($block->getWidths() as $width => $size){
						?>
						<div class="form-group form-group-sm">
							<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_width_<?php echo $width; ?>"><?php echo $this->widthsLabels[$width]; ?></label>
							<div class="col-sm-3">
								<select class="form-control" id="block_<?php echo $block->getFullId(); ?>_width_<?php echo $width; ?>" name="block_<?php echo $block->getFullId(); ?>_width_<?php echo $width; ?>">
									<?php
									for ($i = 0; $i <= 12 ;$i++){
										?><option <?php if ($size == $i) echo 'selected'; ?>><?php echo $i; ?></option><?php
									}
									?>
								</select>
							</div>
						</div>
						<?php
					}
					?>
					<div class="form-group form-group-sm">
						<label class="col-sm-5 control-label" for="block_<?php echo $block->getFullId(); ?>_titleLevel">Taille du titre</label>
						<div class="col-sm-6">
							<select class="form-control" id="block_<?php echo $block->getFullId(); ?>_titleLevel" name="block_<?php echo $block->getFullId(); ?>_titleLevel">
								<?php
								for ($i = 0; $i <= 6 ;$i++){
									?><option value="<?php echo $i; ?>" <?php if ($block->getTitleLevel() == $i) echo 'selected'; ?>><?php echo $this->levelTitlesLabels[$i]; ?> <?php if ($i > 0) { ?>(H<?php echo $i; ?>) <?php }else{ ?>Pas de titre<?php } ?></option><?php
								}
								?>
							</select>
						</div>
					</div>
					<?php $block->getFormCustomFields(); ?>
					<?php } ?>
					<input type="hidden" name="fileName" value="<?php echo $fileName; ?>">
					<input type="hidden" name="blockFullId" value="<?php echo $block->getFullId(); ?>">
					<input type="hidden" name="position" value="<?php echo $position; ?>">
					<input type="hidden" name="edit">
					<button type="submit" class="btn btn-primary" name="request" value="saveBlock">Enregistrer</button>
					<?php if (!$block->isUnsaved()){ ?>
						<button type="submit" name="request" value="delBlock" class="btn btn-danger">Supprimer</button>
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Ajouter un bloc <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addBlock=before&refBlock=<?php echo $block->getFullId(); ?>#block_<?php echo $block->getParentId(); ?>-newBlock">Avant ce bloc</a></li>
								<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addBlock=after&refBlock=<?php echo $block->getFullId(); ?>#block_<?php echo $block->getParentId(); ?>-newBlock">Après ce bloc</a></li>
							</ul>
						</div>
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Déplacer <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveBlock&moveBlock=before&refBlock=<?php echo $block->getFullId(); ?>#block_<?php echo $block->getFullId(); ?>">Vers le haut</a></li>
								<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveBlock&moveBlock=after&refBlock=<?php echo $block->getFullId(); ?>#block_<?php echo $block->getFullId(); ?>">Vers le bas</a></li>
							</ul>
						</div>
					<?php } ?>
				</div>
			</form>
		</div>
		<?php
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

	protected function populateThemes(){
		global $settings;
		$fs = new Fs($settings->absolutePath.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Content'.DIRECTORY_SEPARATOR.'Themes');
		$this->themes[] = $fs->getSubDirsIndDir();
	}

	/**
	 * @return array
	 */
	public function getCssFiles() {
		return $this->cssFiles;
	}

	/**
	 * @return array
	 */
	public function getThemes() {
		return $this->themes;
	}

}