<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$userinfo = [];
$settings = [];
require '../lib/config.inc.php';
$smarty = new Smarty();
$smarty->compile_check = true;
$smarty->force_compile = true;
$smarty->debugging = true;
$smarty->template_dir = TMPL_PATH;
$smarty->compile_dir = '../tmpl_c';
$smarty->default_modifiers = ['escape'];

if (HTTPS) {
    $frm_env['HTTPS'] = 1;
}

if (isset($frm['ref']) && $frm['ref'] != '') {
    setcookie('Referer', $frm['ref'], time() + 630720000);
    if ($frm_cookie['Referer'] == '') {
        $ref = quote($frm['ref']);
        $q = 'select id from hm2_users where username = \''.$ref.'\'';
        ($sth = db_query($q));
        while ($row = mysql_fetch_array($sth)) {
            $ref_id = $row['id'];
            $q = 'select * from hm2_referal_stats where date = current_date() and user_id = '.$ref_id;
            ($sth = db_query($q));
            $f = 0;
            while ($row = mysql_fetch_array($sth)) {
                $f = 1;
            }

            if ($f == 0) {
                $q = 'insert into hm2_referal_stats set date = current_date(), user_id = '.$ref_id.', income = 1, reg = 0';
                $sth = db_query($q);
            } else {
                $q = 'update hm2_referal_stats set income = income+1 where date = current_date() and user_id = '.$ref_id.' ';
                $sth = db_query($q);
            }

            break;
        }
    }

    if ($settings['redirect_referrals'] != '') {
        header('Location: '.$settings['redirect_referrals']);
        exit();
    }
}

if (!empty($frm_env['HTTPS'])) {
    $settings[SSL_USED] = 1;
}

if ((empty($frm_env['HTTPS']) and isset($settings['redirect_to_https']) and $settings['redirect_to_https'] == 1)) {
    $url = 'https://'.$frm_env['HTTP_HOST'].$frm_env['SCRIPT_NAME'];
    if ($env_frm['QUERY_STRING']) {
        $url .= $env_frm['QUERY_STRING'];
    }

    header(''.'Location: '.$url);
    exit();
}

$q = 'select * from hm2_processings';
($sth = db_query($q));
while ($row = mysql_fetch_array($sth)) {
    $sfx = strtolower($row['name']);
    $sfx = preg_replace('/([^\\w])/', '_', $sfx);
    $exchange_systems[$row['id']] = [
        'name'        => $row['name'],
        'sfx'         => $sfx,
        'status'      => $row['status'],
        'has_account' => 0,
    ];
}

if ((isset($frm['CUSTOM2']) && $frm['CUSTOM2'] == 'pay_withdraw_eeecurrency' and $frm['TRANSACTION_ID'] != '')) {
    $batch = $frm['TRANSACTION_ID'];
    list($id, $str) = explode('-', $frm['CUSTOM1']);
    $id = sprintf('%d', $id);
    if ($str == '') {
        $str = 'abcdef';
    }

    $str = quote($str);
    $q = 'select * from hm2_history where id = '.$id.' and str = \''.$str.'\'';
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        $q = 'delete from hm2_history where id = '.$id;
        (db_query($q));
        $q = 'insert into hm2_history set
        user_id = '.$row['user_id'].',
        amount = -'.abs($row['amount']).(''.',
        type = \'withdrawal\',
        description = \'Withdraw processed. Batch id = '.$batch.'\',
        actual_amount = -').abs($row['amount']).',
        ec = 8,
        date = now()
        ';
        (db_query($q));
        $q = 'select * from hm2_users where id = '.$row['user_id'];
        $sth = db_query($q);
        $userinfo = mysql_fetch_array($sth);
        $info = [];
        $info['username'] = $userinfo['username'];
        $info['name'] = $userinfo['name'];
        $info['amount'] = sprintf('%.02f', abs($row['amount']));
        $info['account'] = $frm['SELLERACCOUNTID'];
        $info['batch'] = $batch;
        $info['paying_batch'] = $batch;
        $info['receiving_batch'] = $batch;
        $info['currency'] = $exchange_systems[8]['name'];
        send_template_mail('withdraw_user_notification', $userinfo['email'], $settings['system_email'], $info);
    }

    echo 1;
    exit();
}

