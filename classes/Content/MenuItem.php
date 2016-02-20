<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 23/01/2016
 * Time: 19:00
 */

namespace Content;

use Alerts\Alert;

class MenuItem {

	/**
	 * @var string
	 */
	protected $id = null;

	/**
	 * @var string
	 */
	protected $title = null;

	/**
	 * @var string
	 */
	protected $link = null;

	/**
	 * Icon from Font Awesome
	 * @var string Font Awesome icons are defined by CSS classes ("fa fa-link" for link icon). Just set "link" to get the link icon.
	 */
	protected $icon = null;

	/**
	 * @var bool
	 */
	protected $noLabel = false;
	/**
	 * @var string
	 */
	protected $parentId = null;

	/**
	 * @var string[]
	 */
	protected $CSSClasses = array();

	/**
	 * Menu Item
	 *
	 * @param string  $id         Item ID
	 * @param string  $title      Item label
	 * @param string  $link       Item link
	 * @param string  $parentId   Menu ID
	 * @param string  $icon       Font Awesome Icon
	 * @param bool    $noLabel    If true, the item is only displayed with its icon
	 * @param string  $CSSClasses Custom CSS classes, separated by whitespaces
	 */
	public function __construct($id, $title, $link, $parentId, $icon = null, $noLabel = false, $CSSClasses = null) {
		$this->id = $id;
		$this->parentId = $parentId;
		$this->title = $title;
		$this->link = $link;
		$this->icon = $icon;
		if ($noLabel and empty($icon)){
			new Alert('debug', 'L\'item de menu ne peut pas être seulement une icône (noLabel = true) car aucune icône n\'a été définie !');
		}elseif($noLabel){
			$this->noLabel = $noLabel;
		}
		if (!empty($CSSClasses)) $this->CSSClasses = explode(' ', $CSSClasses);
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
	 * @return string
	 */
	public function getParentId() {
		return $this->parentId;
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}

	public function getHTMLIcon(){
		return '<i class="fa fa-'.$this->icon.'"></i>';
	}

	/**
	 * @param string $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
	}

	/**
	 * @return boolean
	 */
	public function hasNoLabel() {
		return $this->noLabel;
	}

	/**
	 * @param boolean $noLabel
	 */
	public function setNoLabel($noLabel) {
		$this->noLabel = $noLabel;
	}

	public function toJSON(){
		return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	public function toArray(){
		$array = array(
			'title'       => $this->title,
			'id'          => $this->id,
			'CSSClasses'  => $this->CSSClasses,
			'link'        => $this->link,
			'icon'        => $this->icon,
		  'noLabel'     => $this->noLabel
		);
		return $array;
	}

	public function toHTML(){
		$CSSClasses = $this->getCSSClasses(true);
		?>
		<li <?php if (!empty($this->id)) echo 'id="menuItem_'.$this->id.'"'; ?> <?php if (!empty($CSSClasses)) echo 'class="'.$CSSClasses.'"'; ?>>
			<a href="<?php echo $this->link; ?>"><?php if (!empty($this->icon)) echo $this->getHTMLIcon(); ?> <?php if (!empty($this->title)) echo $this->title; ?></a>
		</li>
		<?php
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