<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
