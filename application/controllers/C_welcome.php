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
		$m = new Task(22);



		dd($m);

		$m->is_completed = 0;
		$m->save();

		dd ($m);
	}

	public function testException()
	{
		if (!$_GET['check']) throw new AuthException("oops");
	}
}
?>