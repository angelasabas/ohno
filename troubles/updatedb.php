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
require_once( 'header.inc.php' );
require_once( 'config.inc.php' );
?>

<?php

if( isset( $_GET["done"] ) ) {
	$query = 'TRUNCATE TABLE ' . $status_table . '; ';
	$query .= 'INSERT INTO `' . $status_table . '` VALUES (1, \'Dead link: 404 error\'); INSERT INTO `' . $status_table . '` VALUES (2, \'Dead link: 403 error\'); INSERT INTO `' . $status_table . '` VALUES (3, \'Dead link: site is not the fanlisting\'); INSERT INTO `' . $status_table . '` VALUES (4, \'Dead link: other\'); INSERT INTO `' . $status_table . '` VALUES (5, \'Navigation problem: Broken links to essential pages\'); INSERT INTO `' . $status_table . '` VALUES (6, \'Navigation problem: broken image map\'); INSERT INTO `' . $status_table . '` VALUES (7, \'Navigation problem: difficult/confusing\'); INSERT INTO `' . $status_table . '` VALUES (8, \'Navigation problem: other\'); INSERT INTO `' . $status_table . '` VALUES (9, \'Updating: no update for two months\'); INSERT INTO `' . $status_table . '` VALUES (10, \'Updating: No "last updated" date\'); INSERT INTO `' . $status_table . '` VALUES (11, \'Updating: Auto-add with neglect\'); INSERT INTO `' . $status_table . '` VALUES (12, \'Updating: JavaScript update date\'); INSERT INTO `' . $status_table . '` VALUES (13, \'Updating: other\'); INSERT INTO `' . $status_table . '` VALUES (14, \'TFL Link: no link\'); INSERT INTO `' . $status_table . '` VALUES (15, \'TFL Link: barely visible/hidden\'); INSERT INTO `' . $status_table . '` VALUES (16, \'TFL Link: outdated\'); INSERT INTO `' . $status_table . '` VALUES (17, \'TFL Link: opens within a frame\'); INSERT INTO `' . $status_table . '` VALUES (18, \'TFL Link: other\'); INSERT INTO `' . $status_table . '` VALUES (19, \'Rule-breaking: not asking for countries\'); INSERT INTO `' . $status_table . '` VALUES (20, \'Rule-breaking: not listing countries\'); INSERT INTO `' . $status_table . '` VALUES (21, \'Rule-breaking: requiring other information\'); INSERT INTO `' . $status_table . '` VALUES (22, \'Rule-breaking: rules that prohibit certain people from joining\'); INSERT INTO `' . $status_table . '` VALUES (23, \'Rule-breaking: badly-worded rules\'); INSERT INTO `' . $status_table . '` VALUES (24, \'Rule-breaking: other\'); INSERT INTO `' . $status_table . '` VALUES (25, \'Hiatus: date of return has passed significantly\'); INSERT INTO `' . $status_table . '` VALUES (26, \'Hiatus: no set date of return\'); INSERT INTO `' . $status_table . '` VALUES (27, \'Hiatus: other\'); INSERT INTO `' . $status_table . '` VALUES (28, \'Other\');';

	$queries = explode( ';', $query );

	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the MySQL server: ' .
			mysql_error() );
	mysql_select_db( $db_database )
		or die( 'Cannot select database: ' . mysql_error() );
	foreach( $queries as $q )
		if( $q )
			mysql_query( $q )
				or die( 'Cannot execute query: ' .
				mysql_error() );

	echo '<p><b>Database tables updated successfully.</b></p>';

	}
else {
?>

	<p>
	If you are upgrading from 2.0 to 2.1, you MUST run this script
	to update your troubles status tables.
	</p>

	<form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
	<input type="hidden" name="done" />
	<input type="submit" value="Update my Tables" />
	</form>

<?php
	}
require_once( 'footer.inc.php' );
?>