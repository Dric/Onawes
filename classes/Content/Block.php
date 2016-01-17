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
use Michelf\MarkdownExtra;

class Block {

	protected $content = null;

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

	protected $widthsLabels = array(
		'xs'  => 'Smartphone',
		'sm'  => 'Tablette',
		'md'  => 'PC',
		'lg'  => 'Grand écran'
	);

	protected $levelTitlesLabels = array(
		1 => 'Plus grand titre',
		2 => 'Grand titre',
		3 => 'Titre moyen',
		4 => 'Petit titre',
		5 => 'Très petit titre',
		6 => 'Titre de moindre importance'
	);

	public function __construct($id, $CSSClasses = null, $tag = 'div'){
		$this->tag = (in_array($tag, $this->allowedTags)) ? $tag : 'div';
		$this->id = $id;
		if ($this->id == 'new'){
			$this->isUnsaved = true;
		} else {
			$this->title = $id;
			if (!empty($CSSClasses)) $this->$CSSClasses = explode(' ', $CSSClasses);
		}
	}

	/**
	 * @param bool $rawContent MarkDown processed if false, raw content if true
	 *
	 * @return string
	 */
	public function getContent($rawContent = false) {
		if (!$rawContent){
			//$content = MarkdownExtra::defaultTransform(htmlspecialchars_decode($this->content));
			$content = MarkdownExtra::defaultTransform($this->content);
			// Gestion des antislashes dans les balises code (les antislashes sont doublés dans ces cas-là par le système)
			$content = str_replace('\\\\', '\\', $content);
		}else{
			$content = $this->content;
		}
		return $content;
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'titleLevel'  => $this->titleLevel,
			'id'          => $this->id,
			'tag'         => $this->tag,
			'CSSClasses'  => $this->CSSClasses,
			'widths'      => $this->widths,
			'content'     => $this->content
		);
		return $array;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Display an edit form for the block
	 */
	public function setContentForm(){
		?>
		<div class="col-lg-6" id="block_<?php echo $this->id; ?>">
			<form class="well form-horizontal">
				<h4><?php if (!$this->isUnsaved) { ?>Edition du block <code id="block_<?php echo $this->id; ?>_title"><?php echo $this->getTitle(); ?></code><?php } else { ?>Ajouter un nouveau block<?php }?><button class="btn btn-xs tooltip-bottom" title="Modifier le titre" id="block_<?php echo $this->id; ?>_title_edit"><i class="fa fa-edit"></i></button></h4>
				<div class="form-group">
					<label class="col-sm-5 control-label" for="block_<?php echo $this->id; ?>_tag">Tag</label>
					<div class="col-sm-3">
						<select class="form-control" id="block_<?php echo $this->id; ?>_tag" name="block_<?php echo $this->id; ?>_tag">
							<?php
							foreach ($this->getAllowedTags() as $allowedTag){
								?><option <?php if ($allowedTag == $this->getTag()) echo 'selected'; ?>><?php echo $allowedTag; ?></option><?php
							}
							?>
						</select>
					</div>
				</div>
				Tailles :
				<?php
				foreach ($this->getWidths() as $width => $size){
					?>
					<div class="form-group form-group-sm">
						<label class="col-sm-5 control-label" for="block_<?php echo $this->id; ?>_width_<?php echo $width; ?>"><?php echo $this->widthsLabels[$width]; ?></label>
						<div class="col-sm-3">
							<select class="form-control" id="block_<?php echo $this->id; ?>_width_<?php echo $width; ?>" name="block_<?php echo $this->id; ?>_width_<?php echo $width; ?>">
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
					<label class="col-sm-5 control-label" for="block_<?php echo $this->id; ?>_titleLevel">Taille du titre</label>
					<div class="col-sm-6">
						<select class="form-control" id="block_<?php echo $this->id; ?>_titleLevel" name="block_<?php echo $this->id; ?>_titleLevel">
							<?php
							for ($i = 1; $i <= 6 ;$i++){
								?><option value="<?php echo $i; ?>" <?php if ($this->getTitleLevel() == $i) echo 'selected'; ?>><?php echo $this->levelTitlesLabels[$i]; ?> (H<?php echo $i; ?>)</option><?php
							}
							?>
						</select>
					</div>
				</div>
					<label for="block_<?php echo $this->id; ?>_content">Contenu</label>
					<textarea name="block_<?php echo $this->id; ?>_content" id="block_<?php echo $this->id; ?>_content" class="form-control" rows="8">
						<?php
						echo $this->getContent(true);
						?>
					</textarea>
			</form>
		</div>
		<?php
	}

	/**
	 * @return array
	 */
	public function getWidths($width = null) {
		if (empty($width)) return $this->widths;
		if (isset($this->widths[$width])) return $this->widths[$width];
		return null;
	}

	public function getCssWidth(){
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

}