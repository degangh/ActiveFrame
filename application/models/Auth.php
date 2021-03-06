<?php

class Auth
{
    public function isLogin()
    {
        if (!$_SESSION['user']) throw new AuthException(AuthException::NOT_LOGIN);
    }

    public function login($username, $password)
    {
        loadModel('User');

        $user = new User;

        if (!$user->has($username, $password)) throw new AuthException(AuthException::LOGIN_ERROR);
        else 
        {
            $_SESSION['user']['username'] = $user->username;
            //$_SESSION['user']['roles'] = $user->roles;
        }
    }

    public function logout()
    {
        session_destroy();
        redirect('login');
    }
}