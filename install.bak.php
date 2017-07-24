<?php

function main()
{
    global $frm_env;
    global $settingsFile;
    if (!is_writeable($settingsFile)) {
        echo '<br><br><br><br><center><h1>Please set the 666 permissions for the <b>'.$settingsFile.'</b> file!<br>';
        exit();
    }

    if (!is_dir('tmpl_c')) {
        echo '<br><br><br><br><center><h1>Please create a directory <b>tmpl_c</b> with 777 permissions!<br>';
        exit();
    }

    if (!is_dir('tmpl_c')) {
        echo '<br><br><br><br><center><h1>Please create the <b>tmpl_c</b> directory with 777 permissions!<br>';
        exit();
    }

    $file = @fopen('tmpl_c/test', 'w');
    if (!$file) {
        echo '<br><br><br><br><center><h1>Please set 777 permissions for the <b>tmpl_c</b> folder!<br>';
        exit();
    }
    require '../smarty/Smarty.class.php';
    $smarty = new Smarty();
    $smarty->compile_check = true;
    $smarty->template_dir = TMPL_PATH;
    $smarty->compile_dir = './tmpl_c';
    $smarty->assign('hostname', $frm_env['HTTP_HOST']);
    $smarty->assign('install', 1);
    $smarty->display('install.tpl');
    exit();
}

