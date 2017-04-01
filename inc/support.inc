<?php
  if ($frm['action'] == 'send')
  {
    if ($frm['support_token'] != (new Flash)->get('support_token')) exit;
    $from = ($userinfo['logged'] ? $userinfo['email'] : $frm['email']);
    $message = $settings['site_name'] . ' Support request from ' . date ('l dS of F Y h:i:s A') . '

---------------------------------------------------------------
';
    $message .= $frm['message'];
    $message .= '

---------------------------------------------------------------
';
    $message .= 'User Additional Info :
';
    if ($userinfo['logged'] == 1)
    {
      $accounting = array ();
      $accounting = get_user_balance ($userinfo['id']);
      $message .= 'User      : ' . $userinfo['username'] . '
';
      $message .= 'User Name : ' . $userinfo['name'] . '
';
      $message .= 'E-Mail    : ' . $userinfo['email'] . '
';
      $message .= 'E-Gold Acc : ' . $userinfo['egold_account'] . '
';
      if ($settings[def_payee_account_intgold])
      {
        $message .= 'IntGold Acc : ' . $userinfo['intgold_account'] . '
';
      }

      if ($settings[def_payee_account_stormpay])
      {
        $message .= 'StormPay Acc : ' . $userinfo['stormpay_account'] . '
';
      }

      if ($settings[def_payee_account_ebullion])
      {
        $message .= 'e-Bullion Acc : ' . $userinfo['ebullion_account'] . '
';
      }

      if ($settings[def_payee_account_paypal])
      {
        $message .= 'PayPal Acc : ' . $userinfo['paypal_account'] . '
';
      }

      if ($settings[def_payee_account_goldmoney])
      {
        $message .= 'GoldMoney Acc : ' . $userinfo['goldmoney_account'] . '
';
      }

      if ($settings[def_payee_account_eeecurrency])
      {
        $message .= 'eeeCurrency Acc : ' . $userinfo['eeecurrency_account'] . '
';
      }

      if ($settings[def_payee_account_pecunix])
      {
        $message .= 'Pecunix Acc : ' . $userinfo['pecunix_account'] . '
';
      }

      $message .= 'Status    : ' . $userinfo['status'] . '
';
      $message .= '' . 'Active Deposits  : $' . sprintf ('%.02f', $accounting['active_deposit']) . '
';
    }
    else
    {
      $message .= 'User Name : ' . $frm['name'] . '
';
      $message .= 'E-Mail    : ' . $frm['email'] . '
';
      $message .= 'Not Registered/Logged user
';
    }

    $message .= 'IP Address: ' . $_SERVER['REMOTE_ADDR'] . '
';
    $message .= 'Language  : ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '
';
    $q = 'select * from hm2_users where id = 1';
    $sth = db_query ($q);
    $admin_email = '';
    while ($row = mysql_fetch_array ($sth))
    {
      $admin_email = $row['email'];
    }

    send_mail($admin_email, $settings['site_name'] . ' Support Request', $message, '' . 'From: ' . $from);
    header ('Location: ?a=support&say=send

');
    exit ();
  }

  $token = (new Flash)->setget('support_token', genarate_token());
  $smarty->assign ('token', $token);
  $smarty->assign ('say', $frm['say']);
  $smarty->display ('support.tpl');
