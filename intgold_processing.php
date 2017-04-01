<?php
include 'lib/config.inc.php';
$dbconn = db_open();
if ( ! $dbconn) {
    print 'Cannot connect mysql';
    exit ();
}

$mymd5 = $settings['md5altphrase_intgold'];
if ($frm['CUSTOM2'] == 'pay_withdraw') {
    $batch = $frm['TRANSACTION_ID'];
    list ($id, $str) = explode('-', $frm['CUSTOM1']);
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
	ec = 2,
	date = now()
	';
        (db_query($q) OR print mysql_error());
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

    print 1;
    db_close($dbconn);
    exit ();
}

if (($mymd5 == $frm['HASH'] AND ($frm['TRANSACTION_ID'] != '' AND $exchange_systems[2]['status'] == 1))) {
    if ($frm['RESULT'] != '0') {
        db_close($dbconn);
        exit ();
    }

    $user_id = sprintf('%d', $frm['ITEM_NUMBER']);
    $h_id = sprintf('%d', $frm['CUSTOM2']);
    $compound = sprintf('%d', $frm['CUSTOM4']);
    $amount = $frm['AMOUNT'];
    $batch = $frm['TRANSACTION_ID'];
    $account = $frm['BUYERACCOUNTID'];
    if ($frm['CUSTOM3'] == 'checkpayment') {
        add_deposit(2, $user_id, $amount, $batch, $account, $h_id, $compound);
    }
}

db_close($dbconn);
print '1';
exit ();
