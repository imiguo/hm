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


  header ('Content-type: text/vnd.wap.wml');
  $id = $userinfo['id'];
  $q = 'select date_format(date + interval ' . $settings['time_dif'] . ('' . ' hour, \'%b-%e-%Y %r\') as last_login from hm2_user_access_log where user_id = ' . $id . ' order by id desc limit 1, 1');
  $sth = db_query ($q);
  while ($row = mysql_fetch_array ($sth))
  {
    $last_access = $row['last_login'];
  }

  $smarty->assign ('last_access', $last_access);
  $ab = get_user_balance ($id);
  $ab_formated = array ();
  $ab['deposit'] = 0 - $ab['deposit'];
  $ab['earning'] = number_format ($ab['earning'], 2);
  reset ($ab);
  while (list ($kk, $vv) = each ($ab))
  {
    $ab_formated[$kk] = number_format ($vv, 2);
  }

  $smarty->assign ('ab_formated', $ab_formated);
  $q = 'select *, date_format(deposit_date + interval ' . $settings['time_dif'] . ('' . ' hour, \'%b-%e-%Y %r\') as dd from hm2_deposits where user_id = ' . $id . ' and status = \'on\' order by deposit_date desc limit 1');
  ($sth = db_query ($q) OR print mysql_error ());
  if ($row = mysql_fetch_array ($sth))
  {
    $smarty->assign ('last_deposit', number_format ($row['amount'], 2));
    $smarty->assign ('last_deposit_date', $row['dd']);
  }

  $q = 'select *, date_format(date + interval ' . $settings['time_dif'] . ('' . ' hour, \'%b-%e-%Y %r\') as dd from hm2_history where user_id = ' . $id . ' and type = \'withdrawal\' order by date desc limit 1');
  ($sth = db_query ($q) OR print mysql_error ());
  if ($row = mysql_fetch_array ($sth))
  {
    $smarty->assign ('last_withdrawal', number_format (abs ($row['actual_amount']), 2));
    $smarty->assign ('last_withdrawal_date', $row['dd']);
  }

  $smarty->display ('wap/account_main.tpl');
?>
