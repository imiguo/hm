<?php
if ($frm['action'] == 'confirm') {
    $smarty->assign('say', $frm['say']);
    $smarty->display('edit_account_confirmation.tpl');
    db_close($dbconn);
    exit ();
}

if ($frm['action'] == 'edit_account') {
    if (($settings['demomode'] == 1 AND $userinfo['id'] < 3)) {
        header('Location: ?a=edit_account');
        exit ();
    }

    $errors = [];
    if ($frm['action2'] != 'confirm') {
        if ($frm['fullname'] == '') {
            array_push($errors, 'full_name');
        }

        if ($frm['password'] != '') {
            if (0 < $settings['min_user_password_length']) {
                if (strlen($frm['password']) < $settings['min_user_password_length']) {
                    array_push($errors, 'password_too_small');
                }
            }

            if ($frm['password'] != $frm['password2']) {
                array_push($errors, 'password_confirm');
            }
        }

        if (($settings['usercanchangeemail'] AND $frm['email'] == '')) {
            array_push($errors, 'email');
        }

        if ($settings['use_user_location']) {
            if ($frm['address'] == '') {
                array_push($errors, 'address');
            }

            if ($frm['city'] == '') {
                array_push($errors, 'city');
            }

            if ($frm['state'] == '') {
                array_push($errors, 'state');
            }

            if ($frm['zip'] == '') {
                array_push($errors, 'zip');
            }

            if ($frm['zip'] == '') {
                array_push($errors, 'country');
            }
        }

        if ($settings['use_transaction_code']) {
            if ($frm['transaction_code'] != '') {
                if ($frm['transaction_code_current'] == $userinfo['transaction_code']) {
                    if (0 < $settings['min_user_password_length']) {
                        if (strlen($frm['transaction_code']) < $settings['min_user_password_length']) {
                            array_push($errors, 'transaction_code_too_small');
                        }
                    }

                    if ($frm['transaction_code'] != $frm['transaction_code2']) {
                        array_push($errors, 'transaction_code_confirm');
                    }
                } else {
                    array_push($errors, 'invalid_transaction_code');
                }
            }

            if ((($frm['transaction_code'] != '' AND $frm['password'] != '') AND $frm['transaction_code'] == $frm['password'])) {
                array_push($errors, 'transaction_code_vs_password');
            }
        }
    }

    if (sizeof($errors) == 0) {
        if ($settings['account_update_confirmation'] == 1) {
            session_start();
            if ($frm['action2'] == 'confirm') {
                if ($_SESSION['account_update_confirmation_code'] == $frm['account_update_confirmation_code']) {
                    if (is_array($_SESSION['fields'])) {
                        $frm = array_merge($frm, $_SESSION['fields']);
                    } else {
                        header('Location: ?a=edit_account');
                        db_close($dbconn);
                        exit ();
                    }
                } else {
                    header('Location: ?a=edit_account&action=confirm&say=invalid_code');
                    db_close($dbconn);
                    exit ();
                }
            } else {
                $code = get_rand_md5(50);
                $_SESSION['account_update_confirmation_code'] = $code;
                $_SESSION['fields'] = $frm;
                $info = [];
                $info['confirmation_code'] = $code;
                $info['username'] = $userinfo['username'];
                $info['name'] = $userinfo['name'];
                $info['ip'] = $frm_env['REMOTE_ADDR'];
                send_template_mail('account_update_confirmation', $userinfo['email'], $settings['system_email'], $info);
                header('Location: ?a=edit_account&action=confirm');
                db_close($dbconn);
                exit ();
            }
        }

        $fullname = quote($frm['fullname']);
        $password = quote($frm['password']);
        $enc_password = md5($password);
        $egold = quote($frm['egold_account']);
        $perfectmoney = quote($frm['perfectmoney_account']);
        $email = quote($frm['email']);
        $evocash = quote($frm['evocash_account']);
        $intgold = quote($frm['intgold_account']);
        $stormpay = quote($frm['stormpay_account']);
        $ebullion = quote($frm['ebullion_account']);
        $paypal = quote($frm['paypal_account']);
        $goldmoney = quote($frm['goldmoney_account']);
        $eeecurrency = quote($frm['eeecurrency_account']);
        $pecunix = quote($frm['pecunix_account']);
        $address = quote($frm['address']);
        $city = quote($frm['city']);
        $state = quote($frm['state']);
        $zip = quote($frm['zip']);
        $country = quote($frm['country']);
        $transaction_code = quote($frm['transaction_code']);
        if (($userinfo['email'] != $frm['email'] AND $settings['usercanchangeemail'] == 1)) {
            $q = ''.'update hm2_users set email = \''.$email.'\' where id > 1 and id = '.$userinfo['id'];
            (db_query($q) OR print mysql_error());
        }

        if (($userinfo['egold_account'] != $frm['egold_account'] AND $settings['usercanchangeegoldacc'])) {
            $q = ''.'update hm2_users set egold_account = \''.$egold.'\' where id > 1 and id = '.$userinfo['id'];
            (db_query($q) OR print mysql_error());
        }

        if (($userinfo['perfectmoney_account'] != $frm['perfectmoney_account'] AND $settings['usercanchangeperfectmoneyacc'])) {
            $q = ''.'update hm2_users set perfectmoney_account = \''.$perfectmoney.'\' where id > 1 and id = '.$userinfo['id'];
            (db_query($q) OR print mysql_error());
        }

        if ($frm['password'] != '') {
            $q = ''.'update hm2_users set password = \''.$enc_password.'\' where id > 1 and id = '.$userinfo['id'];
            (db_query($q) OR print mysql_error());
            if ($settings['store_uncrypted_password'] == 1) {
                $pswd = quote($frm['password']);
                $q = ''.'update hm2_users set pswd = \''.$pswd.'\' where id > 1 and id = '.$userinfo['id'];
                (db_query($q) OR print mysql_error());
            }
        }

        if ($frm['transaction_code'] != '') {
            $q = ''.'update hm2_users set transaction_code = \''.$transaction_code.'\' where id > 1 and id = '.$userinfo['id'];
            (db_query($q) OR print mysql_error());
        }

        if ($frm['wappassword'] != '') {
            $enc_password = quote(md5($frm['wappassword']));
            $q = ''.'update hm2_users set stat_password = \''.$enc_password.'\' where id > 1 and id = '.$userinfo['id'];
            (db_query($q) OR print mysql_error());
        }

        $edit_location = '';
        if ($settings['use_user_location']) {
            $edit_location = ''.'address = \''.$address.'\',
                          city = \''.$city.'\',
                          state = \''.$state.'\',
                          zip = \''.$zip.'\',
                          country = \''.$country.'\',
                         ';
        }

        $user_auto_pay_earning = sprintf('%d', $frm['user_auto_pay_earning']);
        $q = ''.'update hm2_users set name = \''.$fullname.'\', 
                                 '.$edit_location.'
    	                         evocash_account = \''.$evocash.'\', 
                                 intgold_account = \''.$intgold.'\',
				 stormpay_account = \''.$stormpay.'\',
				 ebullion_account = \''.$ebullion.'\',
				 paypal_account = \''.$paypal.'\',
				 goldmoney_account = \''.$goldmoney.'\',
				 eeecurrency_account = \''.$eeecurrency.'\',
                                 user_auto_pay_earning = '.$user_auto_pay_earning.'
            where id > 1 and id = '.$userinfo['id'];
        (db_query($q) OR print mysql_error());
        $q = 'select * from hm2_users where id ='.$userinfo['id'];
        $sth = db_query($q);
        $userinfo = mysql_fetch_array($sth);
        $userinfo['logged'] = 1;

        // 发邮件通知
        if ($settings['sendnotify_when_userinfo_changed'] == 1) {
            $info = [];
            $info['username'] = $userinfo['username'];
            $info['password'] = $password;
            $info['name'] = $userinfo['name'];
            $info['ip'] = $frm_env['REMOTE_ADDR'];
            $info['egold'] = $userinfo['egold_account'];
            $info['perfectmoney'] = $userinfo['perfectmoney_account'];
            $info['email'] = $userinfo['email'];
            if ($frm['email'] == '') {
                $frm['email'] = $userinfo['email'];
            }

            send_template_mail('change_account', $frm['email'], $settings['opt_in_email'], $info);
        }

        header('Location: ?a=edit_account&say=changed');
        exit ();
    }
}

include './inc/countries.inc';
$q = 'select date_format(\''.$userinfo['date_register'].'\' + interval '.$settings['time_dif'].' day, \'%b-%e-%Y %r\') as date_registered';
$sth = db_query($q);
$row = mysql_fetch_array($sth);
$userinfo['date_register'] = $row['date_registered'];
$smarty->assign('userinfo', $userinfo);
$smarty->assign('errors', $errors);
$smarty->assign('frm', $frm);
$smarty->assign('settings', $settings);
$smarty->assign('countries', $countries);
$smarty->display('edit_account.tpl');
?>
