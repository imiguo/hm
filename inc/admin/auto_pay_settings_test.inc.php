<?
/***********************************************************************/
/*                                                                     */
/*  This file is created by deZender                                   */
/*                                                                     */
/*  deZender (Decoder for Zend Encoder/SafeGuard):                     */
/*    Version:      0.9.3.1                                            */
/*    Author:       qinvent.com                                        */
/*    Release on:   2005.12.5                                          */
/*                                                                     */
/***********************************************************************/


  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>HYIP Manager Pro</title>
<link href="images/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#FFFFF2" link="#666699" vlink="#666699" alink="#666699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<center>
<br><br><br>
	 <table cellspacing=0 cellpadding=1 border=0 width=80% he';
  echo 'ight=100% bgcolor=#ff8d00>
	   <tr>
	     <td>
           <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
             <tr bgcolor="#FFFFFF" valign="top"> 
<td bgcolor=#FFFFFF>
';
  if (function_exists ('curl_init'))
  {
    ini_set ('error_reporting', E_ALL);
    $ch = curl_init ();
    print curl_error ($ch);
    curl_setopt ($ch, CURLOPT_URL, 'https://www.e-gold.com/acct/confirm.asp');
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, 'AccountID=' . $frm['acc'] . '&PassPhrase=' . $frm['pass'] . '&Payee_Account=' . $frm['acc'] . '&Amount=0.01&PAY_IN=1&WORTH_OF=Gold&Memo=Test+transaction&IGNORE_RATE_CHANGE=y');
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = curl_exec ($ch);
    print '' . '<hr>' . $a . '<hr>';
    curl_close ($ch);
    $parts = array ();
    if (preg_match ('/<input type=hidden name=PAYMENT_BATCH_NUM VALUE="(\\d+)">/ims', $a, $parts))
    {
      print 'Test status: OK<br>Batch id = ' . $parts[1];
    }
    else
    {
      if (preg_match ('/<input type=hidden name=ERROR VALUE="(.*?)">/ims', $a, $parts))
      {
        $txt = preg_replace ('/&lt;/i', '<', $parts[1]);
        $txt = preg_replace ('/&gt;/i', '>', $txt);
        print 'Test status: Failed<br>' . $txt;
      }
      else
      {
        print '' . 'Test status: Failed<br>Unknown Error:<BR>' . $a;
      }
    }
  }
  else
  {
    print 'Sorry, but curl does not installed on your server';
  }

  echo '
</tr></table>
</tr></table>
</center>
</body>
';
  exit ();
?>
