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
define('SUBDOMAIN', !empty($_SERVER['SUBDOMAIN']) ? $_SERVER['SUBDOMAIN'] : '');
if (SUBDOMAIN && is_dir(APP_PATH.'/templates/'.SUBDOMAIN.'/tmpl/')) {
    define('TMPL_PATH', APP_PATH.'/templates/'.SUBDOMAIN.'/tmpl/');
} else {
    define('TMPL_PATH', __DIR__.'/tmpl/');
}

if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    define('HTTPS', true);
} else {
    define('HTTPS', false);
}
