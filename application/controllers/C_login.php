<?php
class C_login extends CController
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		/*--
		Load your lib or model to process reuqest
		--*/
		loadview('login/index',$data);
	}

	public function check()
	{
		if (!$_POST) throw new Exception('Request cannot be fulfilled');

		$username = str_replace(' ' , '', $_POST['username']);
		$password = str_replace(' ' , '', $_POST['password']);

		$this->auth->login($username, $password);

		redirect('welcome');
		

	}
}
?>