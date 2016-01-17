<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 09:51
 */
spl_autoload_register(function ($class) {
	@include_once 'classes/' . str_replace("\\", "/", $class) . '.php';
});
// Définition de quelques variables
$isHTTPS = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'];
$args = array(
	'absolutePath'  => realpath(dirname(__FILE__)),
	'absoluteURL'   => rtrim((($isHTTPS) ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].str_replace('index.php', '', $_SERVER['PHP_SELF']), '/')
);

/**
 * Chargement des paramètres
 * @var Settings $settings
 */
if ('classes/LocalSettings.php'){
	$settings = new LocalSettings($args);
}else{
	$settings = new Settings($args);
}
session_start();

//$isLoggedIn = \Auth\Login::checkAuth();


Template::header();
?>
	<body>
	<div id="wrapper">
		<!-- Si javascript n'est pas activé, on prévient l'utilisateur que ça peut merder... -->
		<noscript>
			<div class="alert alert-info">
				<p>Ce site ne fonctionnera pas sans Javascript !</p>
			</div>
			<style>
				.tab-content>.tab-pane{
					display: block;
				}
			</style>
		</noscript>
		<div id="page-content-wrapper" class="container">
			<div class="content-header row">
				<div class="col-md-12">
					<h1>
						<a href="<?php echo $settings->absoluteURL; ?>"><?php echo $settings->scriptTitle; ?></a>
					</h1>
				</div>
			</div>
			<div class="page-content inset row">
				<?php
				$Content = new \Content\ContentManager();
				$page = $Content->addPageFromJSON('test.json');
				$Content->editPage($page);
				?>
			</div>
		</div>
		<footer>
			<?php Template::footer(); ?>
			<?php if ($settings->debug) echo ' | Mode debug activé | '; ?>
			<img class="tooltip-top" alt="PasSage !" src="<?php echo $settings->absoluteURL; ?>/img/PasSage-16.png" style="vertical-align: text-bottom;"/> <abbr title="Oh No, Antoher Website Editor System !">Onawes</abbr> 2016
		</footer>
	</div>
	<?php Template::jsFooter(); ?>
	</body>

<?php
