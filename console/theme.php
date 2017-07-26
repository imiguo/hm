<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$theme = '';
if (!isset($argv)) {
    exit;
}
if (isset($argv[2])) {
    $theme = $argv[2];
}
$theme_list = [
    'default',
    'dollarup',
    'enjoymoney',
    'fundsmore',
    'gainmost',
    'logunion',
    'yourshares',
    'current',
    'clear',
];
if (!in_array($theme, $theme_list)) {
    echo "\n";
    echo "参数列表： \n";
    echo "====================================== \n";
    print_r($theme_list);
    exit;
}

foreach (glob('./public/*') as $file) {
    if (is_link($file)) {
        unlink($file);
    }
}
foreach (glob('./*') as $file) {
    if (is_link($file)) {
        unlink($file);
    }
}
if ('clear' == $theme) {
    exit;
}
foreach (glob('../templates/'.$theme.'/*') as $file) {
    if (strpos($file, '/tmpl')) {
        symlink(realpath($file), './'.basename($file));
    } else {
        symlink(realpath($file), './public/'.basename($file));
    }
}
