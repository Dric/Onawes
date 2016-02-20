<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:39
 */

namespace Content;


use Alerts\Alert;

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

	protected $theme = null;

	/**
	 * @var int[]
	 */
	protected $rowsOrder = array();

	protected $CSSClasses = array();

	/** @var Menu */
	protected $menu = null;

	public function __construct($fileName, $title = null){
		$this->fileName = $fileName;
		if (empty($title)) $this->title = $this->fileName;
		$this->menu = new Menu();
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


	public function addMenuItem(MenuItem $item){
		$this->menu->addItem($item);
	}

	/**
	 * Display HTML content of page
	 *
	 * @param Theme $theme
	 */
	public function toHTML($theme){
		$theme->toHTMLHeader($this->menu, $this->title);
		/** @var Row $row */
		foreach ($this->rows as $row){
			$row->toHTML();
		}
		$theme->toHTMLFooter();
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'CSSClasses'  => $this->CSSClasses,
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