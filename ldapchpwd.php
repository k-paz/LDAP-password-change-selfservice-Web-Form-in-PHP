<?php
/**
* LDAP password change selfservice Web Form in PHP
* Copyright (c) 2015 - 2016 Krzyszof Paz. GNU GPL v3
* @category  ldapchpwd.php
* @package   ldapchpwd.php
* @author    Krzysztof Paz
* @copyright 2016 (C) Krzysztof Paz. GNU General Public License, version 3 (GPL-3.0).
* @license   https://opensource.org/licenses/GPL-3.0
* @version   Release: 1.0
* @webpage   https://github.com/k-paz/LDAP-password-change-selfservice-Web-Form-in-PHP
* @link      https://github.com/k-paz/LDAP-password-change-selfservice-Web-Form-in-PHP
* 
* Forked from:	     http://technology.mattrude.com/2010/11/ldap-php-change-password-webpage/
* Initial credits:   Matt Rude <http://mattrude.com>
* 
* My changes over Matt's version:
* + custom LDAP port definition added, 
* + salting and hashing for SHA512 encoded passwords added, 
* + code simplified only to LDAP password change - mail&search sideprocedures removed,
* + updated the LDAP password change code for making it work with Apache Directory Server, 
* + full page background image (bg.jpg) support added and form is centered by the HTML/CSS part.
*
* Recommendation: 	Use this page with the HTTPS/SSL/443 connections!
* Warning:	Regardless of HTTPS, default LDAP ports like 389, 10389, etc. usually runs unencryped.
* ToDo:		Adding smooth support for the LDAPS/SSL ports like 636, 10636,etc. would be nice to have.
* 
* This code is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3.0 of the License, or (at your option) any later version.
*
* This code is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*/

$message = array();
$message_css = "";
 
function changePassword($user,$oldPassword,$newPassword,$newPasswordCnf){
  global $message;
  global $message_css;

/* SET YOUR LDAP SERVER DETAILS HERE */
  $server = "192.168.1.1";
  $lport = 10389;
  $dn = "dc=yourcompany,dc=domain,dc=com";

/* The implementation */
  error_reporting(0);
  $con = ldap_connect($server, $lport);
  ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
  ldap_set_option($con, LDAP_OPT_REFERRALS, 0);

  $user_dn = "cn=".$user.",".$dn;
  //$message[] = "Username: " . $user;
  //$message[] = "DN: " . $user_dn;
  //$message[] = "Current Pass: " . $oldPassword;
  //$message[] = "New Pass: " . $newPassword;
   
  /* Examples for testing some conditions */
  if ($user === "demo" || $user === "administrator") {
    $message[] = "Error E100 - You are not allowed to change the password.";
    return false;
  }
  if (ldap_bind($con, $user_dn, $oldPassword) === false) {
    $message[] = "Error E101 - Username or OLD password is wrong.";
    return false;
  }
  if ($newPassword != $newPasswordCnf ) {
    $message[] = "Error E102 - Your NEW passwords do not match!";
    return false;
  }
  if (strlen($newPassword) < 8 ) {
    $message[] = "Error E103 - Your new password is too short.<br/>Your password must be at least 8 characters long.";
    return false;
  }

/* And Finally, Change The Password */
  $salt = uniqid(mt_rand(), true);
  $userdata["userpassword"][0] = "{ssha512}".base64_encode(hash('sha512', $newPassword.$salt, true).$salt);             
  if (ldap_modify($con,$user_dn,$userdata) === false){
    $error = ldap_error($con);
    $errno = ldap_errno($con);
    $message[] = "E201 - Your password cannot be changed, please contact the administrator.";
    $message[] = "$errno - $error";
  } else {
    $message_css = "yes";
    $message[] = "The password for $user has been changed.<br/>Your new password is now fully Active.";
  }
}
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>LDAP Password Change</title>

<style type="text/css">
body { font-family: Verdana,Arial,Courier New; font-size: 0.7em; background-image: url(bg.jpg); background-repeat:no-repeat; background-position: center center fixed; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; }
th { text-align: right; padding: 0.8em; }

.outer {
    display: table;
    position: absolute;
    height: 70%;
    width: 98%;
}

.middle {
    display: table-cell;
    vertical-align: middle;
}

.inner {
    margin-left: auto;
    margin-right: auto; 
    width: 80%;
}
#container { text-align: center; width: 600px; margin: 1% auto; }
.msg_yes { margin: 0 auto; text-align: center; color: green; background: #D4EAD4; border: 1px solid green; border-radius: 10px; margin: 2px; }
.msg_no { margin: 0 auto; text-align: center; color: red; background: #FFF0F0; border: 1px solid red; border-radius: 10px; margin: 2px; }
</style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

<div class="outer">
<div class="middle">
<div class="inner">

<div id="container">
<h1>Password Change</h1>
<p>Your new password must be at least 8 characters long or longer.<br/>
</p>
<br>
<?php
      if (isset($_POST["submitted"])) {
        changePassword($_POST['username'],$_POST['oldPassword'],$_POST['newPassword1'],$_POST['newPassword2']);
        global $message_css;
        if ($message_css == "yes") {
          ?><div class="msg_yes"><?php
         } else {
          ?><div class="msg_no"><?php
          $message[] = "Your password was not changed.";
        }
        foreach ( $message as $one ) { echo "<p>$one</p>"; }
      ?></div><?php
      } ?>

<form action="<?php print $_SERVER['PHP_SELF']; ?>" name="passwordChange" method="post">
<table style="width: 400px; margin: 0 auto;">
<tr><th>Your Username:</th><td><input name="username" type="text" size="20px" autocomplete="off" /></td></tr>
<tr><th>Current password:</th><td><input name="oldPassword" size="20px" type="password" /></td></tr>
<tr><th>New password:</th><td><input name="newPassword1" size="20px" type="password" /></td></tr>
<tr><th>New password (confirm):</th><td><input name="newPassword2" size="20px" type="password" /></td></tr>
<tr><th></th><td><br></td></tr>
<tr><td colspan="2" style="text-align: center;" >
<input name="submitted" type="submit" value="Change Password"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button onclick="$('frm').action='ldapchpwd.php';$('frm').submit();">Cancel</button>
</td></tr>
</table>
</form>
</div>

</div>
</div>
</div>

</body>
</html>
