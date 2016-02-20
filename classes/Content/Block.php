<?php
/**
 * Creator: Dric
 * Date: 22/01/2016
 * Time: 09:55
 */

namespace Content;

use Alerts\Alert;

class Block {

	/**
	 * Type de bloc
	 * @var string
	 */
	protected $type = null;
	/**
	 * @var string
	 */
	protected $tag = 'div';

	protected $allowedTags = array(
		'div',
		'section',
		'article'
	);
	/**
	 * @var string
	 */
	protected $id = null;

	protected $isUnsaved = false;
	/**
	 * @var string[]
	 */
	protected $CSSClasses = array();

	/**
	 * @var string
	 */
	protected $parentId = null;
	/**
	 * @var string
	 */
	protected $title = null;

	/**
	 * @var int Level Title (h1, h2, etc.)
	 */
	protected $titleLevel = 0;

	protected $widths = array(
		'xs'  => 0,
		'sm'  => 0,
		'md'  => 0,
		'lg'  => 0
	);

	public function __construct($id, $parentId, $CSSClasses = null, $tag = 'div'){
		$this->tag = (in_array($tag, $this->allowedTags)) ? $tag : 'div';
		$this->id = $id;
		$this->parentId = $parentId;
		if ($this->id == 'newBlock'){
			$this->isUnsaved = true;
		} else {
			$this->title = $id;
			if (!empty($CSSClasses)) $this->CSSClasses = explode(' ', $CSSClasses);
		}
	}

	/**
	 * @param string $width
	 *
	 * @return array
	 */
	public function getWidths($width = null) {
		if (empty($width)) return $this->widths;
		if (isset($this->widths[$width])) return $this->widths[$width];
		return null;
	}

	public function getHTMLCssWidth(){
		$ret = '';
		foreach ($this->widths as $width => $size){
			if ($size != 0){
				$ret .= 'col-'.$width.'-'.$size.' ';
			}
		}
		return $ret;
	}

	/**
	 * @return string
	 */
	public function getBlockId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getFullId() {
		return $this->parentId.'__'.$this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
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

	protected function getHTMLCSSClasses(){
		return $this->getCSSClasses(true);
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
	 * @param string $width Largeur (xs, sm, md ou lg)
	 * @param int $size Taille entre 1 et 12
	 *
	 */
	public function setWidth($width, $size) {
		if ($size > 12) {
			new Alert('debug', 'SetWidth : la taille du block est supérieure à 12 ! Taille ramenée à 12');
			$size = 12;
		}
		if (isset($this->widths[$width])) $this->widths[$width] = (int)$size;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	public function getHTMLTitle(){
		return '<h'.$this->titleLevel.'>'.$this->title.'</h'.$this->titleLevel.'>';
	}

	/**
	 * @param int     $level
	 * @param string  $title
	 */
	public function setTitle($level, $title) {
		$this->titleLevel = (int)$level;
		$this->title = $title;
	}

	/**
	 * @return int
	 */
	public function getTitleLevel() {
		return $this->titleLevel;
	}

	/**
	 * @param int $titleLevel
	 */
	public function setTitleLevel($titleLevel) {
		$this->titleLevel = $titleLevel;
	}

	/**
	 * @return array
	 */
	public function getAllowedTags() {
		return $this->allowedTags;
	}

	/**
	 * @return string
	 */
	public function getParentId() {
		return $this->parentId;
	}

	public function toArray(){
		$array = array(
			'type'        => $this->type,
			'title'       => $this->title,
			'titleLevel'  => $this->titleLevel,
			'id'          => $this->id,
			'tag'         => $this->tag,
			'CSSClasses'  => $this->CSSClasses,
			'widths'      => $this->widths,
			'properties'  => null
		);
		return $array;
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toHTML(){
		?>
		<<?php echo $this->getTag(); ?> class="<?php echo $this->getHTMLCssWidth(); ?><?php echo ' '.$this->getHTMLCSSClasses(); ?>" id="<?php echo $this->getFullId(); ?>">
      <?php echo $this->getHTMLTitle(); ?>
      <?php $this->getHTMLCustom(); ?>
		</<?php echo $this->getTag(); ?>>
		<?php
	}

	public function getHTMLCustom(){

	}

	/**
	 * Returns the fields sent by block editing form
	 * @return string[]
	 */
	public function getRequestFieldsToSave(){
		return array();
	}

	public function getExcerpt(){
		if (!$this->isUnsaved) {
			?>
			<!--<p>ID : <code><?php echo $this->getFullId(); ?></code></p>-->
			<div class="pull-right"><span class="label label-default"><?php echo $this->getType(); ?></span></div>
			<?php
		}
		?>
		<h4><?php if (!$this->isUnsaved) { ?>Bloc <code><?php echo $this->getTitle(); ?></code><?php } else { ?>Ajouter un nouveau block<?php }?></h4>
		<?php

	}

	public function getFormCustomFields(){
		// Insert here the fields used by the block type
	}



	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return boolean
	 */
	public function isUnsaved() {
		return $this->isUnsaved;
	}

}