<?php

class AuthException extends Exception
{
    public $errorCode;

	const LOGIN_ERROR=1;
	const ACCESS_DENY=2;

	
	function __construct($errorCode)
	{
		$this->errorCode=$errorCode;
	}

    public function handleException()
    {
        redirect('login');
    }
}