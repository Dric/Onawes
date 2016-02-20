<?php
/**
 * Classe de connexion au site
 *
 * User: cedric.gallard
 * Date: 19/03/14
 * Time: 09:10
 *
 * @package Users
 */

use Alerts\Alert;
use Content\Theme;

/**
 * Classe de gestion de l'authentification
 *
 * @package Users
 */
class Security {

	/**
	 * Suppression du cookie d'authentification et de la session PHP
	 */
	static function deleteCookie(){
		global $settings;
		setcookie($settings->authCookieName, "", 1, '/', '', FALSE, TRUE); //On supprime le cookie
		unset($_COOKIE[$settings->authCookieName]);
		$_SESSION = array();
		session_destroy();
	}


	/**
	 * Vérifie que l'utilisateur est connecté
	 *
	 * @return bool
	 */
	static function isLoggedIn(){
		global $settings;
		if (isset($_COOKIE[$settings->authCookieName])){
			$cookie = $_COOKIE[$settings->authCookieName];
			if ($cookie == $settings->authSaltKey) return $cookie;
			// If bad salt key, we delete cookie too
			self::deleteCookie();
		}
		return false;
	}

	/**
	 * Mélange le mot de passe avec une clé de salage pour ne pas le mettre en clair dans la base de données
	 * @param string $pwd Mot de passe
	 *
	 * @return string
	 */
	static function saltPwd($pwd){
		global $settings;
		return sha1($pwd.$settings->authSaltKey);
	}

	/**
	 * Valide la connexion d'un utilisateur
	 * @return bool
	 */
	static function tryLogin(){
		global $settings;
		if (!isset($_REQUEST['loginPwd']) or empty($_REQUEST['loginPwd'])) {
			new Alert('error', 'Le mot de passe est vide !');
			return false;
		}
		$loginPwd = htmlspecialchars($_REQUEST['loginPwd']);
		$stayConnected = (isset($_REQUEST['stayConnected'])) ? true : false;
		if (!empty($loginPwd) and  $loginPwd == $settings->authPwd){
			$cookieDuration = ($stayConnected) ? (time()+(90*24*3600)) : 0;
			$ret = setcookie($settings->authCookieName, $settings->authSaltKey, $cookieDuration, '/', '', FALSE, TRUE);
			if (!$ret){
				new Alert('error', 'Impossible de créer le cookie d\'authentification !');
				return false;
			}
			header('location: '.Template::createURL(array('edit' => true)));
		}
		new Alert('error', 'Le mot de passe est incorrect !');
		return false;
	}



	/**
	 * Affiche le formulaire de connexion
	 *
	 */
	static function loginForm(){
		global $theme, $Content;
		$title = $Content->getSiteSettings()['mainTitle'];
		/** @var Theme $theme */
		$theme->toHTMLHeader(null, 'Connexion');
		?>
		<body>
			<div id="">
				<!-- Page content -->
				<div id="page-content-wrapper" class="login-wrap container">
					<!-- Si javascript n'est pas activé, on prévient l'utilisateur que ça va merder... -->
					<noscript>
						<div class="alert alert-danger">
							<p class="text-center">Ce site fonctionne sans Javascript, mais vous devriez quand même l'activer pour un plus grand confort d'utilisation.</p>
						</div>
					</noscript>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<div class="text-center">
								<h1>
									<?php if (isset($title) and !empty($title)) echo $title.' - '; ?>Connexion
								</h1>
							</div>
						</div>
					</div>
					<div class="row space-top">
						<div class="col-md-8 col-md-offset-2">
							<!-- Keep all page content within the page-content inset div! -->
							<form id="loginForm" class="" method="post" role="form">
								<div class="form-group">
									<label for="loginPwd">Mot de passe</label>
									<input type="password" class="form-control pwd input-lg" id="loginPwd" name="loginPwd" placeholder="Saisissez votre mot de passe">
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="stayConnected" checked>
										Rester connecté
									</label>
								</div>
								<button type="submit" name="tryLogin" class="btn btn-default btn-lg center-block">Connexion</button>
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
} 