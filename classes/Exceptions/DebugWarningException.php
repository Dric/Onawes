<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:15
 */

namespace Exceptions;


class DebugWarningException extends oException {
	protected $isDebug = true;
	protected $messageType = "Error";
}