<?php
/**
 * Creator: Dric
 * Date: 05/02/2016
 * Time: 12:53
 */

namespace Content\Blocks;

use Alerts\Alert;
use Content\Block;
use Exception;
use PHPMailer;

class ContactFormBlock extends Block{
	/**
	 * Type de bloc
	 * @var string
	 */
	protected $type = 'ContactForm';

	/**
	 * Destinataires
	 *
	 * array('label' => 'email address')
	 * @var string[]
	 */
	protected $sendTo = array();

	/**
	 * @var string[]
	 */
	protected $copyTo = array();

	/**
	 * @var string
	 */
	protected $subject = null;

	/**
	 * @var bool
	 */
	protected $antiSpam = false;
	/**
	 * Returns the fields sent by block editing form
	 * @return string[]
	 */
	public function getRequestFieldsToSave(){
		return array('sendTo', 'copyTo', 'antiSpam');
	}

	public function getHTMLCustom(){
		$this->displayForm();
	}


	public function getFormCustomFields(){
		?>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_sendTo">Destinataires possibles (à remplir comme tel : nom##adresse-email, séparés par des virgules)</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_sendTo" name="block_<?php echo $this->getFullId(); ?>_sendTo" value="<?php echo $this->getSendTo(true); ?>" required>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-5 control-label" for="block_<?php echo $this->getFullId(); ?>_copyTo">Copie à (à remplir comme tel : nom##adresse-email, séparés par des virgules)</label>
			<div class="col-sm-5">
				<input type="text" class="form-control" id="block_<?php echo $this->getFullId(); ?>_copyTo" name="block_<?php echo $this->getFullId(); ?>_copyTo" value="<?php echo $this->getCopyTo(true); ?>">
			</div>
		</div>
		<div class="checkbox">
			<label>
				<input type="checkbox" name="block_<?php echo $this->getFullId(); ?>_antiSpam" id="block_<?php echo $this->getFullId(); ?>_antiSpam" <?php if ($this->antiSpam) echo 'checked'; ?>>
				Utiliser un antispam (très basic)
			</label>
		</div>
		<?php
	}

	public function toArray(){

		$array = parent::toArray();
		$array['properties']['sendTo'] = $this->sendTo;
		$array['properties']['copyTo'] = $this->copyTo;
		$array['properties']['antiSpam'] = $this->antiSpam;
		return $array;
	}

	/**
	 * Envoi du mail
	 *
	 * @param string $sendTo        Destinataire
	 * @param string $expName       Nom de l'expéditeur
	 * @param string $expEmail      Adresse email de l'expéditeur
	 * @param string $message       Corps de l'email
	 * @param string $antispamCheck Si renseigné, nous avons affaire à un spam !
	 * @param bool   $isCopy        Est un email en copie
	 *
	 * @return bool
	 */
	public function sendEmail($sendTo, $expName, $expEmail, $message, $antispamCheck = null, $isCopy = false){
		if (!empty($antispamCheck)){
			die('NO SPAM ALLOWED (how surprising isn\'t it ?)');
		}
		if (empty($sendTo) or empty($expName) or empty($expEmail) or empty($message)){
			new Alert('error', 'Au moins un des champs requis est vide !');
			return false;
		}
		$expName = htmlentities($expName);
		if (!\Check::isEmail($expEmail)){
			new Alert('error', 'L\'adresse email n\'est pas valide');
			return false;
		}
		$message = htmlentities($message);

		$mail = new PHPMailer(true);
		$mail->CharSet = 'utf-8';

		try {
			$to = $sendTo;
			if(!PHPMailer::validateAddress($to)) {
				new Alert('error', 'Email address ' . $to . ' is invalid -- aborting!');
			}
			$mail->isMail();
			$mail->addReplyTo($expEmail, $expName);
			$mail->From       = $expEmail;
			$mail->FromName   = $expName;
			$mail->addAddress($sendTo);
			$mail->Subject  = (($isCopy) ? 'Copie d\'un' : '').$this->subject;
			$body = $message;
			$mail->WordWrap = 78;
			$mail->msgHTML($body, dirname(__FILE__), true); //Create message bodies and embed images
			/*$mail->addAttachment('images/phpmailer_mini.png','phpmailer_mini.png');  // optional name
			$mail->addAttachment('images/phpmailer.png', 'phpmailer.png');  // optional name*/

			try {
				$mail->send();
				if (!$isCopy)	new Alert('success', 'Le message a été correctement envoyé !');
				return true;
			}	catch (Exception $e) {
				new Alert('error', 'Impossible d\'envoyer le message à ' . $to. ': '.$e->getMessage());
				return false;
			}
		}	catch (Exception $e) {
			new Alert('error', $e->getMessage());
			return false;
		}
	}

