<?php
include 'lib/config.inc.php';
$dbconn = db_open();
if ( ! $dbconn) {
    print 'Cannot connect mysql';
    exit ();
}

$mymd5 = $settings['md5altphrase_goldmoney'];
if ($frm['a'] == 'pay_withdraw') {
    $batch = $frm['OMI_TXN_ID'];
    list ($id, $str) = explode('-', $frm['withdraw']);
    $id = sprintf('%d', $id);
    if ($str == '') {
        $str = 'abcdef';
    }

    $str = quote($str);
    $q = ''.'select * from hm2_history where id = '.$id.' and str = \''.$str.'\'';
    $sth = db_query($q);
    while ($row = mysql_fetch_array($sth)) {
        $q = ''.'delete from hm2_history where id = '.$id;
        (db_query($q) OR print mysql_error());
        $q = 'insert into hm2_history set 
	user_id = '.$row['user_id'].',
	amount = -'.abs($row['amount']).(''.',
	type = \'withdrawal\',
	description = \'Withdraw processed. Batch id = '.$batch.'\',
	actual_amount = -').abs($row['amount']).',
	ec = 7,
	date = now()
	';
        (db_query($q) OR print mysql_error());
        $q = 'select * from hm2_users where id = '.$row['user_id'];
        $usth = db_query($q);
        $userinfo = mysql_fetch_array($usth);
        $info = [$user];
        $info['username'] = $userinfo['username'];
        $info['name'] = $userinfo['name'];
        $info['amount'] = sprintf('%.02f', abs($row['amount']));
        $info['account'] = $frm['OMI_MERCHANT_HLD_NO'];
        $info['batch'] = $batch;
        $info['paying_batch'] = $batch;
        $info['receiving_batch'] = $batch;
        $info['currency'] = $exchange_systems[7]['name'];
        send_template_mail('withdraw_user_notification', $userinfo['email'], $settings['system_email'], $info);
    }

    print 1;
    db_close($dbconn);
    exit ();
}

if ($frm['OMI_MODE'] != 'LIVE') {
    print '1';
    db_close($dbconn);
    exit ();
}

$hash = strtoupper(md5($frm['OMI_MERCHANT_REF_NO'].'?'.$frm['OMI_MODE'].'?'.$frm['OMI_MERCHANT_HLD_NO'].'?'.$frm['OMI_PAYER_HLD_NO'].'?'.$frm['OMI_CURRENCY_CODE'].'?'.$frm['OMI_CURRENCY_AMT'].'?'.$frm['OMI_GOLDGRAM_AMT'].'?'.$frm['OMI_TXN_ID'].'?'.$frm['OMI_TXN_DATETIME'].'?'.$mymd5));
if (($hash == strtoupper($frm['OMI_HASH']) AND $exchange_systems[7]['status'] == 1)) {
    $user_id = sprintf('%d', $frm['userid']);
    $h_id = sprintf('%d', $frm['hyipid']);
    $compound = sprintf('%d', $frm['compound']);
    $amount = $frm['OMI_CURRENCY_AMT'];
    $batch = $frm['OMI_TXN_ID'];
    $account = $frm['OMI_PAYER_HLD_NO'];
    if (($frm['a'] == 'checkpayment' AND $frm['OMI_CURRENCY_CODE'] == 840)) {
        add_deposit(7, $user_id, $amount, $batch, $account, $h_id, $compound);
    }
}

db_close($dbconn);
print '1';
exit ();
