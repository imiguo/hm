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


  $q = '' . 'select 
               u1.username,
               u1.id,
               count(*) as col
         from 
               hm2_users as u1 left outer join hm2_users as u2
                 on u1.id = u2.ref
         where u2.date_register > \'' . $settings[refs10_start_date] . '\' + interval ' . $settings['time_dif'] . ' hour
         group by
               u1.username
         having col > 0
         order by col desc, u1.id
         limit 0, 20
        ';
  ($sth = db_query ($q) OR print mysql_error ());
  $stats = array ();
  while ($row = mysql_fetch_array ($sth))
  {
    $q2 = '' . 'select 
                  count(distinct u.id) as col
            from
                  hm2_users as u,
                  hm2_deposits as d
            where
                  u.ref = ' . $row[id] . ' and
                  u.id = d.user_id
           ';
    ($sth1 = db_query ($q2) OR print mysql_error ());
    $row1 = mysql_fetch_array ($sth1);
    $row[active_col] = $row1[col];
    array_push ($stats, $row);
  }

  $q = '' . 'select date_format(\'' . $settings[refs10_start_date] . '\' + interval ' . $settings['time_dif'] . ' hour, \'%b-%e-%Y\') as d';
  ($sth = db_query ($q) OR print mysql_error ());
  $row = mysql_fetch_array ($sth);
  $smarty->assign ('start_date', $row[d]);
  $smarty->assign ('stats', $stats);
  $smarty->display ('refs10.tpl');
?>
