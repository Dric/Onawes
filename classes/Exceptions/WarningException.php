<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:14
 */

namespace Exceptions;


class WarningException extends oException{
	protected $isDebug = false;
	protected $messageType = "Error";
}