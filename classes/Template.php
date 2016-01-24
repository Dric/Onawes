<?php
use Alerts\AlertsManager;

/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:27
 */

class Template {
	/**
	 * Tableau de lignes html à inclure dans la partie `<head>` de la page
	 * @var string[]
	 */
	protected static $header = array();
	/**
	 * Tableau de lignes html à inclure dans les chargement de scripts javascript dans la partie `<head>` de la page
	 *
	 * Afin d'améliorer la vitesse d'affichage de la page, mieux vaut charger les scripts js à la fin de la page, et donc les ajouter à {@link $jsFooter}
	 * @var string[]
	 */
	protected static $jsHeader = array();
	/**
	 * Tableau de lignes html à inclure dans les chargement de fichiers CSS dans la partie `<head>` de la page
	 * @var string[]
	 */
	protected static $cssHeader = array();
	/**
	 * Tableau de lignes html à inclure dans la partie `<footer>` de la page
	 * @var string[]
	 */
	protected static $footer = array();
	/**
	 * Tableau de lignes html à inclure dans les chargement de scripts javascript dans la partie `<footer>` de la page
	 *
	 * Les scripts js devraient être dans la mesure du possible ajoutés au maximum dans ce tableau.
	 *
	 * @var string[]
	 */
	protected static $jsFooter = array();

	public static function header($cssFile = null, $subTitle = ''){
		global $settings;
		if (empty($cssFile)) $cssFile = 'onawes.css';
		?>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="description" content="<?php echo $settings->scriptTitle; ?>">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
			<!--[if lt IE 9]>
			<script src="<?php echo $settings->absoluteURL; ?>/js/html5shiv.js"></script>
			<script src="<?php echo $settings->absoluteURL; ?>/js/respond.min.js"></script>
			<![endif]-->
			<title><?php echo $settings->scriptTitle; if (!empty($subTitle)) echo ' - '.$subTitle; ?></title>

			<!-- The CSS -->
			<link href="<?php echo $settings->absoluteURL; ?>/css/<?php echo $cssFile; ?>" rel="stylesheet">
			<?php self::cssHeader(); ?>
			<?php
			// On ajoute le contenu de $header
			foreach (self::$header as $headerLine){
				echo $headerLine.PHP_EOL;
			}
			?>
			<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-57x57.png">
			<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-60x60.png">
			<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-72x72.png">
			<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-76x76.png">
			<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-114x114.png">
			<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-120x120.png">
			<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-144x144.png">
			<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-152x152.png">
			<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $settings->absoluteURL; ?>/img/favicons/apple-touch-icon-180x180.png">
			<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-32x32.png" sizes="32x32">
			<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/android-chrome-192x192.png" sizes="192x192">
			<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-96x96.png" sizes="96x96">
			<link rel="icon" type="image/png" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon-16x16.png" sizes="16x16">
			<link rel="manifest" href="<?php echo $settings->absoluteURL; ?>/img/favicons/manifest.json">
			<link rel="shortcut icon" href="<?php echo $settings->absoluteURL; ?>/img/favicons/favicon.ico">
			<meta name="msapplication-TileColor" content="#ffc40d">
			<meta name="msapplication-TileImage" content="<?php echo $settings->absoluteURL; ?>/img/favicons/mstile-144x144.png">
			<meta name="msapplication-config" content="<?php echo $settings->absoluteURL; ?>/img/favicons/browserconfig.xml">
			<meta name="theme-color" content="#ffffff">
		</head>

	<?php
	}

	/**
	 * Affiche les scripts en haut de page
	 */
	public static function jsHeader(){
		foreach (self::$jsHeader as $headerLine){
			echo $headerLine.PHP_EOL;
		}
	}

	/**
	 * Affiche les css en haut de page
	 */
	public static function cssHeader(){
		foreach (self::$cssHeader as $headerLine){
			echo $headerLine.PHP_EOL;
		}
	}

	/**
	 * Ajoute le contenu de $footer
	 */
	public static function footer(){
		foreach (self::$footer as $footerLine){
			echo $footerLine.PHP_EOL;
		}
	}

	/**
	 * Affiche les scripts de bas de page
	 */
	public static function jsFooter(){
		global $settings;
		?>
		<!-- JavaScript obligatoire -->
		<script src="<?php echo $settings->absoluteURL; ?>/js/jquery-2.2.0.min.js"></script>
		<script src="<?php echo $settings->absoluteURL; ?>/js/bootstrap.min.js"></script>
		<script src="<?php echo $settings->absoluteURL; ?>/js/noty/packaged/jquery.noty.packaged.min.js"></script>
		<script src="<?php echo $settings->absoluteURL; ?>/js/onawes.js"></script>

		<?php
		foreach (self::$jsFooter as $footerLine){
			echo $footerLine.PHP_EOL;
		}
		?>
		<!-- Affichage des alertes -->
		<?php AlertsManager::getAlerts(); ?>
	<?php
	}

	/**
	 * Ajoute une ou plusieurs lignes html au header
	 * @param string $header
	 */
	public static function addHTMLToHeader($header) {
		self::$header[] = $header;
	}

	/**
	 * Ajoute une ou plusieurs lignes html au footer
	 * @param string $footer
	 */
	public static function addHTMLToFooter($footer) {
		self::$footer[] = $footer;
	}

	/**
	 * Ajoute une ligne dans le tableau des chargements de scripts javascript dans la partie `<head>` de la page
	 * @warning Privilégiez plutôt le chargement des scripts en fin de page via {@link setJsFooter()}
	 * @param string $js
	 */
	public static function addJsToHeader($js) {
		self::$jsHeader[] = $js;
	}

	/**
	 * Ajoute une ligne dans le tableau des chargements de fichiers CSS dans la partie `<head>` de la page
	 * @param string $css
	 */
	public static function addCSSToHeader($css) {
		self::$cssHeader[] = $css;
	}

	/**
	 * Ajoute une ligne dans le tableau des chargements de scripts javascript dans la partie `<footer>` de la page
	 * @param string $js
	 */
	public static function addJsToFooter($js) {
		self::$jsFooter[] = $js;
	}
}