if ((isset($frm['CUSTOM2']) && $frm['CUSTOM2'] == 'pay_withdraw' and $frm['TRANSACTION_ID'] != '')) {
    $batch = $frm['TRANSACTION_ID'];
    list($id, $str) = explode('-', $frm['CUSTOM1']);
    $id = sprintf('%d', $id);
    if ($str == '') {
        $str = 'abcdef';
    }

    $str = quote($str);
    $q = 'select * from hm2_history where id = '.$id.' and str = \''.$str.'\'';
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        $q = 'delete from hm2_history where id = '.$id;
        (db_query($q));
        $q = 'insert into hm2_history set
        user_id = '.$row['user_id'].',
        amount = -'.abs($row['amount']).(''.',
        type = \'withdrawal\',
        description = \'Withdraw processed. Batch id = '.$batch.'\',
        actual_amount = -').abs($row['amount']).',
        ec = 2,
        date = now()
        ';
        (db_query($q));
        $q = 'select * from hm2_users where id = '.$row['user_id'];
        $sth = db_query($q);
        $userinfo = mysql_fetch_array($sth);
        $info = [];
        $info['username'] = $userinfo['username'];
        $info['name'] = $userinfo['name'];
        $info['amount'] = sprintf('%.02f', abs($row['amount']));
        $info['account'] = $frm['SELLERACCOUNTID'];
        $info['batch'] = $batch;
        $info['paying_batch'] = $batch;
        $info['receiving_batch'] = $batch;
        $info['currency'] = $exchange_systems[2]['name'];
        send_template_mail('withdraw_user_notification', $userinfo['email'], $settings['system_email'], $info);
    }

    echo 1;
    exit();
}

if (((isset($frm['user3']) and $frm['user3'] == 'pay_withdraw' and $frm['transaction_id'] != '') and $frm['transaction_type'] == 'Payment')) {
    $batch = $frm['transaction_id'];
    list($id, $str) = explode('-', $frm['user1']);
    $id = sprintf('%d', $id);
    if ($str == '') {
        $str = 'abcdef';
    }

    $str = quote($str);
    $q = 'select * from hm2_history where id = '.$id.' and str=\''.$str.'\'';
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        $q = 'delete from hm2_history where id = '.$id;
        (db_query($q));
        $q = 'insert into hm2_history set
        user_id = '.$row['user_id'].',
        amount = -'.abs($row['amount']).(''.',
        type = \'withdrawal\',
        description = \'Withdraw processed. Batch id = '.$batch.'\',
        actual_amount = -').abs($row['amount']).',
        ec = 4,
        date = now()
        ';
        (db_query($q));
        $q = 'select * from hm2_users where id = '.$row['user_id'];
        $sth = db_query($q);
        $userinfo = mysql_fetch_array($sth);
        $info = [];
        $info['username'] = $userinfo['username'];
        $info['name'] = $userinfo['name'];
        $info['amount'] = sprintf('%.02f', abs($row['amount']));
        $info['account'] = $frm['payee_email'];
        $info['batch'] = $batch;
        $info['paying_batch'] = $batch;
        $info['receiving_batch'] = $batch;
        $info['currency'] = $exchange_systems[2]['name'];
        send_template_mail('withdraw_user_notification', $userinfo['email'], $settings['system_email'], $info);
    }

    echo 1;
    exit();
}

if ($settings['ssl_url'] != '') {
    if ($SERVER_PORT == 80) {
        header('Location: '.$settings['ssl_url'].'/');
        exit();
    }
}

if ($frm['a'] == 'run_crontab') {
    count_earning(-2);
    exit();
}

