<?php
class C_welcome extends CController
{
	function __construct()
	{
		parent::__construct();
		loadModel("Task");
		loadException('AuthException');
	}
	
	public function index()
	{
		/*--
		Load your lib or model to process reuqest
		--*/
		loadview('welcome',$data);
	}

	public function test()
	{
		var_dump($_SESSION);
	}

	public function testException()
	{
		if (!$_GET['check']) throw new AuthException("oops");
	}
}
?>