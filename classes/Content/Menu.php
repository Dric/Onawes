<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 23/01/2016
 * Time: 18:59
 */

namespace Content;

use Alerts\Alert;

class Menu {
	protected $id = null;

	/**
	 * @var MenuItem[]
	 */
	protected $items = array();

	/**
	 * @var int[]
	 */
	protected $itemsOrder = array();

	/**
	 * @var string[]
	 */
	protected $CSSClasses = array();

	/**
	 * Return content as HTML string
	 * @return string
	 */
	public function toHTML(){
		$CSSClasses = $this->getCSSClasses(true);
		?>
		<ul <?php if (!empty($this->id)) echo 'id="'.$this->id.'"'; ?> <?php if (!empty($CSSClasses)) echo 'class="'.$CSSClasses.'"'; ?>>
			<?php
			foreach ($this->itemsOrder as $itemId => $position){
				$this->items[$itemId]->toHTML();
			}
			?>
		</ul>
		<?php
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'id'          => $this->id,
			'CSSClasses'  => $this->CSSClasses,
		);
		foreach ($this->itemsOrder as $itemId => $position){
			$array['items'][$itemId] = $this->items[$itemId]->toArray();
		}
		return $array;
	}

	/**
	 * @return MenuItem[]
	 */
	public function getItems() {
		$ret = array();
		// `$this->itemsOrder` is always sorted by position when adding a menu item
		foreach ($this->itemsOrder as $itemId => $position){
			$ret[$itemId] = $this->items[$itemId];
		}
		return $ret;
	}

	public function addItem(MenuItem $item, $replaceItemId = null, $position = null) {
		if ($item->getId() == 'newItem'){
			if (is_null($position)){
				new Alert('error', 'Erreur : Ajout de nouvel item de menu - la position n\'est pas renseignée !');
				return false;
			}
			foreach ($this->itemsOrder as $itemId => $pos){
				if ($pos >= $position){
					$this->itemsOrder[$itemId] = $pos + 1;
				}
			}
			$item->setId($replaceItemId);
			$this->items[$item->getId()] = $item;
			$this->itemsOrder[$item->getId()] = $position;
		}elseif (!(empty($replaceItemId))){
			$newPosition = (is_null($position)) ? $this->itemsOrder[$replaceItemId] : $position;
			unset($this->items[$replaceItemId]);
			unset($this->itemsOrder[$replaceItemId]);
			if (!is_null($position)){
				if (in_array($position, $this->itemsOrder)){
					foreach ($this->itemsOrder as $itemId => $pos){
						if ($pos >= $position){
							$this->itemsOrder[$itemId] = $pos + 1;
						}
					}
				}
			}
			$this->items[$item->getId()] = $item;
			$this->itemsOrder[$item->getId()] = $newPosition;
		}else{
			$this->items[$item->getId()] = $item;
			$this->itemsOrder[$item->getId()] = (is_null($position)) ? array_search($item->getId(), array_keys($this->items))+1 : $position;
			/*
			 * @from <http://stackoverflow.com/a/3145647>
			 * Checking if duplicates values exist
			 */
			if (in_array($position, $this->itemsOrder) and count($this->itemsOrder) !== count(array_unique($this->itemsOrder))){
				foreach ($this->itemsOrder as $itemId => $pos){
					if ($pos >= $position and $itemId != $item->getId()){
						$this->itemsOrder[$itemId] = $pos + 1;
					}
				}
			}

		}
		asort($this->itemsOrder);
		return true;
	}

	/**
	 * Remove a menu item
	 *
	 * @param string $itemId
	 *
	 * @return bool
	 */
	public function removeItem($itemId){
		unset($this->items[$itemId]);
		$position = $this->itemsOrder[$itemId];
		unset($this->itemsOrder[$itemId]);
		foreach ($this->itemsOrder as $itemOrderID => $pos){
			if ($pos > $position){
				$this->itemsOrder[$itemOrderID] = $pos - 1;
			}
		}
		asort($this->itemsOrder);
		return true;
	}

	public function getItemPosition($itemId){
		return (isset($this->itemsOrder[$itemId])) ? $this->itemsOrder[$itemId] : 0;
	}

	/**
	 * Move a item
	 *
	 * @param string $itemId MenuItem ID
	 * @param string $moveDirection Possible values : `before` or `after`
	 *
	 * @return bool
	 */
	public function moveItem($itemId, $moveDirection){
		$oldPosition = $this->itemsOrder[$itemId];
		if ($moveDirection == 'before'){
			$position = $oldPosition - 1;
		}elseif($moveDirection == 'after'){
			$position = $oldPosition + 1;
		}else{
			new Alert('error', 'Erreur : Impossible de déplacer le bloc car la direction de déplacement est incorrecte (<code>'.$moveDirection.'</code>)!');
			return false;
		}
		$itemToSwitch = array_search($position, $this->itemsOrder);
		if (!$itemToSwitch){
			new Alert('error', 'Erreur : Impossible de déplacer le bloc.');
			return false;
		}
		$this->itemsOrder[$itemId] = $position;
		$this->itemsOrder[$itemToSwitch] = $oldPosition;
		asort($this->itemsOrder);
		return true;
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
	 * @param string $CSSClass CSS class to add - ignored if already present
	 *
	 */
	public function addCSSClass($CSSClass) {
		if (!in_array($CSSClass, $this->CSSClasses)) $this->CSSClasses[] = $CSSClass;
	}
}