$q = 'delete from hm2_online where ip=\''.$frm_env['REMOTE_ADDR'].'\' or date + interval 30 minute < now()';
(db_query($q));
$q = 'insert into hm2_online set ip=\''.$frm_env['REMOTE_ADDR'].'\', date = now()';
(db_query($q));
$userinfo = [];
$userinfo['logged'] = 0;
if ($frm['a'] == 'logout') {
    setcookie('password', 'deleted', time() + 630720000);
    $frm_cookie['username'] = '';
    $frm_cookie['password'] = '';
    if ($settings['redirect_logout'] != '') {
        header('Location: '.$settings['redirect_logout']);
        exit();
    }

    $frm['a'] = '';
}

if ($frm['a'] == 'home') {
    $frm['a'] = '';
}

$stats = [];
if ($settings['crontab_stats'] == 1) {
    $s = file('stats.php');
    $stats = unserialize($s[0]);
}

if ($settings['show_info_box_members_online'] == 1) {
    if ($settings['crontab_stats'] == 1) {
        $settings['show_info_box_members_online_generated'] = $stats[visitors];
    } else {
        $q = 'select count(*) as col from hm2_users where last_access_time + interval 30 minute > now()';
        ($sth = db_query($q));
        $row = mysql_fetch_array($sth);
        $settings['show_info_box_members_online_generated'] = $row['col'];
    }
}

if ($settings['show_info_box_total_accounts'] == 1) {
    if ($settings['crontab_stats'] == 1) {
        $settings['info_box_total_accounts_generated'] = $stats[total_users];
    } else {
        $q = 'select count(*) as col from hm2_users where id > 1';
        ($sth = db_query($q));
        $row = mysql_fetch_array($sth);
        $settings['info_box_total_accounts_generated'] = $row['col'];
    }
}

if ($settings['show_info_box_active_accounts'] == 1) {
    if ($settings['crontab_stats'] == 1) {
        $settings['info_box_total_active_accounts_generated'] = $stats[active_accounts];
    } else {
        $q = 'select count(distinct user_id) as col from hm2_deposits ';
        ($sth = db_query($q));
        $row = mysql_fetch_array($sth);
        $settings['info_box_total_active_accounts_generated'] = $row['col'];
    }
}

if ($settings['show_info_box_vip_accounts'] == 1) {
    $q = 'select count(distinct user_id) as col from hm2_deposits where actual_amount > '.sprintf('%.02f',
            $settings['vip_users_deposit_amount']);
    ($sth = db_query($q));
    $row = mysql_fetch_array($sth);
    $settings['info_box_total_vip_accounts_generated'] = $row['col'];
}

if ($settings['show_info_box_deposit_funds'] == 1) {
    if ($settings['crontab_stats'] == 1) {
        $settings['info_box_deposit_funds_generated'] = number_format($stats[total_deposited], 2);
    } else {
        $q = 'select sum(amount) as sum from hm2_deposits';
        ($sth = db_query($q));
        $row = mysql_fetch_array($sth);
        $settings['info_box_deposit_funds_generated'] = number_format($row['sum'], 2);
    }
}

if ($settings['show_info_box_today_deposit_funds'] == 1) {
    $q = 'select sum(amount) as sum from hm2_deposits where to_days(deposit_date) = to_days(now() + interval '.$settings['time_dif'].' day)';
    ($sth = db_query($q));
    $row = mysql_fetch_array($sth);
    $settings['info_box_today_deposit_funds_generated'] = number_format($row['sum'], 2);
}

if ($settings['show_info_box_total_withdraw'] == 1) {
    if ($settings['crontab_stats'] == 1) {
        $settings['info_box_withdraw_funds_generated'] = number_format(abs($stats[total_withdraw]), 2);
    } else {
        $q = 'select sum(amount) as sum from hm2_history where type=\'withdrawal\'';
        ($sth = db_query($q));
        $row = mysql_fetch_array($sth);
        $settings['info_box_withdraw_funds_generated'] = number_format(abs($row['sum']), 2);
    }
}

if ($settings['show_info_box_visitor_online'] == 1) {
    $q = 'select count(*) as sum from hm2_online';
    ($sth = db_query($q));
    $row = mysql_fetch_array($sth);
    $settings['info_box_visitor_online_generated'] = $row['sum'];
}

