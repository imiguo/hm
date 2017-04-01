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


  echo '<b>Custom pages:</b><br><br>



You can add any custom html to our script, for example "Rate Us" html where will be stored links to rating sites.<br><br>
To create custom page you should follow the next steps:<br>
<li>copy \'example.tpl\' file to [your_document_name].tpl (for example \'rate_us.tpl\')</li>
<li>Change content of the page with your favorite html editor</li>
<li>Upload this file to your server into \'';
  echo 'tmpl/custom\' directory</li>
<li>Check result - ';
  echo $settings['site_url'];
  echo '/?a=cust&page=[your_document_name] <br>Example: <a href=';
  echo $settings['site_url'];
  echo '/?a=cust&page=rate_us target=_blank>';
  echo $settings['site_url'];
  echo '/?a=cust&page=rate_us</a></li>
<li>Add this link to the top menu (edit \'tmpl/logo.tpl\' file) or to the left menu 
  (edit \'tmpl/left.tpl\' file)</li>


';
?>
