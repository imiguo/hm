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


  $q = 'select max(percent) as percent from hm2_referal';
  $sth = db_query ($q);
  while ($row = mysql_fetch_array ($sth))
  {
    $smarty->assign ('percent', $row['percent']);
  }

  $ref_plans = array ();
  $q = 'select * from hm2_referal order by from_value';
  $sth = db_query ($q);
  while ($row = mysql_fetch_array ($sth))
  {
    array_push ($ref_plans, $row);
  }

  $smarty->assign ('ref_plans', $ref_plans);
  $ref_levels = array ();
  for ($l = 2; $l < 11; ++$l)
  {
    if ((0 < $settings['ref' . $l . '_cms'] AND $settings['ref' . $l . '_cms'] < 100))
    {
      array_push ($ref_levels, array ('level' => $l, 'percent' => $settings['ref' . $l . '_cms']));
      continue;
    }
  }

  $smarty->assign ('ref_levels', $ref_levels);
  $smarty->display ('ref_plans.tpl');
?>