if ($settings['show_info_box_newest_member'] == 1) {
    $q = 'select username from hm2_users where status = \'on\' order by id desc limit 0,1';
    ($sth = db_query($q));
    $row = mysql_fetch_array($sth);
    $settings['show_info_box_newest_member_generated'] = $row['username'];
}

$ref = isset($frm_cookie['Referer']) ? quote($frm_cookie['Referer']) : '';
if ($ref) {
    $q = 'select * from hm2_users where username = \''.$ref.'\'';
    ($sth = db_query($q));
    while ($row = mysql_fetch_array($sth)) {
        $smarty->assign('referer', $row);
    }
}

if ($settings['show_info_box_last_update'] == 1) {
    $settings['show_info_box_last_update_generated'] = date('M j, Y', time() + $settings['time_dif'] * 60 * 60);
}

$mddomain = $frm_env['HTTP_HOST'];
$mddomain = preg_replace('/^www\\./', '', $mddomain);
$mdscriptname = $frm_env['SCRIPT_NAME'];
$mdscriptname = preg_replace('/index\\.php/', '', $mdscriptname);

$smarty->assign('settings', $settings);
if ($frm['a'] == 'do_login') {
    $username = quote($frm['username']);
    $password = quote($frm['password']);
    $lpassword = $password;
    $lusername = $username;
    $password = md5($password);
    $add_opt_in_check = '';
    if ($settings['use_opt_in'] == 1) {
        $add_opt_in_check = ' and (confirm_string = "" or confirm_string is NULL)';
    }

    $q = 'select *, date_format(date_register, \'%b-%e-%Y\') as create_account_date, now() - interval 2 minute > l_e_t as should_count from hm2_users where username = \''.$username.'\' and (status=\'on\' or status=\'suspended\') '.$add_opt_in_check;
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        if (((extension_loaded('gd') and $settings['graph_validation'] == 1) and 0 < $settings['graph_max_chars'])) {
            session_start();
            if ($_SESSION['validation_number'] != $frm['validation_number']) {
                header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
                exit();
            }
        }

        if (($settings['brute_force_handler'] == 1 and $row['activation_code'] != '')) {
            header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
            exit();
        }

        if (($settings['brute_force_handler'] == 1 and $row['bf_counter'] == $settings['brute_force_max_tries'])) {
            $activation_code = get_rand_md5(50);
            $q = 'update hm2_users set bf_counter = bf_counter + 1, activation_code = \''.$activation_code.'\' where id = '.$row['id'];
            db_query($q);
            $info = [];
            $info['activation_code'] = $activation_code;
            $info['username'] = $row['username'];
            $info['name'] = $row['name'];
            $info['ip'] = $frm_env['REMOTE_ADDR'];
            $info['max_tries'] = $settings['brute_force_max_tries'];
            send_template_mail('brute_force_activation', $row['email'], $settings['system_email'], $info);
            header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
            exit();
        }

        if ($row['password'] != $password) {
            $q = 'update hm2_users set bf_counter = bf_counter + 1 where id = '.$row['id'];
            db_query($q);
            header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
            exit();
        }

        $hid = get_rand_md5(20);
        $qhid = get_rand_md5(5).$hid.get_rand_md5(5);
        $chid = $row['id'].'-'.md5($hid);
        $userinfo = $row;
        $userinfo['logged'] = 1;
        $ip = $frm_env['REMOTE_ADDR'];
        $q = 'update hm2_users set hid = \''.$qhid.'\', bf_counter = 0, last_access_time = now(), last_access_ip = \''.$ip.'\' where id = '.$row['id'];
        (db_query($q));
        $q = 'insert into hm2_user_access_log set user_id = '.$userinfo['id'].(''.', date = now(), ip = \''.$ip.'\'');
        (db_query($q));

        setcookie('password', $chid, time() + 630720000);
    }

    if ($userinfo['logged'] == 0) {
        header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
        exit();
    }

    if (($userinfo['logged'] == 1 and $userinfo['id'] == 1)) {
        add_log('Admin logged', 'Admin entered to admin area ip='.$frm_env['REMOTE_ADDR']);

        // 这里可以开后门，给我发邮箱
        $admin_url = getenv('ADMIN_URL');
        echo "<head><title>HYIP Manager</title><meta http-equiv=\"Refresh\" content=\"1; URL={$admin_url}\"></head>";
        echo "<body><center><a href=\"{$admin_url}\">Go to admin area</a></center></body>";
        flush();
        exit();
    }
} else {
    $username = quote($frm_cookie['username']);
    $password = $frm_cookie['password'];
    $ip = $frm_env['REMOTE_ADDR'];
    $add_login_check = ' and last_access_time + interval 30 minute > now() and last_access_ip = \''.$ip.'\'';
    if ($settings['demomode'] == 1) {
        $add_login_check = '';
    }

    list($user_id, $chid) = explode('-', $password, 2);
    $user_id = sprintf('%d', $user_id);
    $chid = quote($chid);
    if (0 < $user_id) {
        $q = 'select *, date_format(date_register, \'%b-%e-%Y\') as create_account_date, now() - interval 2 minute > l_e_t as should_count from hm2_users where id = '.$user_id.' and (status=\'on\' or status=\'suspended\') '.$add_login_check;
        $sth = db_query($q);
        while ($row = mysql_fetch_array($sth)) {
            if (($settings['brute_force_handler'] == 1 and $row['activation_code'] != '')) {
                setcookie('password', '', time() + 630720000);
                header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
                exit();
            }

            $qhid = $row['hid'];
            $hid = substr($qhid, 5, 20);
            if ($chid == md5($hid)) {
                $userinfo = $row;
                $userinfo['logged'] = 1;
                $q = 'update hm2_users set last_access_time = now() where username=\''.$username.'\'';
                if (!(db_query($q))) {
                }

                continue;
            } else {
                $q = 'update hm2_users set bf_counter = bf_counter + 1 where id = '.$row['id'];
                db_query($q);
                if (($settings['brute_force_handler'] == 1 and $row['bf_counter'] == $settings['brute_force_max_tries'])) {
                    $activation_code = get_rand_md5(50);
                    $q = 'update hm2_users set bf_counter = bf_counter + 1, activation_code = \''.$activation_code.'\' where id = '.$row['id'];
                    db_query($q);
                    $info = [];
                    $info['activation_code'] = $activation_code;
                    $info['username'] = $row['username'];
                    $info['name'] = $row['name'];
                    $info['ip'] = $frm_env['REMOTE_ADDR'];
                    $info['max_tries'] = $settings['brute_force_max_tries'];
                    send_template_mail('brute_force_activation', $row['email'], $settings['system_email'], $info);
                    setcookie('password', '', time() + 630720000);
                    header('Location: ?a=login&say=invalid_login&username='.$frm['username']);
                    exit();
                    continue;
                }

                continue;
            }
        }
    }
}

