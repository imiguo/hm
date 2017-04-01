<?php
include 'lib/config.inc.php';
$dbconn = db_open();
if ( ! $dbconn) {
    print 'Cannot connect mysql';
    exit ();
}

$mymd5 = $settings['md5altphrase_evocash'];
if ($frm['a'] == 'pay_withdraw') {
    $batch = $frm['receivingtransactionid'];
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
	ec = 1,
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
        $info['account'] = $frm['receivingaccountid'];
        $info['batch'] = $frm['payingtransactionid'].'/'.$frm['receivingtransactionid'];
        $info['paying_batch'] = $frm['payingtransactionid'];
        $info['receiving_batch'] = $frm['receivingtransactionid'];
        $info['currency'] = $exchange_systems[1]['name'];
        send_template_mail('withdraw_user_notification', $userinfo['email'], $settings['system_email'], $info);
    }

    print 1;
    db_close($dbconn);
    exit ();
}

$hash = strtoupper(md5($frm['payingaccountid'].':'.$frm['receivingaccountid'].':'.$frm['payingtransactionid'].':'.$frm['receivingtransactionid'].':'.$frm['amount'].':'.strtoupper(md5($settings['md5altphrase_evocash'])).':'.$frm['timestampgmt']));
if (($hash == strtoupper($frm['merchanthashcheck']) AND $exchange_systems[1]['status'] == 1)) {
    $user_id = sprintf('%d', $frm['userid']);
    $h_id = sprintf('%d', $frm['hyipid']);
    $compound = sprintf('%d', $frm['compound']);
    $amount = $frm['amount'];
    $batch = $frm['receivingtransactionid'];
    $account = $frm['payingaccountid'];
    if ($frm['a'] == 'checkpayment') {
        add_deposit(1, $user_id, $amount, $batch, $account, $h_id, $compound);
    }
}

db_close($dbconn);
print '1';
exit ();
