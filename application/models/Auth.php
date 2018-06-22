<?php

class Auth
{
    public function isLogin()
    {

    }

    public function login($username, $password)
    {
        loadException('AuthException');
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
        
    }
}