<?php

namespace DNT\Json\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
	protected $message = "Json: File does not exist";
}
