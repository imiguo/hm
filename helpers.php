<?php

use Illuminate\Container\Container;

if (! function_exists('app')) {
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return empty($parameters)
            ? Container::getInstance()->make($abstract)
            : Container::getInstance()->makeWith($abstract, $parameters);
    }
}

if (! function_exists('mysql_query')) {
    function mysql_query($query)
    {
        return app('mysql')->query($query);
    }
}

if (! function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result)
    {
        return $result->fetch_array();
    }
}

if (! function_exists('mysql_fetch_assoc')) {
    function mysql_fetch_assoc($result)
    {
        return $result->fetch_assoc();
    }
}

if (! function_exists('mysql_insert_id')) {
    function mysql_insert_id()
    {
        return app('mysql')->insert_id;
    }
}

if (! function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($escapestr)
    {
        return app('mysql')->real_escape_string($escapestr);
    }
}
