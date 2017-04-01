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
    $ch = curl_init ();
    print curl_error ($ch);
    curl_setopt ($ch, CURLOPT_URL, 'https://www.evocash.com/evoswift/instantpayment.cfm?payingaccountid=' . $frm['acc'] . '&username=' . $frm['username'] . '&password=' . $frm['pass'] . '&transaction_code=' . $frm['code'] . '&amount=0.01&reference=ref&memo=memo&receivingaccountid=' . $frm['acc']);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = curl_exec ($ch);
    curl_close ($ch);
    $parts = array ();
    if (preg_match ('/<INPUT TYPE="Hidden" NAME="Error" VALUE="(.*?)">/ims', $a, $parts))
    {
      $txt = preg_replace ('/&lt;/i', '<', $parts[1]);
      $txt = preg_replace ('/&gt;/i', '>', $txt);
      if ($txt == 'You can\'t make a transfer to your own account.')
      {
        print 'Test status: OK<br>';
      }
      else
      {
        print 'Test status: Failed<br>' . $txt;
      }
    }
    else
    {
      print '' . 'Test status: Failed<br>Unknown Error:<BR>' . $a;
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
