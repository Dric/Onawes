<?php
/**
 * Created by PhpStorm.
 * User: Dric
 * Date: 17/01/2016
 * Time: 10:18
 */

namespace Exceptions;


class InfoException extends oException{
	protected $isDebug = false;
	protected $messageType = "info";
}