<?php
/**
 * Created by PhpStorm.
 * User: cedric.gallard
 * Date: 04/04/14
 * Time: 14:42
 */
namespace Alerts;

use Sanitize;

/**
 * Classe de gestion des alertes
 *
 * @package Logs
 */
class AlertsManager {

	/**
	 * Liste des alertes générées
	 * @var Alert[]
	 */
	static protected $alerts = array();

	/**
	 * Tableau des types d'alertes autorisés
	 *
	 * Ces types d'alertes reprennent les types d'alerte de Bootstrap (sauf 'debug' et 'error' qui est remappé sur 'danger')
	 * @var string[]
	 */
	protected static $allowedTypes = array('success', 'warning', 'info', 'danger', 'error', 'debug');

	/**
	 * Retourne ou affiche les alertes générées
	 *
	 * @param string $type Type d'alerte à afficher
	 * @param string $format Format d'affichage (js ou html) (facultatif)
	 *
	 * @return void
	 */
	static public function getAlerts($type = '', $format = 'js'){
		global $settings;
		if (!empty($type)){
			foreach (self::$alerts[$type] as $alert){
				self::displayAlert($alert, $format);
			}
		}else{
			foreach (self::$alerts as $type => $typeAlerts){
				if ((!$settings->debug and $type != 'debug') or $settings->debug){
					foreach ($typeAlerts as $alert){
						self::displayAlert($alert, $format);
					}
				}
			}
		}
		if ($format == 'js') echo '</script>'.PHP_EOL;
	}

	/**
	 * Affiche l'alerte
	 *
	 * @param Alert  $alert Alerte à afficher
	 */
	public static function displayAlert(Alert $alert){
		$type = $alert->getType();
		if ($type == 'danger')  $type = 'error';
		if ($type == 'debug')   $type = 'warning';
		if ($type == 'info')    $type = 'information';

		$animation = 'flipInX';
		switch ($type){
			case 'error':   $animation = 'rubberBand';break;
			case 'success': $animation = 'tada';break;
		}
		$content = (($alert->getTitle() != '') ? '<h3>'.$alert->getTitle().'</h3>':'').Sanitize::SanitizeForJs($alert->getContent());
		?>
		<script>
		noty({
			text: '<?php echo $content; ?>',
			theme: 'bootstrapTheme',
			type: '<?php echo $type ?>',
			layout: 'center',
			animation: {
				open: 'animated <?php echo $animation; ?>', // Animate.css class names
				close: 'animated flipOutX' // Animate.css class names
			},
			closeWith: ['click', 'button']
		});
		</script>
		<?php
	}

	/**
	 * Retourne les types d'alertes autorisés
	 * @return array
	 */
	public static function getAllowedTypes() {
		return self::$allowedTypes;
	}

	/**
	 * Ajoute une alerte à la liste des alertes générées
	 * @param Alert $alert Alerte à ajouter
	 */
	public static function addToAlerts(Alert $alert){
		self::$alerts[$alert->getType()][] = $alert;
	}

	/**
	 * Supprime une alerte de la liste des alertes générées
	 * @param Alert $alert Alerte à supprimer
	 */
	public static function removeAlert(Alert $alert){
		unset(self::$alerts[$alert->getType()][array_search($alert, self::$alerts, true)]);
	}

	/**
	 * Affiche les alertes de type 'debug'
	 */
	public static function debug(){
		global $db, $classesUsed;
		new Alert('debug', '<code>Db->getQueriesCount</code> : <strong>'.$db->getQueriesCount().'</strong> requête(s) SQL effectuées.');
		new Alert('debug', '<code>PHP</code> : Mémoire utilisée : <ul><li>Script :  <strong>'.Sanitize::readableFileSize(memory_get_usage()).'</strong></li><li>Total :   <strong>'.Sanitize::readableFileSize(memory_get_usage(true)).'</strong></li></ul>');
	}
} 