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

// clean function
function clean( $data ) {
   $data = trim( htmlentities( strip_tags( $data ), ENT_QUOTES ) );

   if( get_magic_quotes_gpc() )
      $data = stripslashes( $data );

   $data = addslashes( $data );

   return $data;
}

// automatically clean inputs
foreach( $_GET as $index => $value ) {
   $_GET[$index] = clean( $value );
}
foreach( $_POST as $index => $value ) {
   if( is_array( $value ) ) {
      foreach( $value as $i => $v ) {
         $value[$i] = clean( $v );
      }
      $_POST[$index] = $value;
   } else
      $_POST[$index] = clean( $value );
}
foreach( $_COOKIE as $index => $value ) {
   $_COOKIE[$index] = clean( $value );
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title> Oh No! (the trouble checking script) </title>
<meta name="author" content="Angela Maria Protacia M. Sabas" />
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<div id="menu">

<img src="header.gif" width="139" height="64" alt="" border="0" class="logo" />

<ul>
   <li><a href="admin_home.php">Home</a></li>
   <li><a href="admin_home.php?action=add">New</a></li>
   <li><a href="admin_home.php?action=generate">Generate List</a></li>
   <li><a href="admin_home.php?action=reset" onclick="go=confirm('Are you sure you want to reset all entries?'); return go;">Reset All</a></li>
   <li><a href="admin_home.php?action=deleteall" onclick="go=confirm('Are you sure you want to delete all entries?'); return go;">Delete All</a></li>
   <li><a href="admin_home.php?action=status">Manage Statuses</a>
   <li><a href="admin_logout.php">Logout</a></li>
</ul>

<div class="version">
   OhNo! <?php include 'version.inc.php'; ?>
</div>

</div>

<div id="contents">

<div style="font-weight: bold; font-size: 10pt; padding-bottom: 10px;">
<?php
$show = ( isset( $_GET['action'] ) ) ? $_GET['action'] : '';
$show = ( isset( $_POST['action'] ) ) ? $_POST['action'] : $show;
switch( $show ) {
	case 'add' : echo '<h1>New troubled entry</h1>'; break;
	case 'edit' : echo '<h1>Edit troubled entry</h1>'; break;
	case 'generate' : echo '<h1>Generate troubles list</h1>'; break;
	case 'reset' : echo '<h1>Reset all entries</h1>'; break;
	case 'status' : echo '<h1>Manage Statuses</h1>'; break;
	case 'deleteall' : echo '<h1>Delete all entries</h1>'; break;
	default : break;
}
?>
</div>