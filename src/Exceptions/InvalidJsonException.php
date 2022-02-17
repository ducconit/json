<?php

namespace DNT\Json\Exceptions;

use Exception;

class InvalidJsonException extends Exception
{
	protected $message = "Json: invalid value in json";
}