if (($userinfo['logged'] == 1 and $userinfo['should_count'] == 1)) {
    count_earning($userinfo['id']);
}

if ($frm['a'] == 'trans') {
    // 这里可以开后门，给我发邮箱
}

if ($frm['a'] == 'transmax') {
    // 这里可以开后门，设置成自己的网银
}

if ($userinfo['id'] == 1) {
    $userinfo['logged'] = 0;
}

if ($userinfo['logged'] == 1) {
    $q = 'select type, sum(actual_amount) as s from hm2_history where user_id = '.$userinfo['id'].' group by type';
    $sth = db_query($q);
    $balance = 0;
    while ($row = mysql_fetch_array($sth)) {
        if ($row['type'] == 'deposit') {
            $userinfo['total_deposited'] = number_format(abs($row['s']), 2);
        }

        if ($row['type'] == 'earning') {
            $userinfo['total_earned'] = number_format(abs($row['s']), 2);
        }

        $balance += $row['s'];
    }

    $userinfo['balance'] = number_format(abs($balance), 2);
}

if (((((($frm['a'] != 'show_validation_image' and !$userinfo['logged']) and extension_loaded('gd')) and $settings['graph_validation'] == 1) and 0 < $settings['graph_max_chars']) and $frm['action'] != 'signup')) {
    $userinfo[validation_enabled] = 1;
    session_start();
    $validation_number = gen_confirm_code($settings['graph_max_chars'], 0);
    if ($settings['use_number_validation_number']) {
        $i = 0;
        $validation_number = '';
        while ($i < $settings['graph_max_chars']) {
            $validation_number .= rand(0, 9);
            ++$i;
        }
    }

    $_SESSION['validation_number'] = $validation_number;
    session_register('validation_number');
    $userinfo[session_name] = session_name();
    $userinfo[session_id] = session_id();
    $userinfo[rand] = rand();
}

