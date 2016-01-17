<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:39
 */

namespace Content;


use DOMDocument;

class Row {

	/**
	 * @var Block[]
	 */
	protected $blocks = array();

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

	public function __construct($id, $CSSClasses = null, $tag = 'div'){
		$this->tag = (in_array($tag, $this->allowedTags)) ? $tag : 'div';
		$this->id = $id;
		$this->title = $id;
		if (!empty($CSSClasses)) $this->$CSSClasses = explode(' ', $CSSClasses);
	}
	/**
	 * Return content as HTML string
	 * @return string
	 */
	public function getHTMLContent(){
		$content = null;
		/** @var Block $block */
		foreach ($this->blocks as $block){
			$content .= $block->getContent();
		}
		return $content;
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
		foreach ($this->blocks as $block){
			$array['blocks'][] = $block->toArray();
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
	 * @return Block[]
	 */
	public function getBlocks() {
		return $this->blocks;
	}

	/**
	 * @param Block $block
	 */
	public function addBlock(Block $block) {
		$this->blocks[] = $block;
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

}