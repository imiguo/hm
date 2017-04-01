<?php
class Flash
{
    public function __construct()
    {
        session_start();
    }
    
    public function get($key)
    {
        $value = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $value;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function setget($key, $value)
    {
        $_SESSION[$key] = $value;
        return $value;
    }
}