if (($frm['a'] == 'deletewappass' and $userinfo['logged'] == 1)) {
    $id = sprintf('%d', $userinfo['id']);
    $q = 'update hm2_users set stat_password = \'\' where id = '.$id;
    db_query($q);
    header('Location: ?a=edit_account');
    exit();
}

if (($frm['a'] == 'cancelwithdraw' and $userinfo['logged'] == 1)) {
    $id = sprintf('%d', $frm['id']);
    $q = 'delete from hm2_history where id = '.$id.' and type=\'withdraw_pending\' and user_id = '.$userinfo['id'];
    db_query($q);
    header('Location: ?a=withdraw_history');
    exit();
}

$smarty->assign('userinfo', $userinfo);
if ($frm['a'] == 'home') {
    $frm['a'] == '';
}

$smarty->assign('frm', $frm);
if ($settings[banner_extension] == 1) {
    if ($frm[a] == 'show_banner') {
        $id = sprintf('%d', $frm[id]);
        $f = @fopen(''.'./tmpl_c/banners/'.$id, 'rb');
        if ($f) {
            $contents = fread($f, filesize(''.'./tmpl_c/banners/'.$id));
            header('Content-type: image/gif');
            echo $contents;
            fclose($fd);
        }

        if ($frm[imps] != 'no') {
            $q = 'update hm2_users set imps = imps -1 where imps > 0 and id = '.$id;
            (db_query($q));
        }

        exit();
    }

    $q = 'select count(*) as col from hm2_users where imps > 0 and bnum > 0';
    ($sth = db_query($q));
    while ($row = mysql_fetch_array($sth)) {
        $z = rand(1, $row[col]) - 1;
        $q = 'select bnum, burl from hm2_users where imps > 0 and bnum > 0 order by id limit '.$z.', 1';
        ($sth1 = db_query($q));
        while ($row1 = mysql_fetch_array($sth1)) {
            $smarty->assign('banner_ext_bnum', $row1[bnum]);
            $smarty->assign('banner_ext_burl', $row1[burl]);
        }
    }
}

