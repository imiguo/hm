<?php

/*
 * This file is part of the entimm/hm.
 *
 * (c) entimm <entimm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/*                                                                     */
/*  This file is created by deZender                                   */
/*                                                                     */
/*  deZender (Decoder for Zend Encoder/SafeGuard):                     */
/*    Version:      0.9.3.1                                            */
/*    Author:       qinvent.com                                        */
/*    Release on:   2005.12.5                                          */
/*                                                                     */
/***********************************************************************/

  $id = sprintf('%d', $frm['id']);
  $q = ''.'select * from hm2_types where id = '.$id;
  ($sth = db_query($q) or print mysql_error());
  $flag = 0;
  while ($row = mysql_fetch_array($sth)) {
      $smarty->assign('package', $row);
      $flag = 1;
  }

  if ($flag == 0) {
      $smarty->assign('no_such_plan', 1);
  }

  $smarty->display('package_info.tpl');
