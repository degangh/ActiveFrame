<?php

class C_logout extends CController
{
    public function index()
    {
        $this->auth->logout();
    }
}