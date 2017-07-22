<?php
include 'lib/config.inc.php';
$dbconn = db_open();
if ( ! $dbconn) {
    print 'Cannot connect mysql';
    exit ();
}

file_put_contents('../log/payeer_processing_'.ENV.'.txt', json_encode($frm) . PHP_EOL, FILE_APPEND);
file_put_contents('../log/payeer_processing_'.ENV.'.txt', 'IP:' . $frm_env['REMOTE_ADDR'] . PHP_EOL, FILE_APPEND);

if ($frm['a'] == 'checkpayment') {
    // Rejecting queries from IP addresses not belonging to Payeer
    if (!in_array($_SERVER['REMOTE_ADDR'], array('185.71.65.92', '185.71.65.189',
        '149.202.17.210'))) exit;
    if (isset($_POST['m_operation_id']) && isset($_POST['m_sign']))
    {
        $m_key = 'aeb814a7f44a';
        // Forming an array for signature generation
        $arHash = array(
            $_POST['m_operation_id'],
            $_POST['m_operation_ps'],
            $_POST['m_operation_date'],
            $_POST['m_operation_pay_date'],
            $_POST['m_shop'],
            $_POST['m_orderid'],
            $_POST['m_amount'],
            $_POST['m_curr'],
            $_POST['m_desc'],
            $_POST['m_status']
        );
    // Adding additional parameters to the array if such parameters have been transferred
    if (isset($_POST['m_params']))
    {
        $arHash[] = $_POST['m_params'];
    }
     // Adding the secret key to the array
    $arHash[] = $m_key;
     // Forming a signature
    $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));
     // If the signatures match and payment status is “Complete”
    if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success')
    {
        $arr = explode('-', $_POST['m_orderid']);
        $user_id = $arr[0];
        $h_id = $arr[2];
        add_deposit(10, $user_id, $_POST['m_amount'], $_POST['m_operation_id'], $_POST['client_account'], $h_id, 0);

        // Here you can mark the invoice as paid or transfer funds to your customer
        // Returning that the payment was processed successfully
        exit($_POST['m_orderid'].'|success');
    }
     // If not, returning an error
     exit($_POST['m_orderid'].'|error');
    }

    db_close($dbconn);
    print '1';
    exit ();
}
