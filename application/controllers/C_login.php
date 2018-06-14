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
}
?>