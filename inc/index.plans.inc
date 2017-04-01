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


  $already_deposits = array ();
  if (0 < $userinfo['id'])
  {
    $q = 'select type_id from hm2_deposits where user_id = ' . $userinfo['id'];
    ($sth = db_query ($q) OR print mysql_error ());
    while ($row = mysql_fetch_array ($sth))
    {
      array_push ($already_deposits, $row['type_id']);
    }
  }

  $q = 'select * from hm2_types where status = \'on\' order by id';
  ($sth = db_query ($q) OR print mysql_error ());
  $plans = array ();
  while ($row = mysql_fetch_array ($sth))
  {
    if (0 < $userinfo['id'])
    {
      if (0 < $row['parent'])
      {
        if (!in_array ($row['parent'], $already_deposits))
        {
          continue;
        }
      }
    }

    $q = 'select * from hm2_plans where parent = ' . $row['id'] . ' order by id';
    if (!($sth1 = db_query ($q)))
    {
      exit (mysql_error ());
      ;
    }

    $row['plans'] = array ();
    while ($row1 = mysql_fetch_array ($sth1))
    {
      $row1['deposit'] = '';
      if ($row1['max_deposit'] == 0)
      {
        $row1['deposit'] = '$' . number_format ($row1['min_deposit']) . ' and more';
      }
      else
      {
        $row1['deposit'] = '$' . number_format ($row1['min_deposit']) . ' - $' . number_format ($row1['max_deposit']);
      }

      array_push ($row['plans'], $row1);
    }

    $periods = array ('d' => 'Daily', 'w' => 'Weekly', 'b-w' => 'Bi Weekly', 'm' => 'Monthly', 'y' => 'Yearly');
    $row['period'] = $periods[$row['period']];
    array_push ($plans, $row);
  }

  $smarty->assign ('index_plans', $plans);
?>
