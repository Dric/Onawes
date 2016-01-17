<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:41
 */

namespace Content;


use DOMDocument;
use FileSystem\Fs;
use simple_html_dom;
use Template;

class ContentManager {

	protected $contentDir;
	/**
	 * @var Page
	 */
	protected $page = null;

	public function __construct(){
		global $settings;
		$this->contentDir = $settings->absolutePath.DIRECTORY_SEPARATOR.$settings->contentDir;
	}

	/**
	 * Save content page to disk
	 *
	 * @param Page $page
	 */
	protected function saveContent(Page $page){
		$fs = new Fs($this->contentDir);
		// A backup is made of the file when saving
		?><pre><?php var_dump($page->toJSON()); ?></pre><?php
		$fs->writeFile($page->getFileName().'.json', $page->toJSON(), false, true);
	}

	/**
	 * Populate Page from file
	 *
	 * @param string $fileName File Name
	 *
	 * @return bool
	 */
	public function addPageFromFile($fileName){
		// reading file
		$fs = new Fs($this->contentDir);
		$fileContent = $fs->readFile($fileName, 'string');
		// parsing html
		$parsed = new simple_html_dom();
		$parsed->load($fileContent);
		// Creating page
		$page = new Page();
		foreach ($parsed->find('.row') as $rowNode){
			// creating rows
			$row = new Row($rowNode->id, $rowNode->class, $rowNode->tag);
			/** @var simple_html_dom $rowBlock */
			foreach ($rowNode->children as $rowBlock){
				// Creating blocks
				$block = new Block($rowBlock->id, $rowBlock->class, $rowBlock->tag);

				// Setting block widths
				$CSSClasses = explode(' ', $rowBlock->class);
				foreach ($CSSClasses as $class){
					$re = "/^col-(\\w{2})-(\\d+)/i";
					preg_match($re, $class, $matches);
					if (!empty($matches)){
						$block->setWidth($matches[1], (int)$matches[2]);
					}
				}
				// Finding title tag - this is the first tag in block
				$titleTag = $rowBlock->firstChild();
				if (in_array($titleTag->tag, array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'))){
					$re = "/h(\d)/i";
					preg_match($re, $titleTag->tag, $matches);
					$block->setTitle($matches[1], $titleTag->innertext);
					// Removing title from content
					$rowBlock->firstChild()->outertext = '';
				}
				// Setting block content
				$block->setContent($rowBlock->innertext);
				$row->addBlock($block);
			}

			// Adding row to page
			$page->addRow($row);
		}
		$this->page = $page;
		$this->saveContent($page);
		return $page;
	}

	public function addPageFromJSON($fileName){
		// reading file
		$fs = new Fs($this->contentDir);
		$fileContent = $fs->readFile($fileName, 'string');
		// Parsing JSON
		$jsonArray = json_decode($fileContent, true);
		$page = new Page();
		$page->setTitle($jsonArray['title']);
		foreach ($jsonArray['rows'] as $jsonRow){
			$row = new Row ($jsonRow['id']);
			$row->setTitle($jsonRow['title']);
			$row->setTag($jsonRow['tag']);
			foreach ($jsonRow['CSSClasses'] as $class){
				$row->addCSSClass($class);
			}
			foreach ($jsonRow['blocks'] as $jsonBlock){
				$block = new Block ($jsonBlock['id']);
				$block->setTitle($jsonBlock['titleLevel'], $jsonBlock['title']);
				$block->setTag($jsonBlock['tag']);
				foreach ($jsonBlock['CSSClasses'] as $class){
					$block->addCSSClass($class);
				}
				foreach ($jsonBlock['widths'] as $width => $size){
					$block->setWidth($width, $size);
				}
				$block->setContent($jsonBlock['content']);
				$row->addBlock($block);
			}
			$page->addRow($row);
		}
		return $page;
	}


	public function editPage(Page $page){
		global $settings;
		?><h2>Edition de la page <code><?php echo $page->getTitle(); ?></code></h2><?php
		/** @var Row $row */
		foreach ($page->getRows() as $row){
			?>
			<div class="well-lg">
				<h3>Edition de la ligne <code><?php echo $row->getTitle(); ?></code></h3>
				<div class="row">
				<?php
				$nbBlocks = 0;
				foreach ($row->getBlocks() as $index => $block){
					$nbBlocks++;
					$block->setContentForm();
					// even (impair) number
					if ($nbBlocks%2 != 1){
						?><div class="clearfix"></div><?php
					}
				}
				$addBlock = new Block('new');
				$addBlock->setContentForm();
				?>
				</div>
			</div>
			<?php
		}
		\Template::addCSSToHeader('<link href="'.$settings->absoluteURL.'/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" rel="stylesheet">');
		\Template::addJsToFooter('<script type="text/javascript" src="'.$settings->absoluteURL.'/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>');
		\Template::addJsToFooter('<script>$(\'textarea\').pagedownBootstrap();</script>');
	}
}