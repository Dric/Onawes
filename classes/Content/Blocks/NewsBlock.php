<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 15/02/2016
 * Time: 14:57
 */

namespace Content\Blocks;

use Content\Block;
use Content\NewsItem;
use FileSystem\File;
use FileSystem\Fs;

class NewsBlock extends Block{
	/**
	 * Type de bloc
	 * @var string
	 */
	protected $type = 'News';

	/**
	 * Number of news items to display
	 * 0 = all items
	 * @var int
	 */
	protected $itemsToDisplay = 0;

	protected $allowedCats = array();

	public function toArray(){

		$array = parent::toArray();
		$array['properties']['itemsToDisplay'] = $this->itemsToDisplay;
		$array['properties']['allowedCats'] = $this->allowedCats;

		return $array;
	}

	/**
	 * Returns the fields sent by block editing form
	 * @return string[]
	 */
	public function getRequestFieldsToSave(){
		return array('itemsToDisplay', 'allowedCats');
	}

	public function getFormCustomFields(){
		?>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_itemsToDisplay">Nombre d'articles à afficher (<code>0</code> pour tout afficher)</label>
			<div class="col-sm-5">
				<input type="number" class="form-control" id="block_<?php echo $this->getFullId(); ?>_itemsToDisplay" name="block_<?php echo $this->getFullId(); ?>_itemsToDisplay" value="<?php echo $this->itemsToDisplay; ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_allowedCats">Catégories autorisées (séparées par des virgules)</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_allowedCats" name="block_<?php echo $this->getFullId(); ?>_allowedCats" value="<?php echo $this->getAllowedCats(true); ?>">
			</div>
		</div>
		<?php
	}

	public function getHTMLCustom(){
		echo $this->getNews();
	}

	protected function getNews(){
		global $Content;
		$fs = new Fs($Content->getContentDir().DIRECTORY_SEPARATOR.'News');
		$newsFiles = array();
		$allowedCats = (empty($this->allowedCats)) ? $Content->getNewsCategories() : $this->allowedCats ;
		foreach ($allowedCats as $cat){
			$files = $fs->getFilesInDir(\Sanitize::sanitizeFilename($cat), 'json', array('extension', 'dateCreated', 'parentFolder'), true);
			if (!empty($files))	$newsFiles = array_merge($files, $newsFiles);
		}

		if (!empty($newsFiles)) {
			$newsFiles = \Sanitize::sortObjectList($newsFiles, array('dateCreated'), 'DESC');
			$i = 0;
			while ($i < $this->itemsToDisplay and isset($newsFiles[$i])){
				$file = $newsFiles[$i];
				$newsItem = new NewsItem(trim(str_replace($Content->getContentDir().DIRECTORY_SEPARATOR.'News', '', $file->parentFolder), DIRECTORY_SEPARATOR), $file->baseName);
				$newsItem->display();
				$i++;
			}
		}else{
			echo '<p>Pas d\'actualités à afficher...</p>';
		}
	}


	/**
	 * @return int
	 */
	public function getItemsToDisplay() {
		return $this->itemsToDisplay;
	}

	/**
	 * @param int $itemsToDisplay
	 */
	public function setItemsToDisplay($itemsToDisplay) {
		$this->itemsToDisplay = $itemsToDisplay;
	}

	/**
	 * Returns allowed categories
	 *
	 * @param bool $returnString If true return cats in a string, comma separated.
	 *
	 * @return array
	 */
	public function getAllowedCats($returnString = false) {
		return ($returnString) ? implode(',', $this->allowedCats) : $this->allowedCats;
	}

	/**
	 *
	 * @param string[]|string $catsToAdd
	 *
	 */
	public function setAllowedCats($catsToAdd) {
		if (!empty($catsToAdd)) {
			if (!is_array($catsToAdd)) {
				// Avec array_map('trim', $array) on supprime les espaces des valeurs
				$this->allowedCats = array_map('trim', explode(',', $catsToAdd));
			} else {
				$this->allowedCats = $catsToAdd;
			}
		}
	}

}