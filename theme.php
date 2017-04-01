<?php
$theme = '';
if (! isset($argv)) exit;
if (isset($argv[1])) {
  $theme = $argv[1];
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
if (! in_array($theme, $theme_list)) {
    echo "\n";
    echo "参数列表： \n";
    echo "====================================== \n";
    print_r($theme_list);
    exit;
}

foreach(glob("./*") as $file)  {
    if(is_link($file)) {
        unlink($file);
    }
}
if ('clear' == $theme) {
    exit;
}
foreach (glob('../templates/' . $theme . '/*') as $file) {
    symlink($file, './' . basename($file));
}
