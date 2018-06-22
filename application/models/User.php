<?php

class User extends CModel
{
    

    public function has($username, $password)
    {
        $s = new CSearcher($this->table);
        $s['username'] = $username;
        $s['password'] = md5($password);

        $user = $s->fetchResult();

        if (count($user) > 0 ) 
        {
            foreach ($user[0] as $k => $v) $this->data[$k] = $v;

            return $this;
        }

        else return false;
    }

}