<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 15/02/2016
 * Time: 14:43
 */

namespace Content;

use FileSystem\File;
use FileSystem\Fs;
use Michelf\MarkdownExtra;

Class NewsItem{

	/** @var string  */
	protected $title = null;
	/** @var string  */
	protected $category = null;
	/** @var string  */
	protected $content = null;
	/** @var string  */
	protected $dateCreated = null;
	/**
	 * NewsItem file name
	 * @var string
	 */
	protected $fileName = null;

	/**
	 * Classe de gestion des articles
	 *
	 * @param string  $category
	 * @param string  $fileName
	 * @param string  $title
	 */
	function __construct($category, $fileName, $title = null){
		global $Content;
		$this->fileName = $fileName;
		$this->setCategory($category);
		$this->title = $title;
		$fs = new Fs($Content->getContentDir().DIRECTORY_SEPARATOR.'News'.DIRECTORY_SEPARATOR.\Sanitize::sanitizeFilename($category));
		if ($fs->fileExists($fileName)){
			$fileMeta = $fs->getFileMeta($fileName, array('dateCreated'));
			$this->dateCreated = $fileMeta->dateCreated;
			$fileContent = $fs->readFile($fileName, 'string');
			if ($fileContent) {
				// Parsing JSON
				$jsonArray = json_decode($fileContent, true);
				if (!empty($jsonArray['title'])) $this->setTitle($jsonArray['title']);
				if (!empty($jsonArray['category'])) $this->setCategory($jsonArray['category']);
				if (!empty($jsonArray['content'])) $this->setContent($jsonArray['content']);
			}
		}
	}

	public function display(){
			?>
			<article class="news row" id="news_<?php echo \Sanitize::sanitizeFilename($this->category.'_'.$this->title); ?>">
				<div class="news-cat col-xs-3"><?php echo $this->category; ?></div>
				<div class="col-xs-9"><div class="news-expand"><a title="Afficher les détails" data-toggle="collapse" data-target="#news_details_<?php echo \Sanitize::sanitizeFilename($this->category.'_'.$this->title); ?>"><i class="fa fa-chevron-down" ></i></a></div>
					<span class="news-date"><?php echo \Sanitize::date($this->dateCreated, 'date'); ?></span>
					<div class="news-title"><?php echo $this->title; ?></div>
					<div class="news-details collapse" id="news_details_<?php echo \Sanitize::sanitizeFilename($this->category.'_'.$this->title); ?>">
						<?php echo $this->getContent(); ?>
					</div>
				</div>
			</article>
			<?php
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'category'    => $this->category,
			'content'     => $this->content
		);
		return $array;
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Transforme un timestamp en date
	 * @param int $ts timestamp unix
	 * @param bool $notime Retourne ou non l'heure avec la date
	 * @return string
	 *
	 */
	public function ts_to_date($ts, $notime = false){
		if ($notime){
			return date('d/m/Y', $ts);
		}else{
			return date('d/m/Y H:i', $ts);
		}
	}


	/**
	 * Formulaire d'ajout/modification d'article
	 * @param string $mode ('add'|'edit') Formulaire en ajout ou en édition
	 * @param bool $ajax Formulaire chargé via Ajax ou non
	 * @param int $id En mode édition, ID de l'article
	 *
	 */
	public function form($mode = 'add', $ajax = false, $id = null){
		if ($mode == 'edit'){
			global $wpdb;
			$article = $wpdb->get_row('SELECT * FROM articles WHERE id = '.$id);
		}
		?>
		<form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
			<?php if ($mode == 'edit'){ ?>
				<input type="hidden" value="<?php echo $id; ?>" name="id">
			<?php } ?>
			<label for="cat">Catégorie <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
			<select name="cat" id="cat" class="input-block-level">
				<option></option>
				<?php
				foreach ($this->cats as $cat){
					echo '<option';
					if ($mode == 'edit' and $article->cat == $cat){
						echo ' selected ';
					}
					echo '>'.$cat.'</option>';
				}
				?>
			</select>
			<label for="titre">Titre <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
			<input type="text" name="titre" id="titre" placeholder="Titre de l'article" class="input-block-level" <?php echo ($mode == 'edit')?'value="'.$article->titre.'"':'';?>>
			<label for="contenu">Contenu <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
			<textarea name="contenu" id="contenu" placeholder="Contenu de l'article" rows="6" class="input-block-level"><?php echo ($mode == 'edit')?$article->contenu:'';?></textarea>
			<!--<label for="file">Fichier (optionnel) <span title="Vous pouvez joindre un fichier (pdf, xls, doc, etc.) ou une image (jpg, gif ou png). L'image sera automatiquement affichée sous le contenu de l'article." class="tooltip-bottom"><i class="icon-question-sign"></i></span></label>
	    <input type="file" name="file" id="file" class="input-block-level">-->
			<?php if (!$ajax){ ?>
				<div class="help small">
					<a class="help-summary" title="Afficher les détails" data-toggle="collapse" data-target="#contact-help-details"><i class="icon-question-sign"></i> Utilisation du formulaire d'ajout d'article</a>
					<div id="contact-help-details" class="collapse">
						Il y a quelques règles évidentes et usuelles à respecter pour vous servir de ce formulaire :
						<ul>
							<li>Vérifiez que votre français est compréhensible et ne contrevient pas trop aux normes orthographiques et grammaticales en vigueur.</li>
							<li>Pensez-bien à affecter votre article à la bonne catégorie.</li>
							<li>Les astérisques rouges signalent que le champ est obligatoire pour que votre article soit publié.</li>
							<li>Il n'y a pas de validation des articles. Ce qui veut dire que si vous écrivez une grosse ânerie, elle sera publiée directement sur le site. Pensez-donc bien à vous relire.</li>
						</ul>
					</div>
				</div>
			<?php } ?>
			<div class="text-right"><button type="submit" class="btn btn-large" id="add_post_send" name="add_post_send"><?php echo ($mode == 'edit')?'Modifier l\'article':'Publier l\'article'; ?></button></div>
		</form>
		<?php
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
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param string $category
	 */
	public function setCategory($category) {
		global $Content;
		$this->category = (in_array($category, $Content->getNewsCategories())) ? $category : $Content->getNewsCategories()[0] ;
	}

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

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getDateCreated() {
		return $this->dateCreated;
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
}
?>