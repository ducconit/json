<?php

namespace DNT\Json\Exceptions;

use Exception;

class PathNotEmptyException extends Exception
{
	protected $message = "Json: path does not empty";
}
