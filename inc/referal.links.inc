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


  $smarty->assign ('site_name', $settings['site_name']);
  if ($settings[use_names_in_referral_links] == 1)
  {
    $userinfo[name] = preg_replace ('/\\s+/', '_', $userinfo[name]);
    $userinfo[username] = $userinfo[name];
  }

  $smarty->assign ('user', $userinfo);
  $smarty->display ('referal.links.tpl');
?>
