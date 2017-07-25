<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __call(string $function_name, array $arguments)
    {
        return call_user_func_array([$this->mysqli, $function_name], $arguments);
    }
}
