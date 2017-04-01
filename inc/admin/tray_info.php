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


  $q = 'select count(*) as col from hm2_users where id > 1';
  if (!($sth = db_query ($q)))
  {
    print mysql_error ();
    ;
  }

  $qmembers = 0;
  while ($row = mysql_fetch_array ($sth))
  {
    $qmembers = $row['col'];
  }

  $q = 'select sum(actual_amount) as col from hm2_deposits where id > 1';
  ($sth = db_query ($q) OR print mysql_error ());
  $deposit = 0;
  while ($row = mysql_fetch_array ($sth))
  {
    $deposit = number_format (abs ($row['col']), 2);
  }

  $q = 'select sum(actual_amount) as col from hm2_history where type=\'withdrawal\'';
  ($sth = db_query ($q) OR print mysql_error ());
  $withdraw = 0;
  while ($row = mysql_fetch_array ($sth))
  {
    $withdraw = number_format (abs ($row['col']), 2);
  }

  echo 'Members: 
';
  echo $qmembers;
  echo 'Deposits: $';
  echo $deposit;
  echo 'Withdrawals: $';
  echo $withdraw;
?>
