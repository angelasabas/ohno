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
require_once( 'config.inc.php' );
if( !isset( $_SESSION["message"] ) )
   $_SESSION["message"] = '';
if( isset( $_COOKIE["ohno_login_" . $_SERVER['SERVER_NAME']] ) &&
   $_COOKIE["ohno_login_" . $_SERVER['SERVER_NAME']] == md5( $set_password . 'OHNO' ) ) {
   header( 'Location: admin_home.php' );
   die( 'Redirecting you...' );
   }

require_once( 'header.inc.php' );
?>

<p><span class="important"><?= htmlentities( $_SESSION["message"] ) ?></span></p>

<form action="admin_login.php" method="post">

<p>Please log in:</p>

<fieldset>
   <legend>Login</legend>
   
   <label for="login_password" id="labelPassword">Password</label>
   <input type="password" id="login_password" name="login_password" />
   <input type="checkbox" id="rememberme" name="rememberme" value="yes" />
   <label for="rememberme" id="labelRememberMe">Remember me?</label>
   
   <input type="submit" id="submit" value="Log in" />
   
</fieldset>

</form>

<?php
require_once( 'footer.inc.php' );
?>