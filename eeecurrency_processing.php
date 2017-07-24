<?php

include 'lib/config.inc.php';
$dbconn = db_open();
if (!$dbconn) {
    echo 'Cannot connect mysql';
    exit();
}

$mymd5 = $settings['md5altphrase_eeecurrency'];
if (($mymd5 == $frm['HASH'] and ($frm['TRANSACTION_ID'] != '' and $exchange_systems[8]['status'] == 1))) {
    if ($frm['RESULT'] != '0') {
        db_close($dbconn);
        exit();
    }

    $user_id = sprintf('%d', $frm['ITEM_NUMBER']);
    $h_id = sprintf('%d', $frm['CUSTOM2']);
    $compound = sprintf('%d', $frm['CUSTOM4']);
    $amount = $frm['AMOUNT'];
    $batch = $frm['TRANSACTION_ID'];
    $account = $frm['BUYERACCOUNTID'];
    if ($frm['CUSTOM3'] == 'checkpayment') {
        add_deposit(8, $user_id, $amount, $batch, $account, $h_id, $compound);
    }
}

db_close($dbconn);
echo '1';
exit();
