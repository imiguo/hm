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


  $q = 'select count(*) as call from hm2_news';
  if (!($sth = db_query ($q)))
  {
    print mysql_error ();
    ;
  }

  $row = mysql_fetch_array ($sth);
  $count_all = $row['call'];
  $page = $frm['page'];
  $onpage = 20;
  $colpages = ceil ($count_all / $onpage);
  if ($page <= 1)
  {
    $page = 1;
  }

  if (($colpages < $page AND 1 < $colpages))
  {
    $page = $colpages;
  }

  $from = ($page - 1) * $onpage;
  $q = 'select 
                 *,
                 date_format(date + interval ' . $settings['time_dif'] . ('' . ' hour, \'%b-%e-%Y %r\') as d
           from 
                 hm2_news 
           order by 
                 date desc
           limit 
                 ' . $from . ', ' . $onpage);
  ($sth = db_query ($q) OR print mysql_error ());
  $news = array ();
  while ($row = mysql_fetch_array ($sth))
  {
    if ($row['full_text'] == '')
    {
      $row['full_text'] = $row['small_text'];
    }

    $row['full_text'] = preg_replace ('/
/', '<br>', $row['full_text']);
    array_push ($news, $row);
  }

  $smarty->assign ('news', $news);
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

  $smarty->display ('news.tpl');
?>
