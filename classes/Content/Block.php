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

	protected $widthsLabels = array(
		'xs'  => 'Smartphone',
		'sm'  => 'Tablette',
		'md'  => 'PC',
		'lg'  => 'Grand écran'
	);

	protected $levelTitlesLabels = array(
		0 => '',
		1 => 'Plus grand titre',
		2 => 'Grand titre',
		3 => 'Titre moyen',
		4 => 'Petit titre',
		5 => 'Très petit titre',
		6 => 'Titre de moindre importance'
	);

	public function __construct($id, $parentId, $CSSClasses = null, $tag = 'div'){
		$this->tag = (in_array($tag, $this->allowedTags)) ? $tag : 'div';
		$this->id = $id;
		$this->parentId = $parentId;
		if ($this->id == 'newBlock'){
			$this->isUnsaved = true;
		} else {
			$this->title = $id;
			if (!empty($CSSClasses)) $this->$CSSClasses = explode(' ', $CSSClasses);
		}
	}

	/**
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
		return $this->parentId.'-'.$this->id;
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

	/**
	 * @return string
	 */
	public function getParentId() {
		return $this->parentId;
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'titleLevel'  => $this->titleLevel,
			'id'          => $this->id,
			'tag'         => $this->tag,
			'CSSClasses'  => $this->CSSClasses,
			'widths'      => $this->widths,
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

	protected function getHTMLCustom(){

	}

	/**
	 * Returns the fields sent by block editing form
	 * @return string[]
	 */
	public function getRequestFieldsToSave(){
		return array();
	}

	protected function getExcerpt(){
		?>
		<h4><?php if (!$this->isUnsaved) { ?>Block <code><?php echo $this->getTitle(); ?></code><?php } else { ?>Ajouter un nouveau block<?php }?></h4>
		<?php
		if (!$this->isUnsaved) { ?>
			<p>ID : <code><?php echo $this->getFullId(); ?></code></p>
			<p>Type : <code><?php echo $this->getType(); ?></code></p>
			<?php
		}
	}

	protected function getFormCustomFields(){
		// Insert here the fields used by the block type
	}

	/**
	 * Display an edit form for the block
	 *
	 * @param string $fileName File Name where is saved the block
	 */
	public function setContentForm($fileName, $position){
		global $settings, $blockTypes;
		?>
		<div class="col-lg-6" id="block_<?php echo $this->getFullId(); ?>">
			<form class="well <?php if ($this->isUnsaved) { ?>well-warning<?php } ?> form-horizontal" action="<?php echo $settings->editURL; ?>#block_<?php echo $this->getFullId(); ?>">
				<?php $this->getExcerpt(); ?>
				<?php
				if ($this->isUnsaved){
					?>
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_newId">ID</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_newId" name="block_<?php echo $this->getFullId(); ?>_newId" value="<?php echo $this->getBlockId(); ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_type">Type de bloc</label>
						<div class="col-sm-5">
							<select class="form-control" id="block_<?php echo $this->getFullId(); ?>_type" name="block_<?php echo $this->getFullId(); ?>_type" required>
								<?php
								foreach ($blockTypes as $blockType){
									?><option <?php if ($blockType == $this->getType()) echo 'selected'; ?>><?php echo $blockType; ?></option><?php
								}
								?>
							</select>
						</div>
					</div>
					<?php
				}else{
					?>
					<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#<?php echo $this->getFullId(); ?>_editPanel" aria-expanded="false" aria-controls="CollapseEditPanel">
						Modifier
					</button>
					<div class="collapse" id="<?php echo $this->getFullId(); ?>_editPanel">
						<div class="form-group">
							<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_title">Titre</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_tag" name="block_<?php echo $this->getFullId(); ?>_title" value="<?php echo $this->getTitle(); ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_newId">ID</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_newId" name="block_<?php echo $this->getFullId(); ?>_newId" value="<?php echo $this->getBlockId(); ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_tag">Tag</label>
							<div class="col-sm-3">
								<select class="form-control" id="block_<?php echo $this->getFullId(); ?>_tag" name="block_<?php echo $this->getFullId(); ?>_tag" required>
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
								<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_width_<?php echo $width; ?>"><?php echo $this->widthsLabels[$width]; ?></label>
								<div class="col-sm-3">
									<select class="form-control" id="block_<?php echo $this->getFullId(); ?>_width_<?php echo $width; ?>" name="block_<?php echo $this->getFullId(); ?>_width_<?php echo $width; ?>">
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
							<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_titleLevel">Taille du titre</label>
							<div class="col-sm-6">
								<select class="form-control" id="block_<?php echo $this->getFullId(); ?>_titleLevel" name="block_<?php echo $this->getFullId(); ?>_titleLevel">
									<?php
									for ($i = 0; $i <= 6 ;$i++){
										?><option value="<?php echo $i; ?>" <?php if ($this->getTitleLevel() == $i) echo 'selected'; ?>><?php echo $this->levelTitlesLabels[$i]; ?> <?php if ($i > 0) { ?>(H<?php echo $i; ?>) <?php }else{ ?>Pas de titre<?php } ?></option><?php
									}
									?>
								</select>
							</div>
						</div>
						<?php $this->getFormCustomFields(); ?>
						<?php } ?>
						<input type="hidden" name="fileName" value="<?php echo $fileName; ?>">
						<input type="hidden" name="blockFullId" value="<?php echo $this->getFullId(); ?>">
						<input type="hidden" name="position" value="<?php echo $position; ?>">
						<input type="hidden" name="edit">
						<button type="submit" class="btn btn-primary" name="request" value="saveBlock">Enregistrer</button>
						<?php if (!$this->isUnsaved){ ?>
							<button type="submit" name="request" value="delBlock" class="btn btn-danger">Supprimer</button>
							<div class="btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Ajouter un bloc <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addBlock=before&refBlock=<?php echo $this->getFullId(); ?>#block_<?php echo $this->getParentId(); ?>-newBlock">Avant ce bloc</a></li>
									<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&addBlock=after&refBlock=<?php echo $this->getFullId(); ?>#block_<?php echo $this->getParentId(); ?>-newBlock">Après ce bloc</a></li>
								</ul>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Déplacer <span class="caret"></span>
								</button>
								<ul class="dropdown-menu">
									<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveBlock&moveBlock=before&refBlock=<?php echo $this->getFullId(); ?>#block_<?php echo $this->getFullId(); ?>">Vers le haut</a></li>
									<li><a href="<?php echo $settings->editURL; ?>&page=<?php echo $fileName; ?>&request=moveBlock&moveBlock=after&refBlock=<?php echo $this->getFullId(); ?>#block_<?php echo $this->getFullId(); ?>">Vers le bas</a></li>
								</ul>
							</div>
						<?php } ?>
					</div>
			</form>
		</div>
		<?php
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
}