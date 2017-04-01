<?php
if (file_exists('install.php')) {
    print 'Delete install.php file for security reason please!';
    exit ();
}

require 'lib/config.inc.php';
require '../smarty/Smarty.class.php';
$smarty = new Smarty ();
$smarty->compile_check = true;
$smarty->template_dir = TMPL_PATH;
$smarty->compile_dir = './tmpl_c';

if ($settings['accesswap'] == 0) {
    exit ();
}

$dbconn = db_open();
if ( ! $dbconn) {
    print 'Cannot connect mysql';
    exit ();
}

check_if_stolen();
$userinfo = [];
$userinfo['logged'] = 0;
$q = 'delete from hm2_online where ip=\''.$frm_env['REMOTE_ADDR'].'\' or date + interval 30 minute < now()';
(db_query($q) OR print mysql_error());
$q = 'insert into hm2_online set ip=\''.$frm_env['REMOTE_ADDR'].'\', date = now()';
(db_query($q) OR print mysql_error());
if ($frm['a'] == 'logout') {
    setcookie('username', '', time() + 630720000);
    setcookie('password', '', time() + 630720000);
    $frm_cookie['username'] = '';
    $frm_cookie['password'] = '';
}

$smarty->assign('settings', $settings);
if ($frm['a'] == 'do_login') {
    $username = quote($frm['username']);
    $password = quote($frm['password']);
    $password = md5($password);
    $add_opt_in_check = '';
    if ($settings['use_opt_in'] == 1) {
        $add_opt_in_check = ' and (confirm_string = "" or confirm_string is NULL)';
    }

    $q = 'select *, date_format(date_register + interval '.$settings['time_dif'].(''.' hour, \'%b-%e-%Y\') as create_account_date from hm2_users where username = \''.$username.'\' and stat_password = \''.$password.'\' and stat_password <> \'\' and (status=\'on\' or status=\'suspended\') '.$add_opt_in_check);
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        $userinfo = $row;
        $userinfo['logged'] = 1;
    }

    if ($userinfo['logged'] == 0) {
        header('Location: wap.php?a=login&say=invalid_login&username='.$frm['username']);
        db_close($dbconn);
        exit ();
    } else {
        $ip = $frm_env['REMOTE_ADDR'];
        $q = 'insert into hm2_user_access_log set user_id = '.$userinfo['id'].(''.',
  	date = now(), ip = \''.$ip.'\'');
        if ( ! (db_query($q))) {
            exit (mysql_error());;
        }

        setcookie('username', $frm['username'], time() + 630720000);
        setcookie('password', md5($frm['password']), time() + 630720000);
        $ip = $frm_env['REMOTE_ADDR'];
        $q = ''.'update hm2_users set last_access_time = now(), last_access_ip = \''.$ip.'\' where username=\''.$username.'\'';
        if ( ! (db_query($q))) {
            exit (mysql_error());;
        }
    }

    if (($userinfo['logged'] == 1 AND $userinfo['id'] == 1)) {
        setcookie('username', $frm['username'], time() + 630720000);
        setcookie('password', md5($frm['password']), time() + 630720000);
        header('Location: wap.php?ok');
        db_close($dbconn);
        exit ();
    }
} else {
    $username = quote($frm_cookie['username']);
    $password = $frm_cookie['password'];
    $ip = $frm_env['REMOTE_ADDR'];
    $add_login_check = ''.' and last_access_time + interval 30 minute > now() and last_access_ip = \''.$ip.'\'';
    if ($settings['demomode'] == 1) {
        $add_login_check = '';
    }

    $q = 'select *, date_format(date_register + interval '.$settings['time_dif'].(''.' hour, \'%b-%e-%Y\') as create_account_date from hm2_users where username = \''.$username.'\' and (status=\'on\' or status=\'suspended\') '.$add_login_check);
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        if ($password == $row['stat_password']) {
            $userinfo = $row;
            $userinfo['logged'] = 1;
            $q = ''.'update hm2_users set last_access_time = now() where username=\''.$username.'\'';
            if ( ! (db_query($q))) {
                exit (mysql_error());;
            }

            continue;
        }
    }
}

if ($userinfo['logged'] == 1) {
    count_earning($userinfo['id']);
}

$smarty->assign('userinfo', $userinfo);
if ($frm['a'] == 'login') {
    include 'inc/wap/login.inc';
} else {
    if ((($frm['a'] == 'do_login' OR $frm['a'] == 'account') AND $userinfo['logged'] == 1)) {
        include 'inc/wap/account_main.inc';
    } else {
        if (($frm['a'] == 'earnings' AND $userinfo['logged'] == 1)) {
            include 'inc/wap/earning_history.inc';
        } else {
            if (($frm['a'] == 'admin_pending' AND $userinfo['id'] == 1)) {
                include 'inc/admin/wap/pending.inc.php';
            } else {
                if ($userinfo['id'] == 1) {
                    include 'inc/admin/wap/main.inc.php';
                    db_close($dbconn);
                    exit ();
                } else {
                    include 'inc/wap/home.inc';
                    db_close($dbconn);
                    exit ();
                }
            }
        }
    }
}

db_close($dbconn);
exit ();
