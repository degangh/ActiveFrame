<?php

class AuthException extends Exception
{
    public $errorCode;

	const LOGIN_ERROR=1;
	const ACCESS_DENY=2;
	const NOT_LOGIN = 3;

	
	function __construct($errorCode)
	{
		$this->errorCode=$errorCode;
	}

    public function handleException()
    {
		switch ($this->errorCode)
		{
			case self::LOGIN_ERROR:
			Session::flash('error', "Username/Password Error");
			redirect('login');
			break;

			case self::NOT_LOGIN:
			Session::flash('error', "Requested page requires login information");
			redirect('login');
			break;

			case self::ACCESS_DENY:
			Session::flash('error', "Requested page is NOT allowed");
			redirect('login');
			break;
			
			default: 
			Session::flash('error', 'Unknow Error');
			redirect('login');
			break;

			
		}
    }
}