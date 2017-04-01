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


  if ((($settings['show_news_box'] AND ($frm['a'] == '' OR $frm['a'] == 'home')) OR $settings['show_news_always']))
  {
    ($max_news = $settings['last_news_count'] OR 5);
    $q = 'select 
                 *,
                 date_format(date + interval ' . $settings['time_dif'] . ('' . ' hour, \'%b-%e-%Y %r\') as d
           from 
                 hm2_news 
           order by 
                 date desc
           limit 
                 0, ' . $max_news);
    ($sth = db_query ($q) OR print mysql_error ());
    $news = array ();
    while ($row = mysql_fetch_array ($sth))
    {
      if ($row['small_text'] == '')
      {
        $row['full_text'] = strip_tags ($row['full_text']);
        $row['small_text'] = preg_replace ('/^(.{100,120})\\s.*/', '' . '$1...', $row['full_text']);
      }

      $row['small_text'] = preg_replace ('/
/', '<br>', $row['small_text']);
      array_push ($news, $row);
    }

    if (sizeof ($news) == 0)
    {
      $settings['show_news_box'] = 0;
      $smarty->assign ('settings', $settings);
    }
    else
    {
      $smarty->assign ('news', $news);
    }
  }
  else
  {
    $settings['show_news_box'] = 0;
    $smarty->assign ('settings', $settings);
  }

?>
