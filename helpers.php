<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Illuminate\Container\Container;

if (!function_exists('app')) {
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

if (!function_exists('mysql_query')) {
    function mysql_query($query)
    {
        return app('mysql')->query($query);
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result)
    {
        return $result->fetch_array();
    }
}

if (!function_exists('mysql_fetch_assoc')) {
    function mysql_fetch_assoc($result)
    {
        return $result->fetch_assoc();
    }
}

if (!function_exists('mysql_insert_id')) {
    function mysql_insert_id()
    {
        return app('mysql')->insert_id;
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($escapestr)
    {
        return app('mysql')->real_escape_string($escapestr);
    }
}

if (function_exists('dd') ) {
    // function shutdown_print_error()
    // {
    //     $error = error_get_last();
    //     $error && dd($error);
    // }
    // register_shutdown_function('shutdown_print_error');
}

if (!function_exists('theme_list')) {
    function theme_list()
    {
        $themes = [];
        foreach (glob(APP_PATH.'/../templates/*') as $file) {
            $themes[] = basename($file);
        }
        return $themes;
    }
}

if (!function_exists('old_theme')) {
    function old_theme()
    {
        $cacheThemeFile = CACHE_PATH.'/theme';
        if (is_file($cacheThemeFile)) {
            return file_get_contents($cacheThemeFile);
        }
        return false;
    }
}

if (!function_exists('theme')) {
    function theme($theme)
    {
        if ($theme == 'random') {
            return array_rand(array_flip(theme_list()));
        }
        if ($theme == 'next') {
            $themes = theme_list();
            if ($oldTheme = old_theme()) {
                try {
                    return $themes[(array_flip($themes)[$oldTheme] + 1) % count($themes)];
                } catch (Exception $e) {

                }
            }
            return current($themes);
        }
        return $theme ?: 'default';
    }
}
