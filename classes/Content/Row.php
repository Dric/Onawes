<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:39
 */

namespace Content;


use Alerts\Alert;
use Content\Blocks\HTMLBlock;

class Row {

	/**
	 * @var HTMLBlock[]
	 */
	protected $blocks = array();

	/**
	 * @var bool
	 */
	protected $isUnsaved = false;

	/**
	 * @var string
	 */
	protected $parentId = null;

	/**
	 * @var int[]
	 */
	protected $blocksOrder = array();

	/**
	 * @var string
	 */
	protected $tag = 'div';

	protected $allowedTags = array(
		'div',
		'section'
	);
	/**
	 * @var string
	 */
	protected $id = null;
	/**
	 * @var string[]
	 */
	protected $CSSClasses = array();

	/**
	 * @var string
	 */
	protected $title = null;

	public function __construct($id, $parentId, $CSSClasses = null, $tag = 'div'){
		$this->tag = (in_array($tag, $this->allowedTags)) ? $tag : 'div';
		$this->id = $id;
		$this->parentId = $parentId;
		if ($this->id == 'newBlock'){
			$this->isUnsaved = true;
		}
		$this->title = $id;
		if (!empty($CSSClasses)) $this->$CSSClasses = explode(' ', $CSSClasses);
	}
	/**
	 * Return content as HTML string
	 * @return string
	 */
	public function toHTML(){
		/** @var Block $block */
		?><div class="<?php echo $this->getCSSClasses(true).' '; ?>row" id="<?php echo $this->id; ?>"><?php
		foreach ($this->blocks as $block){
			$block->toHTML();
		}
		?></div><?php
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'id'          => $this->id,
			'tag'         => $this->tag,
			'CSSClasses'  => $this->CSSClasses,
		);
		foreach ($this->blocksOrder as $blockId => $position){
			$array['blocks'][$blockId] = $this->blocks[$blockId]->toArray();
		}
		return $array;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
		$this->title = $id;
	}

	/**
	 *
	 * @param bool $toString If true, return string of CSS classes separated by spaces
	 *
	 * @return string|\string[]
	 */
	public function getCSSClasses($toString = false) {
		return ($toString) ? implode(' ', $this->CSSClasses) : $this->CSSClasses;
	}

	/**
	 * @param $CSSClass CSS class to add - ignored if already present
	 *
	 */
	public function addCSSClass($CSSClass) {
		if (!in_array($CSSClass, $this->CSSClasses)) $this->CSSClasses[] = $CSSClass;
	}

	/**
	 * @return string
	 */
	public function getTag() {
		return $this->tag;
	}

	/**
	 * @param string $tag
	 */
	public function setTag($tag) {
		$this->tag = (in_array($tag, $this->allowedTags)) ? $tag : 'div';
	}

	/**
	 * @return HTMLBlock[]
	 */
	public function getBlocks() {
		$ret = array();
		// `$this->blocksOrder` is always sorted by position when adding a blocks
		foreach ($this->blocksOrder as $blocksId => $position){
			$ret[$blocksId] = $this->blocks[$blocksId];
		}
		return $ret;
	}

	/**
	 * @param HTMLBlock $block
	 * @param string    $replaceBlockId HTMLBlock Id to replace in case of a block renaming
	 * @param int       $position
	 *
	 * @return bool
	 */
	public function addBlock(HTMLBlock $block, $replaceBlockId = null, $position = null) {
		if ($block->getBlockId() == 'newBlock'){
			if (is_null($position)){
				new Alert('error', 'Erreur : Ajout de nouveau block - la position n\'est pas renseignée !');
				return false;
			}
			foreach ($this->blocksOrder as $blockId => $pos){
				if ($pos >= $position){
					$this->blocksOrder[$blockId] = $pos + 1;
				}
			}
			$block->setId($replaceBlockId);
			$this->blocks[$block->getBlockId()] = $block;
			$this->blocksOrder[$block->getBlockId()] = $position;
		}elseif (!(empty($replaceBlockId))){
			$newPosition = (is_null($position)) ? $this->blocksOrder[$replaceBlockId] : $position;
			unset($this->blocks[$replaceBlockId]);
			unset($this->blocksOrder[$replaceBlockId]);
			if (!is_null($position)){
				if (in_array($position, $this->blocksOrder)){
					foreach ($this->blocksOrder as $blockId => $pos){
						if ($pos >= $position){
							$this->blocksOrder[$blockId] = $pos + 1;
						}
					}
				}
			}
			$this->blocks[$block->getBlockId()] = $block;
			$this->blocksOrder[$block->getBlockId()] = $newPosition;
		}else{
			$this->blocks[$block->getBlockId()] = $block;
			$this->blocksOrder[$block->getBlockId()] = (is_null($position)) ? array_search($block->getBlockId(), array_keys($this->blocks))+1 : $position;
			/*
			 * @from <http://stackoverflow.com/a/3145647>
			 * Checking if duplicates values exist
			 */
			if (in_array($position, $this->blocksOrder) and count($this->blocksOrder) !== count(array_unique($this->blocksOrder))){
				foreach ($this->blocksOrder as $blockId => $pos){
					if ($pos >= $position and $blockId != $block->getBlockId()){
						$this->blocksOrder[$blockId] = $pos + 1;
					}
				}
			}

		}
		asort($this->blocksOrder);
		return true;
	}

	public function removeBlock($blockId){
		unset($this->blocks[$blockId]);
		$position = $this->blocksOrder[$blockId];
		unset($this->blocksOrder[$blockId]);
		foreach ($this->blocksOrder as $blockOrderID => $pos){
			if ($pos > $position){
				$this->blocksOrder[$blockOrderID] = $pos - 1;
			}
		}
		asort($this->blocksOrder);
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

	public function getBlockPosition($blockId){
		return (isset($this->blocksOrder[$blockId])) ? $this->blocksOrder[$blockId] : 0;
	}

	/**
	 * Display an edit form for the row
	 *
	 * @param string $fileName File Name where is saved the row
	 * @param int $position
	 */
	public function setContentForm($fileName, $rowPosition){
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
			<div class="col-md-12" id="row_<?php echo $this->getId(); ?>">
				<div class="panel panel-default">
					<div class="panel-body">
						<form class="well form-horizontal" action="<?php echo $settings->editURL; ?>#row_<?php echo $this->getId(); ?>">
							<h3><?php if (!$this->isUnsaved) { ?>Ligne <code><?php echo $this->getTitle(); ?></code><?php } else { ?>Ajouter une nouvelle ligne<?php }?></h3>
							<div class="form-group">
								<label class="col-sm-5 control-label" for="row_<?php echo $this->getId(); ?>_newId">ID</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="row_<?php echo $this->getId(); ?>_newId" name="row_<?php echo $this->getId(); ?>_newId" value="<?php echo $this->getId(); ?>" required>
								</div>
							</div>
							<input type="hidden" name="fileName" value="<?php echo $fileName; ?>">
							<input type="hidden" name="rowId" value="<?php echo $this->getId(); ?>">
							<input type="hidden" name="position" value="<?php echo $rowPosition; ?>">
							<input type="hidden" name="edit">
							<button type="submit" class="btn btn-primary" name="request" value="saveRow">Enregistrer</button>
							<?php if ($this->id != 'newRow'){ ?>
								<button type="submit" name="request" value="delRow" class="btn btn-danger">Supprimer</button>
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Ajouter une ligne <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addRow=before&refRow=<?php echo $this->getId(); ?>#row_newRow">Avant cette ligne</a></li>
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addRow=after&refRow=<?php echo $this->getId(); ?>#row_newRow">Après cette ligne</a></li>
									</ul>
								</div>
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Déplacer <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveRow&moveRow=before&refRow=<?php echo $this->getId(); ?>#row_<?php echo $this->getId(); ?>">Vers le haut</a></li>
										<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveRow&moveRow=after&refRow=<?php echo $this->getId(); ?>#row_<?php echo $this->getId(); ?>">Vers le bas</a></li>
									</ul>
								</div>
							<?php } ?>
						</form>
						<div class="row">
							<?php
							$nbBlocks = 0;
							if (empty($this->blocks) and !$this->isUnsaved) {
								$addBlock = new HTMLBlock('newBlock', $this->getId());
								$addBlock->setContentForm($fileName, 1);
							}elseif(!empty($this->blocks)){
								foreach ($this->getBlocks() as $index => $block){
									// Nouveau block avant le bloc référent
									if ($refBlock == $block->getFullId() and $blockPosition == 'before'){
										$nbBlocks++;
										$addBlock = new HTMLBlock('newBlock', $this->getId());
										$addBlock->setContentForm($fileName, $this->getBlockPosition($block->getBlockId()));
										// even (impair) number
										if ($nbBlocks%2 != 1){
											?><div class="clearfix"></div><?php
										}
									}
									$nbBlocks++;
									$block->setContentForm($fileName, $this->getBlockPosition($block->getBlockId()));
									// even (impair) number
									if ($nbBlocks%2 != 1){
										?><div class="clearfix"></div><?php
									}
									// Nouveau block après le bloc référent
									if ($refBlock == $block->getFullId() and $blockPosition == 'after'){
										$nbBlocks++;
										$addBlock = new HTMLBlock('newBlock', $this->getId());
										$addBlock->setContentForm($fileName, $this->getBlockPosition($block->getBlockId()) + 1);
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
	 * Move a block
	 *
	 * @param string $blockId HTMLBlock ID
	 * @param string $moveDirection Possible values : `before` or `after`
	 *
	 * @return bool
	 */
	public function moveBlock($blockId, $moveDirection){
		$oldPosition = $this->blocksOrder[$blockId];
		if ($moveDirection == 'before'){
			$position = $oldPosition - 1;
		}elseif($moveDirection == 'after'){
			$position = $oldPosition + 1;
		}else{
			new Alert('error', 'Erreur : Impossible de déplacer le bloc car la direction de déplacement est incorrecte (<code>'.$moveDirection.'</code>)!');
			return false;
		}
		$blockToSwitch = array_search($position, $this->blocksOrder);
		if (!$blockToSwitch){
			new Alert('error', 'Erreur : Impossible de déplacer le bloc.');
			return false;
		}
		$this->blocksOrder[$blockId] = $position;
		$this->blocksOrder[$blockToSwitch] = $oldPosition;
		asort($this->blocksOrder);
		return true;
	}

	/**
	 * @return string
	 */
	public function getParentId() {
		return $this->parentId;
	}
}