<?php
/**
 * Creator: Dric
 * Date: 22/02/2016
 * Time: 12:27
 */

namespace Components;

use Content\Themes\Home;
use FileSystem\Fs;

class Installer {

	public static function doSetup(){
		$title = 'Onawes';
		$theme = New Home();
		$theme->toHTMLHeader(null, 'Installation');
		?>
		<body>
		<div id="">
			<!-- Page content -->
			<div class="login-wrap container">
				<!-- Si javascript n'est pas activé, on prévient l'utilisateur que ça va merder... -->
				<noscript>
					<div class="alert alert-danger">
						<p class="text-center">Ce site ne fonctionne pas sans Javascript.</p>
					</div>
				</noscript>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<div class="text-center">
							<h1>
								<?php if (isset($title) and !empty($title)) echo $title.' - '; ?>Installation
							</h1>
						</div>
					</div>
				</div>
				<div class="row space-top">
					<div class="col-md-8 col-md-offset-2">
						<p>Onawes a détecté que vous le lancez pour la première fois. Pas de panique, nous allons procéder à quelques parmétrages...</p>
						<!-- Keep all page content within the page-content inset div! -->
						<form id="loginForm" class="" method="post" role="form">
							<div class="form-group">
								<label for="adminPwd">Mot de passe</label>
								<input type="password" class="form-control pwd input-lg" id="adminPwd" name="adminPwd" placeholder="Saisissez le mot de passe d'administration">
								<span class="help-block">Saisissez un mot de passe qui vous sera demandé lorsque vous voudrez accéder à la partie Administration d'Onawes.</span>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="usePrettyURL" checked>
									Utiliser les prettyURL (recommandé, mais peut nécessiter un peu de configuration)
								</label>
							</div>
							<button type="submit" name="createLocalSettings" class="btn btn-default btn-lg center-block">Enregistrer les paramètres</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php $theme->toHTMLFooter(); ?>
		</body>
		<?php
		exit;
	}

	protected static function generateSalt($length = 64){
		return base64_encode(openssl_random_pseudo_bytes($length));
	}

	public static function createLocalSettings(){
		global $args;
		//Creating htaccess file...
		$fs = new \FileSystem\Fs($args['absolutePath']);
		$path = DIRECTORY_SEPARATOR.trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		$ret = $fs->writeFile('.htaccess', 'FallbackResource '.$path.'index.php');


		// Creating LocaSettings File...
		$authSalt = self::generateSalt();
		$cookieSalt = self::generateSalt(32);
		$pwd = hash('SHA512',$_REQUEST['adminPwd'].$authSalt);
		$prettyURL = (isset($_REQUEST['usePrettyURL'])) ? 'true' : 'false';
		$fs = new Fs($args['absolutePath'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR);
		$content = '<?php
    		class LocalSettings extends Settings {
    		  protected $authPwd      = \''.$pwd.'\';
    		  protected $authSaltKey  = \''.$authSalt.'\';
    		  protected $cookieKey    = \''.$cookieSalt.'\';
    		  protected $prettyURL    = '.$prettyURL.';
    	  }';
		return $fs->writeFile('LocalSettings.php', trim($content));
	}
}