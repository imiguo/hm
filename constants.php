<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

define('APP_PATH', __DIR__);

define('THEME', $_SERVER['THEME'] ?? env('THEME') ?: 'default');

define('TMPL_PATH', dirname(APP_PATH).'/templates/'.THEME.'/tmpl/');

foreach (glob(dirname(TMPL_PATH).'/public/*') as $file) {
    $target = APP_PATH.'/public/'.basename($file);
    if (!is_link($target)) {
        symlink($file, $target);
    } elseif (false === strpos(readlink($target), THEME.'/public/')) {
        unlink($target);
        symlink($file, $target);
    }
}

if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    define('HTTPS', true);
} else {
    define('HTTPS', false);
}
