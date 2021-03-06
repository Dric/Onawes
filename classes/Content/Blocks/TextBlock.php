<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:39
 */

namespace Content\Blocks;


use Alerts\Alert;
use Content\Block;
use Michelf\MarkdownExtra;

class TextBlock extends Block{

	/**
	 * Type de bloc
	 * @var string
	 */
	protected $type = 'Text';

	/**
	 * Contenu HTML
	 * @var string
	 */
	protected $content = null;


	/**
	 * @param bool $rawContent MarkDown processed if false, raw content if true
	 *
	 * @return string
	 */
	public function getContent($rawContent = false) {
		if (!$rawContent){
			$content = \Sanitize::MarkdownToHTML($this->content);
		}else{
			$content = $this->content;
		}
		return $content;
	}


	public function toArray(){

		$array = parent::toArray();
		$array['properties']['content'] = $this->content;

		return $array;
	}

	public function getHTMLCustom(){
		echo $this->getContent();
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}


	/**
	 * Returns the fields sent by block editing form
	 * @return string[]
	 */
	public function getRequestFieldsToSave(){
		return array('content');
	}

	public function getExcerpt(){
		parent::getExcerpt();
		if ($this->content) { ?><p class="small">Contenu : <code><?php echo \Get::excerpt($this->content, 40); ?></code></p><?php }
	}

	public function getFormCustomFields(){
		?>
		<label for="block_<?php echo $this->getFullId(); ?>_content">Contenu</label>
		<textarea name="block_<?php echo $this->getFullId(); ?>_content" id="block_<?php echo $this->getFullId(); ?>_content" class="form-control" rows="8"><?php	echo $this->getContent(true);	?></textarea>
		<?php
	}
}