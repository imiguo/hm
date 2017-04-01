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


  $activation_code = quote ($frm['code']);
  $code_found = 0;
  if ($activation_code != '')
  {
    $q = '' . 'select id from hm2_users where activation_code = \'' . $activation_code . '\'';
    $sth = db_query ($q);
    while ($row = mysql_fetch_array ($sth))
    {
      $q = 'update hm2_users set bf_counter = 0, activation_code = \'\' where id = ' . $row['id'];
      db_query ($q);
      $code_found = 1;
    }
  }

  $smarty->assign ('activated', $code_found);
  $smarty->display ('activate.tpl');
?>
