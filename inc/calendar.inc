<?
/***********************************************************************/
/*                                                                     */
/*  This file is created by deZender                                   */
/*                                                                     */
/*  deZender (Decoder for Zend Encoder/SafeGuard):                     */
/*    Version:      0.9.3.0                                            */
/*    Author:       qinvent.com                                        */
/*    Release on:   2005.11.12                                         */
/*                                                                     */
/***********************************************************************/


  $id = intval ($frm['type']);
  $plans = array ();
  $q = 'select *, 
               date_format(now() + interval ' . $settings['time_dif'] . ' hour, \'%m/%d/%Y\') as from_date,
               date_format((now() + interval ' . $settings['time_dif'] . ('' . ' hour) + interval q_days day, \'%m/%d/%Y\') as to_date
        from hm2_types where id = ' . $id);
  if (!($sth = db_query ($q)))
  {
    exit (mysql_error ());
    ;
  }

  $trow = mysql_fetch_array ($sth);
  if (!$trow)
  {
    $smarty->assign ('error', 'type_not_found');
    $smarty->display ('calendar_simple.tpl');
    exit ();
  }

  $i = 0;
  $q = '' . 'select * from hm2_plans where parent = ' . $id . ' order by id';
  if (!($sth = db_query ($q)))
  {
    exit (mysql_error ());
    ;
  }

  while ($row = mysql_fetch_array ($sth))
  {
    $row['i'] = $i;
    ++$i;
    array_push ($plans, $row);
  }

  $smarty->assign ('plans', $plans);
  if (($trow['period'] == 'd' AND $trow['work_week']))
  {
    $trow['period'] = 'w-d';
  }

  $periods = array ('w-d' => 'Work Days', 'd' => 'Days', 'w' => 'Weeks', 'b-w' => 'Bi Weeks', 'm' => 'Months', '2m' => 'Bi-Months', '3m' => '3 Months', '6m' => '6 Months', 'y' => 'Years');
  $trow['period_name'] = $periods[$trow['period']];
  $trow['min_deposit'] = $plans[0][min_deposit];
  $smarty->assign ('type', $trow);
  if ($trow['period'] == 'end')
  {
    $smarty->display ('calendar_simple.tpl');
  }
  else
  {
    $smarty->display ('calendar.tpl');
  }

?>
