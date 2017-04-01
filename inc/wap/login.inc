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
  $smarty->assign ('frm', $frm);
  $smarty->display ('wap/login.tpl');
  echo '
';
?>
