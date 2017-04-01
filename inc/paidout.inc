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


  $frm['month'] = intval ($frm['month']);
  if ($frm['month'] == 0)
  {
    $frm['month'] = date ('n', time () + $settings['time_dif'] * 60 * 60);
  }

  $frm['year'] = intval ($frm['year']);
  if ($frm['year'] == 0)
  {
    $frm['year'] = date ('Y', time () + $settings['time_dif'] * 60 * 60);
  }

  $smarty->assign ('frm', $frm);
  $month = array ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  $smarty->assign ('month', $month);
  $year = array ();
  for ($i = $settings['site_start_year']; $i <= date ('Y', time () + $settings['time_dif'] * 60 * 60); ++$i)
  {
    array_push ($year, $i);
  }

  $smarty->assign ('year', $year);
  $datewhere = ' \'' . $frm['year'] . '\' = year(date + interval ' . $settings['time_dif'] . ' hour) and ' . '\'' . $frm['month'] . '\' = month(date + interval ' . $settings['time_dif'] . ' hour) ';
  $type = 'withdrawal';
  $q = '' . '
        select 
              count(*) as cnt
        from 
              hm2_history 
        where 
              ' . $datewhere . ' and type = \'' . $type . '\' and user_id != 1
       ';
  ($sth = db_query ($q) OR print mysql_error ());
  $row = mysql_fetch_array ($sth);
  $count_all = $row['cnt'];
  $page = intval ($frm['page']);
  $onpage = 20;
  $colpages = ceil ($count_all / $onpage);
  if ($page < 1)
  {
    $page = 1;
  }

  if (($colpages < $page AND 1 < $colpages))
  {
    $page = $colpages;
  }

  $from = ($page - 1) * $onpage;
  $q = 'select 
              u.username,
              h.type,
              h.actual_amount,
              date_format(date + interval ' . $settings['time_dif'] . ('' . ' hour, \'%b-%e-%Y %r\') as dd
        from 
              hm2_users as u left outer join hm2_history as h
                on u.id = h.user_id
        where ' . $datewhere . ' and h.type = \'' . $type . '\' and user_id != 1
        order by h.id desc
        limit ' . $from . ', ' . $onpage . '
       ');
  ($sth = db_query ($q) OR print mysql_error ());
  $stats = array ();
  $total_withdraw = 0;
  while ($row = mysql_fetch_array ($sth))
  {
    $total_withdraw += abs ($row['actual_amount']);
    $row['actual_amount'] = number_format (abs ($row['actual_amount']), 2);
    array_push ($stats, $row);
  }

  $smarty->assign ('stats', $stats);
  $smarty->assign ('total_withdraw', number_format ($total_withdraw, 2));
  $pages = array ();
  for ($i = 1; $i <= $colpages; ++$i)
  {
    $apage = array ();
    $apage['page'] = $i;
    $apage['current'] = ($i == $page ? 1 : 0);
    array_push ($pages, $apage);
  }

  $smarty->assign ('pages', $pages);
  $smarty->assign ('colpages', $colpages);
  $smarty->assign ('current_page', $page);
  if (1 < $page)
  {
    $smarty->assign ('prev_page', $page - 1);
  }

  if ($page < $colpages)
  {
    $smarty->assign ('next_page', $page + 1);
  }

  $smarty->display ('paidout.tpl');
?>
