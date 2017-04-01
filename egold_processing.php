<?php
include 'lib/config.inc.php';
$dbconn = db_open();
if ( ! $dbconn) {
    print 'Cannot connect mysql';
    exit ();
}

$mymd5 = $settings['md5altphrase'];
if ($frm['a'] == 'pay_withdraw') {
    $batch = $frm['PAYMENT_BATCH_NUM'];
    list ($id, $str) = explode('-', $frm['withdraw']);
    $id = sprintf('%d', $id);
    if ($str == '') {
        $str = 'abcdef';
    }

    $str = quote($str);
    $q = ''.'select * from hm2_history where id = '.$id.' and str = \''.$str.'\' and type=\'withdraw_pending\'';
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
	ec = 0,
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
        $info['account'] = $frm['PAYEE_ACCOUNT'];
        $info['batch'] = $batch;
        $info['paying_batch'] = $batch;
        $info['receiving_batch'] = $batch;
        $info['currency'] = $exchange_systems[0]['name'];
        send_template_mail('withdraw_user_notification', $userinfo['email'], $settings['system_email'], $info);
    }

    print 1;
    db_close($dbconn);
    exit ();
}

$hash = strtoupper(md5($frm['PAYMENT_ID'].':'.$frm['PAYEE_ACCOUNT'].':'.$frm['PAYMENT_AMOUNT'].':'.$frm['PAYMENT_UNITS'].':'.$frm['PAYMENT_METAL_ID'].':'.$frm['PAYMENT_BATCH_NUM'].':'.$frm['PAYER_ACCOUNT'].':'.$mymd5.':'.$frm['ACTUAL_PAYMENT_OUNCES'].':'.$frm['USD_PER_OUNCE'].':'.$frm['FEEWEIGHT'].':'.$frm['TIMESTAMPGMT']));
if (($hash == strtoupper($frm['V2_HASH']) AND $exchange_systems[0]['status'] == 1)) {
    $ip = $frm_env['REMOTE_ADDR'];
    if ( ! preg_match('/63\\.240\\.230\\.\\d/i', $ip)) {
        exit ();
    }

    $user_id = sprintf('%d', $frm['userid']);
    $h_id = sprintf('%d', $frm['hyipid']);
    $compound = sprintf('%d', $frm['compound']);
    $amount = $frm['PAYMENT_AMOUNT'];
    $batch = $frm['PAYMENT_BATCH_NUM'];
    $account = $frm['PAYER_ACCOUNT'];
    if ((($frm['a'] == 'checkpayment' AND $frm['PAYMENT_METAL_ID'] == 1) AND $frm['PAYMENT_UNITS'] == 1)) {
        add_deposit(0, $user_id, $amount, $batch, $account, $h_id, $compound);
    }
}

db_close($dbconn);
print '1';
exit ();
?>
