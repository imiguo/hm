<?php

class Mysql
{
    private $mysqli;
    private static $instance;

    private function __construct()
    {
        $this->mysqli = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __call(string $function_name, array $arguments)
    {
        return call_user_func_array([$this->mysqli, $function_name], $arguments);
    }
}