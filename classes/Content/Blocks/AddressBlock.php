<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 15/02/2016
 * Time: 22:25
 */

namespace Content\Blocks;

use Content\Block;
use Michelf\MarkdownExtra;

class AddressBlock extends Block{
	/**
	 * Type de bloc
	 * @var string
	 */
	protected $type = 'Address';

	/** @var string */
	protected $givenName = null;
	/** @var string */
	protected $streetAddress = null;
	/** @var string */
	protected $postalCode = null;
	/** @var string */
	protected $locality = null;
	/** @var string */
	protected $latitude = null;
	/** @var string */
	protected $longitude = null;
	/** @var string */
	protected $description = null;

	public function toArray(){

		$array = parent::toArray();
		$array['properties']['givenName']     = $this->givenName;
		$array['properties']['streetAddress'] = $this->streetAddress;
		$array['properties']['postalCode']    = $this->postalCode;
		$array['properties']['locality']      = $this->locality;
		$array['properties']['latitude']      = $this->latitude;
		$array['properties']['longitude']     = $this->longitude;
		$array['properties']['description']   = $this->description;

		return $array;
	}

	public function getHTMLCustom(){
		?>
		<address>
			<div class="fn n">
				<span class="given-name">
					<strong><?php echo $this->givenName; ?></strong>
				</span>
			</div>
			<div class="adr">
				<span class="street-address"><?php echo $this->streetAddress; ?></span><br>
				<span class="postal-code"><?php echo $this->postalCode; ?></span>
				<span class="locality"><?php echo $this->locality; ?></span>
			</div>
			<?php if (!empty($this->latitude) and !empty($this->longitude)){ ?>
			<div class="geo">
				<abbr title="Coordonnées GPS" class="tooltip-bottom">GPS</abbr> :
				<span class="latitude"><?php echo $this->latitude; ?></span>,
				<span class="longitude"><?php echo $this->longitude; ?></span>
			</div>
			<?php } ?>
		</address>
		<?php
		echo $this->getDescription();
	}

	/**
	 * Returns the fields sent by block editing form
	 * @return string[]
	 */
	public function getRequestFieldsToSave(){
		return array('givenName', 'streetAddress', 'postalCode', 'locality', 'latitude', 'longitude', 'description');
	}

	public function getFormCustomFields(){
		?>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_givenName">Nom du lieu</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_givenName" name="block_<?php echo $this->getFullId(); ?>_givenName" value="<?php echo $this->getGivenName(); ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_streetAddress">Adresse (rue, numéro)</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_streetAddress" name="block_<?php echo $this->getFullId(); ?>_streetAddress" value="<?php echo $this->getStreetAddress(); ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_postalCode">Code postal</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_postalCode" name="block_<?php echo $this->getFullId(); ?>_postalCode" value="<?php echo $this->getPostalCode(); ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_locality">Ville</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_locality" name="block_<?php echo $this->getFullId(); ?>_locality" value="<?php echo $this->getLocality(); ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_latitude">GPS : Latitude</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_latitude" name="block_<?php echo $this->getFullId(); ?>_latitude" value="<?php echo $this->getLatitude(); ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_longitude">GPS : Longitude</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_longitude" name="block_<?php echo $this->getFullId(); ?>_longitude" value="<?php echo $this->getLongitude(); ?>">
			</div>
		</div>
		<div class="form-group form-group-sm">
			<label for="block_<?php echo $this->getFullId(); ?>_description">Description additionnelle</label>
			<textarea name="block_<?php echo $this->getFullId(); ?>_description" id="block_<?php echo $this->getFullId(); ?>_description" class="form-control" rows="8"><?php	echo $this->getDescription(true);	?></textarea>
		</div>
		<?php
	}

	public function getExcerpt(){
		parent::getExcerpt();
		if ($this->givenName) { ?><p class="small">Adresse : <code><?php echo \Get::excerpt($this->givenName, 40); ?></code></p><?php }
	}

	/**
	 * @return string
	 */
	public function getGivenName() {
		return $this->givenName;
	}

	/**
	 * @param string $givenName
	 */
	public function setGivenName($givenName) {
		$this->givenName = $givenName;
	}

	/**
	 * @return string
	 */
	public function getStreetAddress() {
		return $this->streetAddress;
	}

	/**
	 * @param string $streetAddress
	 */
	public function setStreetAddress($streetAddress) {
		$this->streetAddress = $streetAddress;
	}

	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postalCode;
	}

	/**
	 * @param string $postalCode
	 */
	public function setPostalCode($postalCode) {
		$this->postalCode = $postalCode;
	}

	/**
	 * @return string
	 */
	public function getLocality() {
		return $this->locality;
	}

	/**
	 * @param string $locality
	 */
	public function setLocality($locality) {
		$this->locality = $locality;
	}

	/**
	 * @return string
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * @param string $latitude
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}

	/**
	 * @return string
	 */
	public function getLongitude() {
		return $this->longitude;
	}

	/**
	 * @param string $longitude
	 */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}

	/**
	 * @param bool $rawContent MarkDown processed if false, raw content if true
	 *
	 * @return string
	 */
	public function getDescription($rawContent = false) {
		if (!$rawContent){
			$content = \Sanitize::MarkdownToHTML($this->description);
		}else{
			$content = $this->description;
		}
		return $content;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
}