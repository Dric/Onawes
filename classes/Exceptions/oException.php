<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:16
 */

namespace Exceptions;


class oException extends \Exception{
	protected $isDebug = false;
	protected $messageType = null;

	/**
	 * @return boolean
	 */
	public function isIsDebug() {
		return $this->isDebug;
	}

	/**
	 * @param boolean $isDebug
	 */
	public function setIsDebug($isDebug) {
		$this->isDebug = $isDebug;
	}

	/**
	 * @return string
	 */
	public function getMessageType() {
		return $this->messageType;
	}

	/**
	 * @param string $messageType
	 */
	public function setMessageType($messageType) {
		$this->messageType = $messageType;
	}
}