<?php

require '../common/function.php';

if (!validate()) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Invalid request.';
    exit;
}

include 'lib/config.inc.php';
include '../env/data.php';

$settings = get_settings();
if (ENV == 'local') {
    $settings = array_merge($settings, $local);
} elseif (ENV == 'test') {
    $settings = array_merge($settings, $test);
} else {
    $settings = array_merge($settings, $prod);
}

if ('init_db' == $frm['a']) {
    $mysqli = new mysqli($settings['hostname'], $settings['db_login'], $settings['db_pass'], $settings['database']);
    $sql = file_get_contents('database/db.sql');
    $mysqli->multi_query($sql) or die(1);
}

$domain = $_SERVER['HTTP_HOST'];
$domain = preg_replace('/^www\\./', '', $domain);

$scriptname = $_SERVER['SCRIPT_NAME'];
$scriptname = preg_replace('/reset\\.php/', '', $scriptname);
$settings['key'] = strtoupper(get_rand_md5(100).md5($domain.'asdfds89ufsdkfnsjfdksh').md5($scriptname.'8hbfnbdnf').md5('grv'.$domain).get_rand_md5(200));
save_settings();

define('THE_GC_SCRIPT_V2005_04_01', 'answer');
$acsent_settings = [
    'detect_ip'      => 'disabled',
    'detect_browser' => 'disabled',
    'email'          => $email,
    'last_browser'   => '',
    'last_ip'        => '',
    'pin'            => '',
    'timestamp'      => 0,
];

db_open();
set_accsent();

echo 'ok';