include 'lib/config.inc.php';
if ($frm['a'] == 'install') {
    $ok = 1;
    require '../smarty/Smarty.class.php';
    $smarty = new Smarty();
    $smarty->compile_check = true;
    $smarty->template_dir = TMPL_PATH;
    $smarty->compile_dir = './tmpl_c';
    $smarty->assign('form_data', $frm);
    $settings['license'] = $frm['license_string'];

    $ok = 1;
    if ($ok == 1) {
        $dbconn = @mysql_connect($frm['mysql_host'], $frm['mysql_username'], $frm['mysql_password']);
        $c = @mysql_select_db($frm['mysql_db']);
        if (!$c) {
            $smarty->assign('wrong_mysql_data', 1);
            $ok = 0;
        }
    }

    if ($ok == 1) {
        $q = 'DROP TABLE IF EXISTS hm2_deposits';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_deposits (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL default \'0\',
  type_id bigint(20) NOT NULL default \'0\',
  deposit_date datetime NOT NULL default \'2017-01-01 00:00:00\',
  last_pay_date datetime NOT NULL default \'2017-01-01 00:00:00\',
  status enum(\'on\',\'off\') default \'on\',
  q_pays bigint(20) NOT NULL default \'0\',
  amount double(10,5) NOT NULL default \'0.00\',
  actual_amount double(10,5) NOT NULL default \'0.00\',
  ec int not null,
  compound float(10, 5),
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_emails';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_emails (
  id varchar(50) NOT NULL default \'\',
  name varchar(255) NOT NULL default \'\',
  subject varchar(255) NOT NULL default \'\',
  text text,
  status TINYINT(1)  UNSIGNED DEFAULT 1 NOT NULL,
  UNIQUE KEY id (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES (\'registration\',\'Registration Completetion\',\'Registration Info\',\'Hello #name#,

Thank you for registration on our site.

Your login information:

Login: #username#
Password: #password#

You can login here: #site_url#

Contact us immediately if you did not authorize this registration.

Thank you.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES (\'confirm_registration\',\'Registration Confirmation\',\'Confirm your registration\',\'Hello #name#,

Thank you for registering in our program
Please confirm your registration or ignore this message.

Copy and paste this link to your browser:
#site_url#/?a=confirm_registration&c=#confirm_string#

Thank you.
#site_name#\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES (\'forgot_password\',\'Password Reminder\',\'The password you requested\',\'Hello #name#,

Someone (most likely you) requested your username and password from the IP #ip#.
Your password has been changed!!!

You can log into our account with:

Username: #username#
Password: #password#

Hope that helps.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES (\'bonus\',\'Bonus Notification\',\'Bonus Notification\',\'Hello #name#,

You received a bonus: $#amount#
You can check your statistics here:
#site_url#

Good luck.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES (\'penalty\',\'Penalty Notification\',\'Penalty Notification\',\'Hello #name#,

Your account has been charged for $#amount#
You can check your statistics here:
#site_url#

Good luck.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES (\'change_account\',\'Account Change Notification\',\'Account Change Notification\',\'Hello #name#,

Your account data has been changed from ip #ip#


New information:

Password: #password#
E-gold account: #egold#
E-mail address: #email#

Contact us immediately if you did not authorize this change.

Thank you.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'withdraw_request_user_notification\', \'User Withdrawal Request Notification\', \'Withdrawal Request has been sent\', \'Hello #name#,


You has requested to withdraw $#amount#.
Request IP address is #ip#.


Thank you.
#site_name#
#site_url#\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'withdraw_request_admin_notification\', \'Administrator Withdrawal Request Notification\', \'Withdrawal Request has been sent\', \'User #username# requested to withdraw $#amount# from IP #ip#.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'withdraw_user_notification\', \'User Withdrawal Notification\', \'Withdrawal has been sent\', \'Hello #name#.

$#amount# has been successfully sent to your #currency# account #account#.
Transaction batch is #batch#.

#site_name#
#site_url#\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'withdraw_admin_notification\', \'Administrator Withdrawal Notification\', \'Withdrawal has been sent\', \'User #username# received $#amount# to #currency# account #account#. Batch is #batch#.\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'deposit_admin_notification\', \'Administrator Deposit Notification\', \'A deposit has been processed\', \'User #username# deposit $#amount# #currency# to #plan#.

Account: #account#
Batch: #batch#
Compound: #compound#%.
Referrers fee: $#ref_sum#\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'deposit_user_notification\', \'Deposit User Notification\', \'Payment received\', \'Dear #name# (#username#)

We have successfully recived your deposit $#amount# #currency# to #plan#.

Your Account: #account#
Batch: #batch#
Compound: #compound#%.


Thank you.
#site_name#
#site_url#\', \'1\')';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'exchange_admin_notification\', \'Exchange Admin Notification\', \'Currency Exchange Processed\', \'User #username# has exchanged $#amount_from# #currency_from# to $#amount_to# #currency_to#.\', \'0\')';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = ''.'INSERT INTO hm2_emails VALUES(\'exchange_user_notification\', \'Exchange User Notification\', \'Currency Exchange Completed\', \'Dear #name# (#username#).

You have successfully exchanged $#amount_from# #currency_from# to $#amount_to# #currency_to#.

Thank you.
#site_name#
#site_url#\', \'0\')';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES(\'brute_force_activation\', \'Account Activation after Brute Force\', \'#site_name# - Your account activation code.\', \'Someone from IP #ip# has entered a password for your account "#username#" incorrectly #max_tries# times. System locked your accout until you activate it.

Click here to activate your account :

#site_url#?a=activate&code=#activation_code#

Thank you.
#site_name#\', 1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES("direct_signup_notification", "Direct Referral Signup", "You have a new direct signup on #site_name#", "Dear #name# (#username#)\\n\\nYou have a new direct signup on #site_name#\\nUser: #ref_username#\\nName: #ref_name#\\nE-mail: #ref_email#\\n\\nThank you.", "1")';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES("referral_commision_notification", "Referral Comission Notification", "#site_name# Referral Comission", "Dear #name# (#username#)\\n\\nYou have recived a referral comission of $#amount# #currency# from the #ref_name# (#ref_username#) deposit.\\n\\nThank you.", "1")';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES("pending_deposit_admin_notification", "Deposit Request Admin Notification", "Deposit Request Notification", "User #username# save deposit $#amount# of #currency# to #plan#.\\n\\n#fields#", "1")';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES("deposit_approved_admin_notification", "Deposit Approved Admin Notification", "Deposit has been approved", "Deposit has been approved:\\n\\nUser: #username# (#name#)\\nAmount: $#amount# of #currency#\\nPlan: #plan#\\nDate: #deposit_date#\\n#fields#", "1")';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES("deposit_approved_user_notification", "Deposit Approved User Notification", "Deposit has been approved", "Dear #name#\\n\\nYour deposit has been approved:\\n\\nAmount: $#amount# of #currency#\\nPlan: #plan#\\n#fields#", "1")';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_emails VALUES("account_update_confirmation", "Account Update Confirmation", "Account Update Confirmation", "Dear #name# (#username#),\\n\\nSomeone from IP address #ip# (most likely you) is trying to change your account data.\\n\\nTo confirm these changes please use this Confirmation Code:\\n#confirmation_code#\\n\\nThank you.\\n#site_name#\\n#site_url#", "1")';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_history';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_history (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL default \'0\',
  amount float(10,5) default NULL,
  type enum(\'deposit\',\'bonus\',\'penality\',\'earning\',\'withdrawal\',\'commissions\',\'early_deposit_release\',\'early_deposit_charge\',\'release_deposit\',\'add_funds\',\'withdraw_pending\',\'exchange_in\',\'exchange_out\',\'internal_transaction_spend\',\'internal_transaction_receive\') default NULL,
  description text NOT NULL,
  actual_amount float(10,5) default NULL,
  date datetime NOT NULL default \'2017-01-01 00:00:00\',
  str varchar(40) NOT NULL default \'\',
  ec int not null,
  deposit_id BIGINT(20) not null default 0,
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_online';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_online (
  ip varchar(15) NOT NULL default \'\',
  date datetime NOT NULL default \'2017-01-01 00:00:00\'
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_pay_errors';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_pay_errors (
  id bigint(20) NOT NULL auto_increment,
  date datetime NOT NULL default \'2017-01-01 00:00:00\',
  txt text NOT NULL,
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_pay_settings';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_pay_settings (
  n varchar(200) NOT NULL default \'\',
  v text NOT NULL
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_pay_settings VALUES (\'egold_account_password\',\'\')';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_plans';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_plans (
  id bigint(20) NOT NULL auto_increment,
  name varchar(250) default NULL,
  description text,
  min_deposit float(10,2) default NULL,
  max_deposit float(10,2) default NULL,
  percent float(10,2) default NULL,
  status enum(\'on\',\'off\') default NULL,
  parent bigint(20) NOT NULL default \'0\',
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_settings';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_settings (
  name varchar(200) NOT NULL default \'\',
  `value` text NOT NULL
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (1,\'Plan 1\',NULL,0.00,100.00,2.20,NULL,1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (2,\'Plan 2\',NULL,101.00,1000.00,2.30,NULL,1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (3,\'Plan 3\',NULL,1001.00,0.00,2.40,NULL,1)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (4,\'Plan 1\',NULL,10.00,100.00,3.20,NULL,2)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (5,\'Plan 2\',NULL,101.00,1000.00,3.30,NULL,2)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (6,\'Plan 3\',NULL,1001.00,5000.00,3.40,NULL,2)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (7,\'Plan 1\',NULL,10.00,100.00,10.00,NULL,3)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (8,\'Plan 2\',NULL,101.00,1000.00,20.00,NULL,3)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_plans VALUES (9,\'Plan 3\',NULL,1001.00,0.00,50.00,NULL,3)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_types';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_types (
  id bigint(20) NOT NULL auto_increment,
  name varchar(250) default NULL,
  description text,
  q_days bigint(20) default NULL,
  min_deposit float(10,2) default NULL,
  max_deposit float(10,2) default NULL,
  period enum(\'d\',\'w\',\'b-w\',\'m\',\'2m\',\'3m\',\'6m\',\'y\',\'end\') default NULL,
  status enum(\'on\',\'off\',\'suspended\') default NULL,
  return_profit enum(\'0\',\'1\') default NULL,
  return_profit_percent float(10,2) default NULL,
  percent float(10,2) default NULL,
  pay_to_egold_directly int(11) NOT NULL default \'0\',
  use_compound int not null,
  work_week int not null,
  parent int not null,
  withdraw_principal TINYINT(1)  UNSIGNED DEFAULT \'0\' NOT NULL,
  withdraw_principal_percent DOUBLE(10,2)  DEFAULT \'0\' NOT NULL,
  withdraw_principal_duration INT UNSIGNED DEFAULT \'0\' NOT NULL,
  compound_min_deposit DOUBLE(10,2)  DEFAULT \'0\',
  compound_max_deposit DOUBLE(10,2)  DEFAULT \'0\',
  compound_percents_type TINYINT(1)  UNSIGNED DEFAULT \'0\',
  compound_min_percent DOUBLE(10,2)  DEFAULT \'0\',
  compound_max_percent DOUBLE(10,2)  DEFAULT \'100\',
  compound_percents TEXT,
  closed TINYINT(1)  UNSIGNED DEFAULT \'0\' NOT NULL,
  withdraw_principal_duration_max INT UNSIGNED DEFAULT \'0\' NOT NULL,
  dsc text,
  hold int not null,
  delay int not null,
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_types VALUES (1,\'1 year 2.4% daily\',NULL,365,NULL,NULL,\'d\',\'on\',\'0\',0.00,NULL,0,0,0,0,0,0,0, 0, 0, 0, 0, 100, \'\', 0, 0, \'\', 0, 0)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_types VALUES (2,\'100 days 3.4% daily\',NULL,365,NULL,NULL,\'d\',\'on\',\'0\',0.00,NULL,0,0,0,0,0,0,0, 0, 0, 0, 0, 100, \'\', 0, 0, \'\', 0, 0)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_types VALUES (3,\'30 days deposit. 150%\',NULL,30,NULL,NULL,\'end\',\'on\',\'1\',0.00,NULL,0,0,0,0,0,0,0, 0, 0, 0, 0, 100, \'\', 0, 0, \'\', 0, 0)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_user_access_log';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_user_access_log (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) NOT NULL default \'0\',
  date datetime default NULL,
  ip varchar(15) NOT NULL default \'\',
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_users';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_users (
  id bigint(20) NOT NULL auto_increment,
  name varchar(200) default NULL,
  username varchar(20) default NULL,
  password varchar(50) default NULL,
  date_register datetime default NULL,
  egold_account bigint(20) NOT NULL default \'0\',
  perfectmoney_account varchar(200) NOT NULL default \'\',
  email varchar(200) default NULL,
  status enum(\'on\',\'off\',\'suspended\') default NULL,
  came_from text NOT NULL,
  ref bigint(20) NOT NULL default \'0\',
  deposit_total float(10,2) NOT NULL default \'0.00\',
  confirm_string varchar(200) NOT NULL default \'\',
  ip_reg varchar(15) NOT NULL default \'\',
  last_access_time datetime NOT NULL default \'2017-01-01 00:00:00\',
  last_access_ip varchar(15) NOT NULL default \'\',
  stat_password varchar(200) not null,
  auto_withdraw int(11) NOT NULL default \'1\',
  user_auto_pay_earning int not null,
  admin_auto_pay_earning int not null,
  pswd varchar(50) not null,
  evocash_account bigint(20) NOT NULL default \'0\',
  intgold_account bigint(20) NOT NULL default \'0\',
  hid varchar(50) not null,
  question varchar(50) not null default \'\',
  answer varchar(50) not null default \'\',
  l_e_t datetime not null default \'20017-01-01\',
  activation_code VARCHAR(50)  NOT NULL,
  bf_counter TINYINT UNSIGNED DEFAULT \'0\' NOT NULL,
  address VARCHAR(255),
  city VARCHAR(255),
  state VARCHAR(255),
  zip VARCHAR(255),
  country VARCHAR(255),
  transaction_code VARCHAR(255),
  stormpay_account varchar(200) not null,
  ebullion_account varchar(200) not null,
  paypal_account varchar(200) not null,
  goldmoney_account varchar(200) not null,
  eeecurrency_account bigint(20) NOT NULL default \'0\',
  pecunix_account bigint(20) NOT NULL default \'0\',
  ac text not null,
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_referal';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_referal (
  id bigint(20) NOT NULL auto_increment,
  level bigint(20) NOT NULL default \'0\',
  name varchar(200) default NULL,
  from_value bigint(20) NOT NULL default \'0\',
  to_value bigint(20) NOT NULL default \'0\',
  percent double(10,2) default NULL,
  percent_daily double (10,2),
  percent_weekly double (10,2),
  percent_monthly double (10, 2),
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_referal VALUES (1,1,\'Level A\',1,2,2.00,null,null,null)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_referal VALUES (2,1,\'Level B\',3,5,3.00,null,null,null)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_referal VALUES (3,1,\'Level C\',6,10,5.00,null,null,null)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_referal VALUES (4,1,\'Level D\',11,20,7.50,null,null,null)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'INSERT INTO hm2_referal VALUES (5,1,\'Level E\',21,0,10.00,null,null,null)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_referal_stats';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'create table hm2_referal_stats (
  date date not null,
  user_id bigint not null,
  income bigint not null,
  reg bigint not null
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_news';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_news (
  id bigint(20) NOT NULL auto_increment,
  date datetime,
  title varchar(255),
  small_text text,
  full_text text,
  PRIMARY KEY  (id)
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_settings';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_settings (
  name varchar(200) NOT NULL default \'\',
  `value` text NOT NULL
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_wires';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'create table hm2_wires (
  id bigint not null auto_increment primary key,
  user_id bigint not null,
  pname varchar(250) not null,
  paddress varchar(250) not null,
  pzip varchar(250) not null,
  pcity varchar(250) not null,
  pstate varchar(250) not null,
  pcountry varchar(250) not null,
  bname varchar(250) not null,
  baddress varchar(250) not null,
  bzip varchar(250) not null,
  bcity varchar(250) not null,
  bstate varchar(250) not null,
  bcountry varchar(250) not null,
  baccount varchar(250) not null,
  biban varchar(250) not null,
  bswift varchar(250) not null,
  amount float(10,5),
  type_id bigint ,
  wire_date datetime not null,
  compound float(10, 5),
  status enum(\'new\',\'problem\',\'processed\')
)';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_exchange_rates';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_exchange_rates (
  `sfrom` int(10) unsigned default NULL,
  `sto` int(10) unsigned default NULL,
  `percent` float(3,2) default \'0.00\')';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $q = 'DROP TABLE IF EXISTS hm2_pending_deposits';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_pending_deposits (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `ec` bigint(20) unsigned default NULL,
  `fields` text,
  `user_id` bigint(20) unsigned NOT NULL default \'0\',
  `amount` float(10,5) NOT NULL default \'0.00000\',
  `type_id` bigint(20) unsigned NOT NULL default \'0\',
  `date` datetime NOT NULL default \'2017-01-01 00:00:00\',
  `status` enum(\'new\',\'problem\',\'processed\') NOT NULL default \'new\',
  `compound` double(10,5) NOT NULL default \'0.00000\',
  PRIMARY KEY  (`id`)
)';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'DROP TABLE IF EXISTS hm2_processings';
        db_query($q) or print_mysql_error().', line:'.__LINE__;
        $q = 'CREATE TABLE hm2_processings (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `infofields` text,
  `status` tinyint(1) unsigned NOT NULL default \'1\',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
)';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("999", "Bank Wire", "a:3:{i:1;s:9:\\"Bank Name\\";i:2;s:12:\\"Account Name\\";i:3;s:15:\\"Payment Details\\";}", "0", "Send your bank wires here:<br>\\r\\nBeneficiary\'s Bank Name: <b>Your Bank Name</b><br>\\r\\nBeneficiary\'s Bank SWIFT code: <b>Your Bank SWIFT code</b><br>\\r\\nBeneficiary\'s Bank Address: <b>Your Bank address</b><br>\\r\\nBeneficiary Account: <b>Your Account</b><br>\\r\\nBeneficiary Name: <b>Your Name</b><br>\\r\\n\\r\\nCorrespondent Bank Name: <b>Your Bank Name</b><br>\\r\\nCorrespondent Bank Address: <b>Your Bank Address</b><br>\\r\\nCorrespondent Bank codes: <b>Your Bank codes</b><br>\\r\\nABA: <b>Your ABA</b><br>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("1000", "e-Bullion", "a:2:{i:1;s:13:\\"Payer Account\\";i:2;s:14:\\"Transaction ID\\";}", "0", "Please send your payments to this account: <b>Your e-Bullion account</b>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("1001", "NetPay", "a:2:{i:1;s:13:\\"Payer Account\\";i:2;s:14:\\"Transaction ID\\";}", "0", "Send your funds to account: <b>Your NetPay account</b>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("1002", "GoldMoney", "a:2:{i:1;s:13:\\"Payer Account\\";i:2;s:14:\\"Transaction ID\\";}", "0", "Send your fund to account: <b>your GoldMoney account</b>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("1003", "MoneyBookers", "a:2:{i:1;s:13:\\"Payer Account\\";i:2;s:14:\\"Transaction ID\\";}", "0", "Send your funds to account: <b>your MoneyBookers account</b>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("1004", "Pecunix", "a:2:{i:1;s:19:\\"Your e-mail address\\";i:2;s:16:\\"Reference Number\\";}", "0", "Send your funds to account: <b>your Pecunix account</b>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $q = 'INSERT INTO hm2_processings VALUES("1005", "PicPay", "a:2:{i:1;s:13:\\"Payer Account\\";i:2;s:14:\\"Transaction ID\\";}", "0", "Send your funds to account: <b>Your PicPay account</b>")';
        (db_query($q) or print mysql_error().', line:'.__LINE__.'<br>');
        $admin_email = quote($frm['admin_email']);
        $admin_password = md5($frm['admin_password']);
        $q = ''.'INSERT INTO hm2_users VALUES (1,\'admin name\',\'admin\',\''.$admin_password.'\',NULL,0, \'\',\''.$admin_email.'\',\'on\',\'     \',0,0.00,\'\',\'\',now(),\'localhost\', \'\', 1, 0, 0, \'\', 0, 0, \'\', \'\', \'\', \'20017-01-01\', \'\', 0, \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\', \'\',\'\',0, 0, \'\')';
        db_query($q) or print mysql_error().', line:'.__LINE__.'<br>';
        $settings['site_name'] = $frm_env['HTTP_HOST'];
        $settings['hostname'] = $frm['mysql_host'];
        $settings['database'] = $frm['mysql_db'];
        $settings['db_login'] = $frm['mysql_username'];
        $settings['db_pass'] = $frm['mysql_password'];
        $settings['opt_in_email'] = $frm['admin_email'];
        $settings['system_email'] = $frm['admin_email'];
        $mddomain = $frm_env['HTTP_HOST'];
        $mddomain = preg_replace('/^www\\./', '', $mddomain);
        $mdscriptname = $frm_env['SCRIPT_NAME'];
        $mdscriptname = preg_replace('/install\\.php/', '', $mdscriptname);
        $settings['key'] = strtoupper(get_rand_md5(100).md5($mddomain.'asdfds89ufsdkfnsjfdksh').md5($mdscriptname.'8hbfnbdnf').md5('grv'.$mddomain).get_rand_md5(200));
        save_settings();
        define('THE_GC_SCRIPT_V2005_04_01', 'answer');
        $acsent_settings = [];
        $acsent_settings[detect_ip] = 'disabled';
        $acsent_settings[detect_browser] = 'disabled';
        $acsent_settings[email] = $frm['admin_email'];
        $acsent_settings[last_browser] = $frm['admin_email'];
        $acsent_settings[last_ip] = $frm['admin_email'];
        $acsent_settings[pin] = '';
        $acsent_settings[timestamp] = 0;
        set_accsent();
        $smarty->assign('script_path', $settings['site_url']);
        $smarty->assign('installed', 1);
    }

    $smarty->assign('hostname', $frm_env['HTTP_HOST']);
    $smarty->assign('install', 1);
    $smarty->display('install.tpl');
    exit();
}
main();
