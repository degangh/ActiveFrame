<?
class Session
{
    
    private static $timeToRefresh = 600;
    
    
    
    public static function start()
    {
        session_name(APP_SESS_NAME);
        // Set session cookie options, tbd
		//session_set_cookie_params($limit, $path, $domain, $https, true);
        session_start();
        if (!self::has('_token')) self::set('_token', bin2hex(openssl_random_pseudo_bytes(32)));
    }
    public static function flash($key, $value)
    {
        if (isset($key)) 
        {
            $_SESSION[$key]  = $value;
            $_SESSION['keyList'][] = $key;
        }
    }
    public static function get($key)
    {
        
        //refresh session periodically
        if (isset($_SESSION['last_touch']) && (time() - $_SESSION['last_touch']) > self::$timeToRefresh) self::refresh();
        $_SESSION['last_touch'] = time();
        
        $value = $_SESSION[$key];
        if (isset($_SESSION['keyList']) && is_array($_SESSION['keyList']) && in_array($key, $_SESSION['keyList']))
        {
            unset ($_SESSION[$key]);
            foreach ($_SESSION['keyList'] as $k => $v) if ($v == $key) unset($_SESSION['keyList'][$k]);
        }
        return $value;
    }
    public static function clear()
    {
    }
    public static function dump()
    {
        echo "<pre>";
        var_dump($_SESSION);
        echo "</pre>";
    }
    public static function refresh()
    {
        $session = array();
        foreach ($_SESSION as $k => $v) $session[$k] = $v;
        echo "old id: " . session_id();
        session_destroy();
        session_id(bin2hex(openssl_random_pseudo_bytes(16)));
        self::start();
        echo "new id: " . session_id();
        foreach ($session as $k => $v) $_SESSION[$k] = $v;
    }
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    private static function _age ()
    {
    }
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function csrf()
    {
        if (self::has('_token')) return self::get('_token');
    }
}

?>