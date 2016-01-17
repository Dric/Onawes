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
use simple_html_dom_node;

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

	protected $CSSClasses = array();

	public function __contruct($fileName, $title = null){
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
	 * Return HTML content of page
	 */
	public function getHTMLContent(){
		$content = null;
		/** @var Row $row */
		foreach ($this->rows as $row){
			$content .= $row->getHTMLContent();
		}
		return $content;
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'CSSClasses'  => $this->CSSClasses,
		);
		foreach ($this->rows as $row){
			$array['rows'][] = $row->toArray();
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

	public function addRow(Row $row){
		$this->rows[] = $row;
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
	 * @return Row[]
	 */
	public function getRows() {
		return $this->rows;
	}
}