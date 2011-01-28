<?php
/*****************************************************************************
 Ohno: The Trouble Checking Script
 Copyright (c) by Angela Sabas
 http://scripts.indisguise.org

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 For more information please view the readme.txt file.
******************************************************************************/
session_start();
$_SESSION["message"] = '';
require_once( 'config.inc.php' );

if( isset( $_POST["login_password"] ) && $_POST["login_password"] != '' )
   $login_password = trim( htmlentities( $_POST["login_password"] ) );
else {
   $_SESSION["message"] = 'You must enter your password below to log into ' .
      'the system.';
   header( 'location: index.php' );
   die( 'Redirecting you...' );
}

if( $login_password != $set_password ) {
   $_SESSION["message"] = 'Your password does not match ' .
      'the previously set administrator password. Please try again.';
   header( 'location: index.php' );
   die( 'Redirecting you...' );
}
else {
   $cookie_name = str_replace( '.', '_', "ohno_login_" . $_SERVER['SERVER_NAME'] );
   session_regenerate_id();
   if( isset( $_REQUEST["rememberme"] ) &&
      $_REQUEST["rememberme"] == 'yes' ) {
      $success = setcookie( $cookie_name, md5( $login_password . 'OHNO' ),
         time()+60*60*24*30 );
   }
   else
      $success = setcookie( $cookie_name, md5( $login_password . 'OHNO' ) );
   if( $success ) {
      header( 'location: admin_home.php' );
      die( 'Redirecting you...' );
   }
   else {
?>
      <p>
      Login successful. <a href="admin_home.php">Continue...</a>
      </p>
<?php
   }
}
?>