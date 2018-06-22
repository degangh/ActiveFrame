<?php
class C_task extends CController
{
	function __construct()
	{
		parent::__construct();
		$this->auth->isLogin();
	}
	
	public function index()
	{
		/*--
		Load your lib or model to process reuqest
		--*/
		loadview('task/index',$data);
    }
    
    public function save()
    {
        if (!$_POST) throw new Exception ("no post");

        else
        {
            var_dump($_POST);
        }
    }
}
?>