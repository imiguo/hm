<?php
  if ($frm['say'] == 'invalid_passphrase')
  {
    echo '<b style="color:red">Invalid Alternative Passphrase. No data has been updated.</b><br><br>
';
  }

  if ($frm['say'] == 'done')
  {
    echo '<b style="color:green">Changes have been successfully updated.</b><br>
<br>
';
  }

  if ($userinfo['logged'] == 0)
  {
    exit ();
  }

  echo '
<b>Advanced login security settings:</b><br><br>

<form method=post>
<input type=hidden name=a value="change_login_security">
<input type=hidden name=act value="change">
Detect IP Address Change Sensitivity<br>
<input type=radio name=ip value=disabled ';
  echo ($acsent_settings['detect_ip'] == 'disabled' ? 'checked' : 'ddd');
  echo '>Disabled<br>
<input type=radio name=ip value=medium ';
  echo ($acsent_settings['detect_ip'] == 'medium' ? 'checked' : '');
  echo '>Medium<br>
<input type=radio name=ip value=high ';
  echo ($acsent_settings['detect_ip'] == 'high' ? 'checked' : '');
  echo '>High<br><br>

Detect Browser Change<br>
<input type=radio name=browser value=disabled ';
  echo ($acsent_settings['detect_browser'] == 'disabled' ? 'checked' : '');
  echo '>Disabled<br>
<input type=radio name=browser value=enabled ';
  echo ($acsent_settings['detect_browser'] == 'enabled' ? 'checked' : '');
  echo '>Enabled<br><br>
E-mail:<br>
<input type=text name=email value="';
  echo $acsent_settings['email'];
  echo '" class=inpts size=50><br>
<input type=submit value="Set" class=sbmt>
</form>
<hr>
  <br><br><br>
';
  $dirs = array ();
  if (!file_exists ('./inc/.htaccess'))
  {
    array_push ($dirs, './inc');
  }

  if (!file_exists ('./tmpl/.htaccess'))
  {
    array_push ($dirs, './tmpl');
  }

  if (!file_exists ('./tmpl_c/.htaccess'))
  {
    array_push ($dirs, './tmpl_c');
  }

  if (0 < sizeof ($dirs))
  {
    echo '
<b>Security note:</b><br><br>
Please upload the .htaccess file to the following folders:<br>
';
    for ($i = 0; $i < sizeof ($dirs); ++$i)
    {
      print '<li>' . $dirs[$i] . '</li>';
    }

    echo 'You can find the .htaccess files in the latest archive with the hyip manager script. 
<hr>
  <br><br><br>
';
  }

  echo '
<b>Encode mysql information and other settings:</b><br><br>
<form method=post>
';
  if (!file_exists ('./tmpl_c/.htdata'))
  {
    echo '<input type=hidden name=a value=encrypt_mysql>
';
    if ($userinfo['transaction_code'] != '')
    {
      echo '<table cellspacing=0 cellpadding=1 border=0>
<tr>
 <td>Alternative Passphrase: </td>
 <td><input type=password name="alternative_passphrase" value="" class=inpts size=30></td>
</tr>
</table>
';
    }

    echo '<input type=submit class=sbmt value="Encode mysql data and other settings."><br>
  It will prevent from hacking attempts when they access mysql directly. Even 
  if a hacker knows your ftp data it will be impossible to change any settings.<br>
';
    echo '<s';
    echo 'pan style="color: red">This action cannot be undone</span>
';
  }
  else
  {
    $code = file ('./tmpl_c/.htdata');
    echo '<textarea class=inpts cols=100 rows=10>';
    echo $code[0];
    echo '
------------------------
';
    echo $settings['key'];
    echo '</textarea>
<br>
  <br> You should save the information from this textarea to your hard disk 
  every time after you change any settings. 
  ';
  }

  echo '</form>
<hr>
<br><br>
';
?>
