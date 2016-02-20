<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:39
 */

namespace Content;


use Alerts\Alert;
use Content\Blocks\TextBlock;

class Row {

	/**
	 * @var TextBlock[]
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
	/** @var bool  */
	protected $isMenuItem = false;

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
		if (!empty($CSSClasses)) $this->CSSClasses = explode(' ', $CSSClasses);
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
			'isMenuItem'  => $this->isMenuItem
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
		if (empty($this->title)) $this->title = $id;
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
	 * @param string $CSSClass CSS class to add - ignored if already present
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
	 * @return TextBlock[]
	 */
	public function getBlocks() {
		$ret = array();
		// `$this->blocksOrder` is always sorted by position when adding a blocks
		foreach ($this->blocksOrder as $blocksId => $position){
			$ret[$blocksId] = $this->blocks[$blocksId];
		}
		return $ret;
	}

	/*
	 * @param Block     $block
	 * @param string    $replaceBlockId Block Id to replace in case of a block renaming
	 * @param int       $position
	 *
	 * @return bool
	 */
	public function addBlock(Block $block, $replaceBlockId = null, $position = null) {
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

	/**
	 * @return boolean
	 */
	public function isUnsaved() {
		return $this->isUnsaved;
	}

	/**
	 * @return boolean
	 */
	public function isIsMenuItem() {
		return $this->isMenuItem;
	}

	/**
	 * @param boolean $isMenuItem
	 */
	public function setIsMenuItem($isMenuItem) {
		$this->isMenuItem = $isMenuItem;
	}
}