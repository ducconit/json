<?php

namespace DNT\Json\Exceptions;

use Exception;

class PathMustBeFileException extends Exception
{
	protected $message = "Json: the path must be a file";
}
