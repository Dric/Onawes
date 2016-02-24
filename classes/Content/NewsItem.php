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