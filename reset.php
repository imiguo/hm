<?php
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

if ('entimm' != $frm['en']) exit;

if ('init_db' == $frm['a']) {
    $mysqli = new mysqli($settings['hostname'], $settings['db_login'], $settings['db_pass'], $settings['database']);
    $sql = file_get_contents('database/db.sql');
    $mysqli->multi_query($sql) OR die(1);
}

$settings['site_url'] = (is_SSL() ? 'https://' : 'http://').$settings['site_name'].preg_replace('/\\/reset.php/', '', $_SERVER['SCRIPT_NAME']);
$settings['site_url_alt'] = (is_SSL() ? 'https://' : 'http://').$settings['site_name'];

$mddomain = $settings['site_name'];
$mddomain = preg_replace('/^www\\./', '', $mddomain);
$mdscriptname = $_SERVER['SCRIPT_NAME'];
$mdscriptname = preg_replace('/reset\\.php/', '', $mdscriptname);
$settings['key'] = strtoupper(get_rand_md5(100).md5($mddomain.'asdfds89ufsdkfnsjfdksh').md5($mdscriptname.'8hbfnbdnf').md5('grv'.$mddomain).get_rand_md5(200));
save_settings();

define('THE_GC_SCRIPT_V2005_04_01', 'answer');
$acsent_settings = [
    'detect_ip' => 'disabled',
    'detect_browser' => 'disabled',
    'email' => $email,
    'last_browser' => $email,
    'last_ip' => $email,
    'pin' => '',
    'timestamp' => 0,
];

db_open();
set_accsent();

echo 'ok';