	/**
	 * Affichage du formulaire de contact
	 *
	 */
	public function displayForm(){
		global $settings;
		if (isset($_REQUEST['email_SendTo']) and isset($_REQUEST['email_SenderName']) and isset($_REQUEST['email_SenderEmail']) and isset($_REQUEST['email_Message'])){
			$spamField = (isset($_REQUEST['email_Subject'])) ? $_REQUEST['email_Subject'] : null;
			if (isset($this->sendTo[$_REQUEST['email_SendTo']])){
				$ret = $this->sendEmail($this->sendTo[$_REQUEST['email_SendTo']], $_REQUEST['email_SenderName'], $_REQUEST['email_SenderEmail'], $_REQUEST['email_Message'], $spamField);
				// If the main recipient is the same as the copyTo recipient, we don't send the same email two times to same address
				if ($ret and !empty($this->copyTo)){
					foreach ($this->copyTo as $name => $email){
						if ($name != $_REQUEST['email_SendTo']){
							$ret = $this->sendEmail($email, $_REQUEST['email_SenderName'], $_REQUEST['email_SenderEmail'], $_REQUEST['email_Message'], null, true);
						}
					}
				}
			}else{
				new Alert('error', 'Le destinataire de l\'email est invalide !');
			}
		}elseif(isset($_REQUEST['email_SendTo'])){
			new Alert('error', 'Au moins un des champs requis n\'est pas rempli !');
		}
		?>
		<form action="#<?php echo $this->getFullId(); ?>" method="POST">
			<div class="form-group">
				<label for="email_SendTo">Contacter <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
				<select name="email_SendTo" id="email_SendTo" class="form-control" required>
					<?php if (count($this->sendTo) > 1){ ?>
						<option></option>
						<?php
					}
					foreach ($this->sendTo as $label=>$email){
						echo '<option value="'.htmlentities($label).'">'.$label.'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="email_SenderEmail">Votre addresse Email <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
				<input type="email" name="email_SenderEmail" id="email_SenderEmail" placeholder="Email" class="form-control" required>
			</div>
			<div class="form-group">
				<label for="email_SenderName">Votre Nom/Pseudo <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
				<input type="text" name="email_SenderName" id="email_SenderName" placeholder="Nom/Pseudo" class="form-control" required>
			</div>
			<?php if ($this->antiSpam){ ?>
				<div class="hide form-group">
					<label for="email_Subject">Sujet</label>
					<input type="text" name="email_Subject" id="email_Subject" placeholder="Sujet" class="form-control">
				</div>
			<?php } ?>
			<div class="form-group">
				<label for="email_Message">Votre Message <span title="Obligatoire" class="required tooltip-bottom">*</span></label>
				<textarea name="email_Message" id="email_Message" placeholder="Message" rows="6" class="form-control" required></textarea>
			</div>
			<div class="help span4 small">
				<a class="help-summary" title="Afficher les détails" data-toggle="collapse" data-target="#contact-help-details"><i class="fa fa-question-circle"></i> Utilisation du formulaire de contact</a>
				<div id="contact-help-details" class="collapse">
					Il y a quelques règles évidentes et usuelles à respecter pour vous servir de ce formulaire de contact :
					<ul>
						<li>Vérifiez que votre français est compréhensible et ne contrevient pas trop aux normes orthographiques et grammaticales en vigueur.</li>
						<li>Si vous souhaitez utiliser ce formulaire pour envoyer du spam, abstenez-vous. Vous perdez votre temps et le nôtre.</li>
						<li>Les astérisques signalent que le champ est obligatoire pour que votre message soit envoyé.</li>
						<li>Nous essaierons de vous répondre dans les plus brefs délais, mais n'hésitez pas à nous relancer si vous ne recevez pas de réponses au bout d'une semaine.</li>
					</ul>
				</div>
			</div>
			<div class="span1 text-right"><button type="submit" class="btn btn-default btn-lg" id="contact_send" name="contact_send">Envoyer</button></div>

		</form>
		<?php
	}

	/**
	 * @return \string[]|string
	 */
	public function getSendTo($returnString = false) {
		if ($returnString) {
			$ret = null;
			foreach ($this->sendTo as $label => $email){
				$ret .= $label.'##'.$email.',';
			}
			return rtrim($ret, ',');
		}else{
			return $this->sendTo;
		}
	}

	/**
	 * @param \string[]|string $sendTo
	 */
	public function setSendTo($sendTo) {
		if (!is_array($sendTo)){
			// Avec array_map('trim', $array) on supprime les espaces des valeurs
			$arr = array_map('trim',explode(',', $sendTo));
			$sendTo = array();
			foreach($arr as $contact){
				$contactArray = explode('##', $contact);
				if (!empty($contactArray[0]) or !empty($contactArray[1]))	$sendTo[$contactArray[0]] = $contactArray[1];
			}
		}
		$this->sendTo = $sendTo;
	}

	/**
	 * @return \string[]|string
	 */
	public function getCopyTo($returnString = false) {
		if ($returnString) {
			$ret = null;
			foreach ($this->copyTo as $label => $email){
				$ret .= $label.'##'.$email.',';
			}
			return rtrim($ret, ',');
		}else{
			return $this->copyTo;
		}
	}

	/**
	 * @param \string[]|string $copyTo
	 */
	public function setCopyTo($copyTo) {
		if (!empty($copyTo)){
			if (!is_array($copyTo)){
				// Avec array_map('trim', $array) on supprime les espaces des valeurs
				$arr = array_map('trim',explode(',', $copyTo));
				$copyTo = array();
				foreach($arr as $contact){
					$contactArray = explode('##', $contact);
					if (!empty($contactArray[0]) or !empty($contactArray[1])) $copyTo[$contactArray[0]] = $contactArray[1];
				}
			}
			$this->copyTo = $copyTo;
		}
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @return boolean
	 */
	public function hasAntiSpam() {
		return $this->antiSpam;
	}

	/**
	 * @param boolean $antiSpam
	 */
	public function setAntiSpam($antiSpam) {
		$this->antiSpam = $antiSpam;
	}
}