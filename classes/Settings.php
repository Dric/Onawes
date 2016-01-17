<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 08/04/2015
 * Time: 10:47
 */

/**
 * Classe regroupant les paramètres de PasSage
 *
 *
 * @property-read string  $authMode       Mode d'authentification
 * @property-read string  $authPwd        Mot de passe
 * @property-read string  $authCookieName Nom du cookie utilisé
 * @property-read string  $authSaltKey    Clé de salage
 *
 * @property-read string  $absolutePath   Chemin absolu du script
 * @property-read string  $absoluteURL    URL du script
 *
 * @property-read string  $scriptTitle    Titre de la page
 *
 * @property-read bool    $debug          Mode debug
 *
 * @property-read string  $contentDir     Chemin du contenu
 */
class Settings {

	protected $authMode       = 'file';
	protected $authPwd        = '123';
	protected $authCookieName = 'PasSage';
	protected $authSaltKey    = 'Kqw+LP1P_P(;7 zU/DRSo0g%P~vrPMjHk2558r)C]5RX:jD~}9,Bgy#+$-pOVkhp';

	protected $debug          = false;

	protected $absolutePath   = '';
	protected $absoluteURL    = '';

	protected $scriptTitle    = 'onawes';

	protected $contentDir     = 'content';


	public function __construct(Array $args = array()){
		foreach ($args as $arg => $value){
			if (isset($this->$arg) and gettype($this->$arg) === gettype($value)){
				$this->$arg = $value;
			}
		}
	}

	public function __get($var){
		if (isset($this->$var)) return $this->$var;
		return null;
	}
}