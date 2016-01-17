<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:18
 */

namespace Exceptions;


class DebugErrorException extends oException{
	protected $isDebug = true;
	protected $messageType = "Error";
}