<?phpheader('HTTP/1.1 202 Accepted');include 'lib/config.inc.php';$dbconn = db_open();if (! $dbconn) {
    print 'Cannot connect mysql';
    exit();
}$frm['transaction_id'] = sprintf('%d', $frm['transaction_id']);if ((((($frm['status'] == 'SUCCESS' and $exchange_systems[4]['status'] == 1) and $frm['secret_code'] == $settings['md5altphrase_stormpay']) and 0 < $frm['transaction_id']) and $frm['transaction_type'] == 'Payment')) {
    $user_id = sprintf('%d', $frm['user1']);
    $h_id = sprintf('%d', $frm['user2']);
    $compound = sprintf('%d', $frm['user4']);
    $amount = $frm['amount'];
    $batch = $frm['transaction_id'];
    $account = $frm['payer_email'];
    if ($frm['user3'] == 'checkpayment') {
        add_deposit(4, $user_id, $amount, $batch, $account, $h_id, $compound);
    }
}db_close($dbconn);print '1';exit();
