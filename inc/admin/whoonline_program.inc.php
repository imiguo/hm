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


  $q = 'select count(*) as col from hm2_online ';
  $sth = db_query ($q);
  $visitors_online = 0;
  while ($row = mysql_fetch_array ($sth))
  {
    $visitors_online = $row['col'];
  }

  echo '<html>
<head>
<link href="images/adminstyle.css" rel="stylesheet" type="text/css">
</head>
<body>
<b>Who online:</b><br><br>

Number visitors: ';
  echo $visitors_online;
  echo '<br><br>

Registered Uses:<br><br>
';
  $q = 'select * from hm2_users where last_access_time + interval 30 minute > now()';
  $sth = db_query ($q);
  $i = 0;
  while ($row = mysql_fetch_array ($sth))
  {
    if (0 < $i)
    {
      print ', ';
    }

    print $row['username'];
    ++$i;
  }

?>
