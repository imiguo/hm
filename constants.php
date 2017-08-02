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

define('TMPL_PATH', dirname(APP_PATH).'/templates/'.THEME.'/tmpl');

// todo: 得到子域名
define('SUBDOMAIN', '');

define('CACHE_PATH', APP_PATH.'/tmpl_c');

if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    define('HTTPS', true);
} else {
    define('HTTPS', false);
}
