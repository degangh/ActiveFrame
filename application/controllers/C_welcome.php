<?php
class C_welcome extends CController
{
	function __construct()
	{
		parent::__construct();
		loadModel("Task");
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
		$m = new Task;

		$m->name = "no body";
		$m->save();

		dd($m);
	}
}
?>