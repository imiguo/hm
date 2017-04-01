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
  $q = 'select max(percent) as percent from hm2_referal';
  $sth = db_query ($q);
  while ($row = mysql_fetch_array ($sth))
  {
    $smarty->assign ('percent', $row['percent']);
  }

  $smarty->display ('wap/home.tpl');
?>
