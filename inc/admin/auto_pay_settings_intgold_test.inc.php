<?php/***********************************************************************//*                                                                     *//*  This file is created by deZender                                   *//*                                                                     *//*  deZender (Decoder for Zend Encoder/SafeGuard):                     *//*    Version:      0.9.3.1                                            *//*    Author:       qinvent.com                                        *//*    Release on:   2005.12.5                                          *//*                                                                     *//***********************************************************************/  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head><title>HYIP Manager Pro</title><link href="images/adminstyle.css" rel="stylesheet" type="text/css"></head><body bgcolor="#FFFFF2" link="#666699" vlink="#666699" alink="#666699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" ><center><br><br><br>	 <table cellspacing=0 cellpadding=1 border=0 width=80% height=100% ';  echo 'bgcolor=#ff8d00>	   <tr>	     <td>           <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">             <tr bgcolor="#FFFFFF" valign="top"> <td bgcolor=#FFFFFF>';  if (function_exists('curl_init')) {
    $ch = curl_init();
    print curl_error($ch);
    curl_setopt($ch, CURLOPT_URL, 'https://intgold.com/cgi-bin/autopay.cgi');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'ACCOUNTID=' . $frm['acc'] . '&PASSWORD=' . $frm['pass'] . '&SECPASSWORD=' . $frm['code'] . '&RECEIVER=' . $frm['acc'] . '&AMOUNT=0.01&TEST=Y');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = curl_exec($ch);
    curl_close($ch);
    $parts = array();
    if (preg_match('' . '/TEST\\sTRANSACTION_ID:(.*?)$/ims', $a, $parts)) {
        print 'Test status: OK<br>Batch id = ' . $parts[1];
    } else {
        print 'Test status: Failed<br>' . $a;
    }
} else {
    print 'Sorry, but curl does not installed on your server';
}  echo '</tr></table></tr></table></center></body>';  exit();