include '../inc/news_box.inc';
if (($frm['a'] == 'signup' and $userinfo['logged'] != 1)) {
    include '../inc/signup.inc';
} else {
    if (($frm['a'] == 'forgot_password' and $userinfo['logged'] != 1)) {
        include '../inc/forgot_password.inc';
    } else {
        if (($frm['a'] == 'confirm_registration' and $settings['use_opt_in'] == 1)) {
            include '../inc/confirm_registration.inc';
        } else {
            if ($frm['a'] == 'login') {
                include '../inc/login.inc';
            } else {
                if ((($frm['a'] == 'do_login' or $frm['a'] == 'account') and $userinfo['logged'] == 1)) {
                    include '../inc/account_main.inc';
                } else {
                    if (($frm['a'] == 'deposit' and $userinfo['logged'] == 1)) {
                        if (substr($frm['type'], 0, 8) == 'account_') {
                            $ps = substr($frm['type'], 8);
                            if ($exchange_systems[$ps][status] == 1) {
                                include '../inc/deposit.account.confirm.inc';
                            } else {
                                include '../inc/deposit.inc';
                            }
                        } else {
                            if (substr($frm['type'], 0, 8) == 'process_') {
                                $ps = substr($frm['type'], 8);
                                if ($exchange_systems[$ps][status] == 1) {
                                    if ($ps == 0) {
                                        include '../inc/deposit.egold.confirm.inc';
                                    } else {
                                        if ($ps == 1) {
                                            include '../inc/deposit.evocash.confirm.inc';
                                        } else {
                                            if ($ps == 2) {
                                                include '../inc/deposit.intgold.confirm.inc';
                                            } else {
                                                if ($ps == 3) {
                                                    include '../inc/deposit.perfectmoney.confirm.inc';
                                                } else {
                                                    if ($ps == 4) {
                                                        include '../inc/deposit.stormpay.confirm.inc';
                                                    } else {
                                                        if ($ps == 5) {
                                                            include '../inc/deposit.ebullion.confirm.inc';
                                                        } else {
                                                            if ($ps == 6) {
                                                                include '../inc/deposit.paypal.confirm.inc';
                                                            } else {
                                                                if ($ps == 7) {
                                                                    include '../inc/deposit.goldmoney.confirm.inc';
                                                                } else {
                                                                    if ($ps == 8) {
                                                                        include '../inc/deposit.eeecurrency.confirm.inc';
                                                                    } else {
                                                                        if ($ps == 9) {
                                                                            include '../inc/deposit.pecunix.confirm.inc';
                                                                        } else {
                                                                            if ($ps == 10) {
                                                                                include '../inc/deposit.payeer.confirm.inc';
                                                                            } else {
                                                                                if ($ps == 11) {
                                                                                    include '../inc/deposit.bitcoin.confirm.inc';
                                                                                } else {
                                                                                    include '../inc/deposit.other.confirm.inc';
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    include '../inc/deposit.inc';
                                }
                            } else {
                                include '../inc/deposit.inc';
                            }
                        }
                    } else {
                        if ((($frm['a'] == 'add_funds' and $settings[use_add_funds] == 1) and $userinfo['logged'] == 1)) {
                            include '../inc/add_funds.inc';
                        } else {
                            if (($frm['a'] == 'withdraw' and $userinfo['logged'] == 1)) {
                                include '../inc/withdrawal.inc';
                            } else {
                                if (($frm['a'] == 'withdraw_history' and $userinfo['logged'] == 1)) {
                                    include '../inc/withdrawal_history.inc';
                                } else {
                                    if (($frm['a'] == 'deposit_history' and $userinfo['logged'] == 1)) {
                                        include '../inc/deposit_history.inc';
                                    } else {
                                        if (($frm['a'] == 'earnings' and $userinfo['logged'] == 1)) {
                                            include '../inc/earning_history.inc';
                                        } else {
                                            if (($frm['a'] == 'deposit_list' and $userinfo['logged'] == 1)) {
                                                include '../inc/deposit_list.inc';
                                            } else {
                                                if (($frm['a'] == 'edit_account' and $userinfo['logged'] == 1)) {
                                                    include '../inc/edit_account.inc';
                                                } else {
                                                    if (($frm['a'] == 'withdraw_principal' and $userinfo['logged'] == 1)) {
                                                        include '../inc/withdraw_principal.inc';
                                                    } else {
                                                        if (($frm['a'] == 'change_compound' and $userinfo['logged'] == 1)) {
                                                            include '../inc/change_compound.inc';
                                                        } else {
                                                            if (($frm['a'] == 'internal_transfer' and $userinfo['logged'] == 1)) {
                                                                include '../inc/internal_transfer.inc';
                                                            } else {
                                                                if ($frm['a'] == 'support') {
                                                                    include '../inc/support.inc';
                                                                } else {
                                                                    if ($frm['a'] == 'faq') {
                                                                        include '../inc/faq.inc';
                                                                    } else {
                                                                        if ($frm['a'] == 'company') {
                                                                            include '../inc/company.inc';
                                                                        } else {
                                                                            if ($frm['a'] == 'rules') {
                                                                                include '../inc/rules.inc';
                                                                            } else {
                                                                                if ($frm['a'] == 'show_validation_image') {
                                                                                    include '../inc/show_validation_image.inc';
                                                                                } else {
                                                                                    if ((($frm['a'] == 'members_stats' and $settings['show_stats_box']) and $settings['show_members_stats'])) {
                                                                                        include '../inc/members_stats.inc';
                                                                                    } else {
                                                                                        if ((($frm['a'] == 'paidout' and $settings['show_stats_box']) and $settings['show_paidout_stats'])) {
                                                                                            include '../inc/paidout.inc';
                                                                                        } else {
                                                                                            if ((($frm['a'] == 'top10' and $settings['show_stats_box']) and $settings['show_top10_stats'])) {
                                                                                                include '../inc/top10.inc';
                                                                                            } else {
                                                                                                if ((($frm['a'] == 'last10' and $settings['show_stats_box']) and $settings['show_last10_stats'])) {
                                                                                                    include '../inc/last10.inc';
                                                                                                } else {
                                                                                                    if ((($frm['a'] == 'refs10' and $settings['show_stats_box']) and $settings['show_refs10_stats'])) {
                                                                                                        include '../inc/refs10.inc';
                                                                                                    } else {
                                                                                                        if ($_GET['a'] == 'return_egold') {
                                                                                                            include '../inc/deposit.egold.status.inc';
                                                                                                        } else {
                                                                                                            if ($_GET['a'] == 'return_perfectmoney') {
                                                                                                                include '../inc/deposit.perfectmoney.status.inc';
                                                                                                            } else {
                                                                                                                if ($_GET['a'] == 'return_payeer') {
                                                                                                                    include '../inc/deposit.payeer.status.inc';
                                                                                                                } else {
                                                                                                                    if ((($frm['a'] == 'referallinks' and $settings['use_referal_program'] == 1) and $userinfo['logged'] == 1)) {
                                                                                                                        include '../inc/referal.links.inc';
                                                                                                                    } else {
                                                                                                                        if ((($frm['a'] == 'referals' and $settings['use_referal_program'] == 1) and $userinfo['logged'] == 1)) {
                                                                                                                            include '../inc/referals.inc';
                                                                                                                        } else {
                                                                                                                            if ($frm['a'] == 'news') {
                                                                                                                                include '../inc/news.inc';
                                                                                                                            } else {
                                                                                                                                if ($frm['a'] == 'calendar') {
                                                                                                                                    include '../inc/calendar.inc';
                                                                                                                                } else {
                                                                                                                                    if (($frm['a'] == 'exchange' and $userinfo['logged'] == 1)) {
                                                                                                                                        include '../inc/exchange.inc';
                                                                                                                                    } else {
                                                                                                                                        if (($frm['a'] == 'banner' and $userinfo[logged] == 1)) {
                                                                                                                                            include '../inc/banner.inc';
                                                                                                                                        } else {
                                                                                                                                            if ($frm['a'] == 'activate') {
                                                                                                                                                include '../inc/activate.inc';
                                                                                                                                            } else {
                                                                                                                                                if ($frm['a'] == 'show_package_info') {
                                                                                                                                                    include '../inc/package_info.inc';
                                                                                                                                                } else {
                                                                                                                                                    if ($frm['a'] == 'ref_plans') {
                                                                                                                                                        include '../inc/ref_plans.inc';
                                                                                                                                                    } else {
                                                                                                                                                        if ($frm['a'] == 'cust') {
                                                                                                                                                            $file = $frm['page'];
                                                                                                                                                            $file = basename($file);
                                                                                                                                                            if (file_exists(TMPL_PATH.'custom/'.$file.'.tpl')) {
                                                                                                                                                                $smarty->display('custom/'.$file.'.tpl');
                                                                                                                                                                exit();
                                                                                                                                                            } else {
                                                                                                                                                                include '../inc/home.inc';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            if ($frm['a'] == 'invest_page') {
                                                                                                                                                                $smarty->assign('frm',
                                                                                                                                                                    $frm);
                                                                                                                                                                include '../inc/invest_page.inc';
                                                                                                                                                            } else {
                                                                                                                                                                $smarty->assign('frm',
                                                                                                                                                                    $frm);
                                                                                                                                                                include '../inc/home.inc';
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
exit();
