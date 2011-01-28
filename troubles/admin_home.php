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
$cookie_name = str_replace( '.', '_', "ohno_login_" . $_SERVER['SERVER_NAME'] );
if( !isset( $_COOKIE[$cookie_name] ) ||
   $_COOKIE[$cookie_name] != md5( $set_password . 'OHNO' ) ) {
   $_SESSION["login_message"] = 'Please log in first before viewing ' .
      'anything.';
   header( 'location: index.php' );
   die( 'Redirecting you...' );
}
require_once( 'header.inc.php' );

$show_default = true;

if( isset( $_REQUEST["action"] ) && $_REQUEST["action"] != '' ) {
   $action = $_REQUEST["action"];
}


/**
 * Add 
 * ************************************************************************************************************
 */
if( isset( $action ) && $action == 'add' ) {
   $show_add_form = true;
   $show_default = false;
   $keepadding = false;

   if( isset( $_GET["done"] ) ) {

      if( $_GET["keepadding"] == 'yes' ) {
         $keepadding = true;
      }

      // check if required fields are present
      if( $_GET["url"] == '' || $_GET["subject"] == '' ) {

         echo '<p class="error">You must enter the entry url, and entry subject at least.</p>';

      }

      else {
         if( !isset( $_GET["status"] ) )
            $_GET["status"] = 0;

         // insert into table
         $query = "INSERT INTO `$troubles_table` VALUES( " .
            "'{$_GET['url']}', '{$_GET['subject']}', " .
            "'{$_GET['status']}', '{$_GET['comments']}', null )";

         $db_link = mysql_connect( $db_server, $db_user,
            $db_password )
            or die( 'Cannot connect to the database. Try again.' );
         mysql_select_db( $db_database )
            or die( 'Cannot connect to the database. Try again.' );
         $result = mysql_query( $query )
            or die( 'Error executing query: ' . mysql_error() );
         if( $result && mysql_affected_rows() > 0 ) {
            echo '<p class="success">Entry ' . $_GET["subject"] . ' inserted into the database successfully.</p>';
            if( !$keepadding ) {
               $show_add_form = false;
               $show_default = true;
            }
            else {
               $show_add_form = true;
               $show_default = false;
            }
         }
         else {
            echo '<p class="error">Error adding entry. Please try again.</p>';
         }

      } // end of if fields are present

   } // end of if get->done

   if( $show_add_form ) {
?>
      <p>You can add entries you check on at this page. Fill out the form below and click "Add".
      Red fields are required.</p>

      <form method="get" action="<?= $_SERVER["PHP_SELF"] ?>">
      <input type="hidden" name="action" value="add" />

      <table style="margin: auto;">

      <tr><td>
      Keep adding?
      </td><td>
<?php
      if( !$keepadding || $_GET["keepadding"] == 'no' ) {
?>
         <input type="radio" value="no" name="keepadding"
            checked="checked" /> No
         <input type="radio" value="yes" name="keepadding" />
            Yes
<?php
      }
      elseif( $keepadding || $_GET["keepadding"] == 'yes' ) {
?>
         <input type="radio" value="no" name="keepadding" /> No
         <input type="radio" value="yes" name="keepadding"
            checked="checked" /> Yes
<?php
      }
?>
      </td></tr>

      <tr class="important"><td>
      Website URL of the entry
      </td><td>
      <input type="text" name="url" />
      </td></tr>

      <tr class="important"><td>
      Subject of the entry
      </td><td>
      <input type="text" name="subject" />
      </td></tr>

      <tr><td>
      Status
      </td><td>
      <select name="status">
      <option value="0">No problem </option>
<?php
      $query = "SELECT * FROM `$status_table` ORDER BY `desc`";

      $db_link = mysql_connect( $db_server, $db_user, $db_password )
         or die( 'Cannot connect to the database. Try again.' );
      mysql_select_db( $db_database )
         or die( 'Cannot connect to the database. Try again.' );
      $result = mysql_query( $query )
         or die( 'Error executing query: ' . mysql_error() );

      while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {
         echo '<option value="' . $row["troublesid"] .
            '">' . $row["desc"] . '</option>';
      }

      mysql_free_result( $result );
      mysql_close( $db_link );
?>
      </select>
      </td></tr>

      <tr><td>
      Comments
      </td><td>
      <textarea name="comments" rows="3" cols="50"></textarea>
      </td></tr>

      <tr><td colspan="2">
      <input type="submit" value="Add the entry" name="done" />
      <input type="reset" value="Clear form" />
      </td></tr>

      </table>

      </form>

<?php
   }
}


/**
 * EDIT
 * ************************************************************************************************************
 */
elseif( isset( $action ) && $action == 'edit' ) {
   $show_edit_form = true;
   $show_default = false;

   if( isset( $_GET["done"] ) &&
      isset( $_GET["subject"] ) &&
      isset( $_GET["siteurl"] ) &&
      isset( $_GET["new_siteurl"] ) &&
      isset( $_GET["new_subject"] ) &&
      $_GET["subject"] != '' &&
      $_GET["new_subject"] != '' &&
      $_GET["siteurl"] != '' &&
      $_GET["new_siteurl"] != '' ) {

      $db_link = mysql_connect( $db_server, $db_user, $db_password )
         or die( 'Cannot connect to the database. Try again.' );
      mysql_select_db( $db_database )
         or die( 'Cannot connect to the database. Try again.' );

      if( !isset( $_GET["status"] ) )
         $_GET["status"] = 0;

      $query = "UPDATE `$troubles_table` SET `siteurl` = '{$_GET['new_siteurl']}', " .
         "`subject` = '" . $_GET['new_subject'] .
         "', `status` = '{$_GET['status']}', `comments` = '{$_GET['comments']}' " .
         "WHERE `siteurl` = '{$_GET['siteurl']}' AND `subject` = '" .
         $_GET['subject'] . "'";

      $result = mysql_query( $query )
         or die( 'Error executing query: ' . mysql_error() );

      if( $result && mysql_affected_rows() > 0 ) {
         echo '<p class="success">The entry has been edited successfully.</p>';
         $show_edit_form = false;
         $show_default = true;
      }
      else
         echo '<p class="error">Error editing the entry.</p>';

   }
   elseif( isset( $_GET["done"] ) &&
      isset( $_GET["subject"] ) &&
      isset( $_GET["siteurl"] ) &&
      isset( $_GET["new_subject"] ) &&
      isset( $_GET["new_siteurl"] ) &&
      $_GET["subject"] == '' &&
      $_GET["new_subject"] == '' &&
      $_GET["new_siteurl"] == '' &&
      $_GET["siteurl"] == '' ) {

      echo '<p class="error">You are not allowed to delete the subject ' .
         'and URL fields.</p>';

   }

   if( $show_edit_form ) {
?>
      <p>You can edit a entry you have entered into the database via this page.
      The information of the entry you have selected for editing is shown below.
      Change the fields you wish to change.</p>

      <form method="get" action="<?= $_SERVER["PHP_SELF"] ?>">
      <input type="hidden" name="action" value="edit" />
      <input type="hidden" name="siteurl" value="<?= html_entity_decode( $_GET["siteurl"], ENT_QUOTES ) ?>" />
      <input type="hidden" name="subject" value="<?= html_entity_decode( $_GET["subject"], ENT_QUOTES ) ?>" />
<?php
      // get info
      $query = 'SELECT * FROM ' . $troubles_table .
         ' WHERE siteurl = "' . $_GET["siteurl"] . '" AND ' .
         ' subject = "' . $_GET["subject"] . '"';
      $db_link = mysql_connect( $db_server, $db_user, $db_password )
         or die( 'Cannot connect to the database. Try again.' );
      mysql_select_db( $db_database )
         or die( 'Cannot connect to the database. Try again.' );
      $result = mysql_query( $query );
      $info = mysql_fetch_array( $result );
?>
      <table style="margin: auto;">

      <tr><td>
      Subject
      </td><td>
      <input type="text" name="new_subject" value="<?=
         html_entity_decode( $_GET["subject"], ENT_QUOTES ) ?>" />
      </td></tr>

      <tr><td>
      Website URL
      </td><td>
      <input type="text" name="new_siteurl" value="<?=
         html_entity_decode( $_GET["siteurl"], ENT_QUOTES ) ?>"
      />
      </td></tr>

      <tr><td>
      Status
      </td><td>
      <select name="status">
      <option value="0">No problem </option>
<?php
      $query = "SELECT * FROM `$status_table` ORDER BY `desc`";

      $result2 = mysql_query( $query )
         or die( 'Error executing query: ' . mysql_error() );

      while( $row = mysql_fetch_array( $result2, MYSQL_ASSOC ) ) {
         if( $info["status"] == $row["troublesid"] ) {
            echo '<option value="' . $row["troublesid"] .
               '" selected="selected">' .
               $row["desc"] . '</option>';
         }
         else {
            echo '<option value="' . $row["troublesid"] .
               '">' . $row["desc"] . '</option>';
         }
      }
      mysql_free_result( $result2 );
?>
      </select>
      </td></tr>

      <tr><td rowspan="3">
      Comments
      </td><td>
      <textarea name="comments" rows="3" cols="50"><?=
         $info["comments"] ?></textarea>
      </td></tr>

      <tr><td colspan="2">
      <input type="submit" name="done" value="Edit this entry" />
      <input type="reset" value="Reset values" />
      </td></tr>

      </table>
<?php
   }
}


/**
 * DELETE
 * ************************************************************************************************************
 */
elseif( isset( $action ) && $action == 'delete' ) {

   if( isset( $_GET["siteurl"] ) && $_GET["siteurl"] != '' &&
      isset( $_GET["subject"] ) && $_GET["subject"] != '' ) {

      $db_link = mysql_connect( $db_server, $db_user, $db_password )
         or die( 'Cannot connect to the database. Try again.' );
      mysql_select_db( $db_database )
         or die( 'Cannot connect to the database. Try again.' );

      $query = 'DELETE FROM ' . $troubles_table .
         ' WHERE siteurl = "' . $_GET["siteurl"] . '" AND ' .
         'subject = "' . $_GET["subject"] . '"';
      $result = mysql_query( $query )
         or die( 'Error executing query: ' . mysql_error() );
      if( $result && mysql_affected_rows() > 0 )
         echo '<p class="success">Entry ' . $_GET["subject"] .
            ' deleted successfully.</p>';
      else
         echo '<p class="error">Error deleting entry. Please try ' .
            'again.</p>';
      mysql_close( $db_link );

   }

}


/**
 * DELETE ALL
 * ************************************************************************************************************
 */
elseif( isset( $action ) && $action == 'deleteall' ) {

   $query = 'DELETE FROM ' . $troubles_table;
   $db_link = mysql_connect( $db_server, $db_user, $db_password )
      or die( 'Cannot connect to the database. Try again.' );
   mysql_select_db( $db_database )
      or die( 'Cannot connect to the database. Try again.' );
   mysql_query( $query )
      or die( 'Error executing query: ' . mysql_error() );
   mysql_close( $db_link );

   echo '<p class="success">All entries deleted.</p>';

}


/******************************************************************************
 Reset
******************************************************************************/
elseif( isset( $action ) && $action == 'reset' ) {

   $query = 'UPDATE ' . $troubles_table . ' SET status = 0';
   $db_link = mysql_connect( $db_server, $db_user, $db_password )
      or die( 'Cannot connect to the database. Try again.' );
   mysql_select_db( $db_database )
      or die( 'Cannot connect to the database. Try again.' );
   mysql_query( $query )
      or die( 'Error executing query: ' . mysql_error() );
   $num = mysql_affected_rows();
   mysql_close( $db_link );

   echo '<p class="success">' . $num . ' fanlistings reset to "no problem".</p>';

}

/**
 * GENERATE LIST
 * ************************************************************************************************************
 */
elseif( isset( $action ) && $action == 'generate' ) {
   $show_default = false;

   if( isset( $_REQUEST["sendlist"] ) ) {

      $to = $_POST["to_email"];
      $from = $_POST["from_email"];
      $copy = $_POST["send_to_yourself"];
      $list = $_POST["generated_list"];
      $name = $_POST["name"];
      $subject = stripslashes( $_POST["subject"] );

      $headers = "From: " . $name . " <" . $from . ">";
      if( $copy == 'yes' )
         $headers .= "\r\nCc: <" . $from . ">";

      $success = mail( $to, $subject, $list, $headers );

      if( $success ) {
         echo '<p class="success">List successfully sent to <i>' . $to .
            '</i></p>';
      }
      else {
         echo '<p class="error">Error sending list!</p>';
      }

   }

?>
   <p>The generated list is in the box below. Just copy all contents
   and paste it to an email.<br />
   Fanlistings with no problems are not included in the list.</p>

   <p>You can also send the list directly using this form. Just enter
   your name, your email address,<br /> 
   and the email address to send this list to, and click "Send
   List".</p>

   <form method="post">
   <input type="hidden" name="action" value="generate" />

<?php
   echo '<p><textarea rows="10" cols="60" name="generated_list">';

   $db_link = mysql_connect( $db_server, $db_user, $db_password )
      or die( 'Cannot connect to the database. Try again.' );
   mysql_select_db( $db_database )
      or die( 'Cannot connect to the database. Try again.' );

   $query = 'SELECT * FROM ' . $troubles_table . ' WHERE status != 0 ' .
      'ORDER BY status, subject';
   $result = mysql_query( $query )
      or die( 'Error executing query: ' . mysql_error() );

   $template_file = 'template.txt';
   if( file_exists( $template_file ) ) {
      $template_email = fopen( $template_file, 'r' );
      $template = fread( $template_email,
         filesize( $template_file ) );
      fclose( $template_email );

      while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {
         $print = $template;

         if( $row["status"] ) {
            $query = 'SELECT * FROM ' . $status_table .
               ' WHERE ' . $status_table .
               '.troublesid = ' . $row["status"];
            $status_rs = mysql_query( $query )
               or die( mysql_error() );
            $status = mysql_fetch_array( $status_rs );
            $print = str_replace( '$$status$$',
               $status["desc"], $print );
         }
         else
            $print = str_replace( '$$status$$',
               'No problem', $print );

         $print = str_replace( '$$subject$$', $row["subject"],
            $print );
         $print = str_replace( '$$url$$', $row["siteurl"],
            $print );
         $print = str_replace( '$$comments$$', $row["comments"],
            $print );
         echo $print . "\r\n";
      }
   } // end of if there is a template
   else {
      $status = 0;
      while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {
         if( $row["status"] != $status ) {
            $change = true;
            $status = $row["status"];

            $result2 = mysql_query( 'SELECT ' .
               $status_table .
               '.desc FROM ' .
               $status_table .
               ' WHERE troublesid = ' .
               $status )
               or die( 'Error executing query: ' .
                  mysql_error() );
            $row2 = mysql_fetch_array( $result2 );
            echo "\r\n";
            echo '----------' . "\r\n";
            echo strtoupper( $row2["desc"] ) . "\r\n\r\n";
         }

         echo $row["subject"] . "\r\n" . $row["siteurl"] .
            "\r\n";
         if( $row["comments"] )
            echo 'Comments: ' . $row["comments"] . "\r\n";
         echo "\r\n";
      }
   }

   mysql_close( $db_link );

   echo "\r\n\r\n" . 'List generated by Ohno' . "\r\n" .
      'http://scripts.indisguise.org' .
      '</textarea></p>';

?>

   <p>
   To send this list to someone, please fill out the form below.
   </p>

   <table style="margin: auto;">

   <tr><td>
   Your name
   </td><td>
   <input type="text" name="name" />
   </td></tr>

   <tr><td>
   Your email address
   </td><td>
   <input type="text" name="from_email" />
   </td></tr>

   <tr><td>
   Email address to send to:
   </td><td>
   <input type="text" name="to_email" />
   </td></tr>

   <tr><td>
   Email subject
   </td><td>
   <input type="text" name="subject" />
   </td></tr>

   <tr><td>
   Send to yourself?
   </td><td style="text-align: left;">
   <input type="radio" name="send_to_yourself" value="no"
   checked="checked" /> No<br />
   <input type="radio" name="send_to_yourself" value="yes" />Yes<br />
   </td></tr>

   <tr><td colspan="2">
   <input type="submit" value="Send List" name="sendlist" />
   </td></tr>

   </table>

<?php
}


/**
 * MANAGE STATUS
 * ************************************************************************************************************
 */
elseif( isset( $action ) && $action == 'status' ) {
   $show_status_form = true;
   $show_default = false;

   if( isset( $_POST["done"] ) ) {
      
      $db_link = mysql_connect( $db_server, $db_user, $db_password )
         or die( 'Cannot connect to the database. Try again.' );
      mysql_select_db( $db_database )
         or die( 'Cannot connect to the database. Try again.' );
      
      
      // update stuff first
      foreach( $_POST['status'] as $id => $s ) {
         if( strlen( $s ) == 0 ) continue;
         $query = "INSERT INTO `$status_table` (`troublesid`, `desc`) VALUES ('$id','$s') " .
            "ON DUPLICATE KEY UPDATE `desc`='$s'";
         $result = mysql_query( $query );
      }
      
      // now delete stuff
      if( isset( $_POST['delete'] ) ) {
         foreach( $_POST['delete'] as $d ) {
            $query = "DELETE FROM `$status_table` WHERE `troublesid` = '$d'";
            $result = mysql_query( $query );
         }
      }
      
      echo '<p class="success">Your status list has been updated. Please check to make sure your changes went through.</p>';

   }

   if( $show_status_form ) {
?>
      <p>You can update and modify status lists here. This is useful for instances where
      you are using <em>Oh No!</em> for tracking something other than fanlisting trouble
      lists ;)</p>

      <div id="multifields" style="display: none;">
         <input type="text" name="status[]" value="" id="statText" />
         <input type="button" value="x" onclick="this.parentNode.parentNode.removeChild(this.parentNode);" />
         <br />
      </div>

      <form method="post" action="<?= $_SERVER["PHP_SELF"] ?>">
      <input type="hidden" name="action" value="status" />
      <input type="hidden" name="done" value="yes" />

      <table>
         
         <tr><th>
            Status ID
         </th><th>
            Status Name
         </th><th>
            Delete?
         </th></tr>
         
<?php
      $query = "SELECT * FROM `$status_table` ORDER BY `desc`";

      $db_link = mysql_connect( $db_server, $db_user, $db_password )
         or die( 'Cannot connect to the database. Try again.' );
      mysql_select_db( $db_database )
         or die( 'Cannot connect to the database. Try again.' );
      $result = mysql_query( $query )
         or die( 'Error executing query: ' . mysql_error() );

      while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {
?>
         <tr><td>
            <?= $row['troublesid'] ?>
         </td><td>
            <input type="text" name="status[<?= $row['troublesid'] ?>]" value="<?= $row['desc'] ?>" id="statText" />
         </td><td>
            <input type="checkbox" name="delete[]" value="<?= $row['troublesid'] ?>" />
         </td></tr>
<?php
      }

      mysql_free_result( $result );
      mysql_close( $db_link );
?>

      <tr><td valign="top">
         <input type="button" value="+" onClick="moreFields()" />
      </td><td colspan="2">
         <div>
            <span id="multifieldshere"></span>
         </div>
      </td></tr>

      <tr><td colspan="3">
      <input type="submit" name="done" value="Update statuses" />
      <input type="reset" value="Reset values" />
      </td></tr>

      </table>

      <script type="text/javascript">
      var counter = 1;
      function moreFields() {
         // if multifields is present
         if( document.getElementById('multifields') == null )
            return false;
         counter++;
         var newFields = document.getElementById( 'multifields' ).cloneNode( true );
         newFields.id = '';
         newFields.style.display = 'block';
         var newField = newFields.childNodes;
         for( var i = 0; i < newField.length; i++ ) {
            var theName = newField[i].name
            if( theName )
               newField[i].name = theName + counter;
         }
         var insertHere = document.getElementById('multifieldshere');
         insertHere.parentNode.insertBefore(newFields,insertHere);
      }
      window.onload = moreFields;
      </script>
<?php
   }
}



/*****************************************************************************
 Default view
******************************************************************************/
if( $show_default ) {
?>
   <h1>Manage Entries</h1>

   <p>
   You can easily keep track of the entries you do a trouble check on using this script.
   </p>

   <p>
   Click on "new" to add an entry and its status into the database. When you're done,
   click on "generate list" to generate a list of entries in trouble that you can copy and
   paste into an email, or anywhere at all.
   </p>

   <p>
   You can also reset all entries to having no problems or delete all entries to start anew.
   </p>

   <table>

   <tr><td>
   Total fanlistings:
   </td><td>
<?php
   include 'get_fanlisting_count.php';
?>
   </td></tr>

   <tr><td>
   Total on trouble:
   </td><td>
<?php
   include 'get_troubles_count.php';
?>
   </td></tr>

   <tr><td>
   Last added/edited:
   </td><td>
<?php
   include 'get_last_added.php';
?>
   </td></tr>

   <form method="get">

   <tr><td>
   Search
   </td><td>
   <input type="text" name="search" />
   </td></tr>

   <tr><td>
   Display Order
   </td><td>
   <select name="order_by" id="order_by">
   <option value="subject" selected="selected">Subject</option>
   <option value="siteurl">Website URL</option>
   <option value="status">Status</option>
   <option value="added">Date added</option>
   </select>

   <input type="radio" name="order_by_order" value="asc" /> Ascending<br />
   <input type="radio" name="order_by_order" value="desc"
      checked="checked" /> Descending
   </td></tr>

   <tr><td>
   Number of entries<br />to show
   </td><td>
   <input type="text" name="show" value="15" />
   </td></tr>

   <tr><td colspan="2">
   <input type="submit" name="Search" />
   </td></tr>


   </form>

   </table>

<?php
   $search = '';
   $order_by = 'subject';
   $order_by_order = 'desc';
   $show = 15;
   if( isset( $_GET["search"] ) )
      $search = $_GET["search"];
   if( isset( $_GET["order_by"] ) && $_GET["order_by"] != '' )
      $order_by = $_GET["order_by"];
   if( isset( $_GET["order_by_order"] ) && $_GET["order_by_order"] != '' )
      $order_by_order = $_GET["order_by_order"];
   if( isset( $_GET["show"] ) && $_GET["show"] != '' &&
      $_GET["show"] != 0 )
      $show = $_GET["show"];

   if( $search )
      echo '<p>Search results for <i>' . $search . '</i>:</p>';
   else
      echo '<p>Showing all entries on the list:</p>';

   $db_link = mysql_connect( $db_server, $db_user, $db_password )
      or die( 'Cannot connect to the database. Try again.' );
   mysql_select_db( $db_database )
      or die( 'Cannot connect to the database. Try again.' );

   $query = 'SELECT * FROM ' . $troubles_table .
      ' WHERE siteurl LIKE "%' . $search . '%" OR ' .
      ' subject LIKE "%' . $search . '%" ORDER BY ' .
      $order_by . ' ' . $order_by_order;

   $result = mysql_query( $query )
      or die( 'Error executing query: ' . mysql_error() );

   $site_array = array();
   while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) )
      $site_array[] = $row;

   $site_num = count( $site_array );

   // set multiple page browsing
   if( !( isset( $_GET["page"] ) ) || $_GET["page"] == '' ) {
      $browse_page = 0;
   }
   else {
      $browse_page = $_GET["page"];
   }
   $array_position = $browse_page * $show;

   // determine where to start showing
   $start = $array_position;
   $end = $array_position + $show;

   echo '<table style="margin: auto;"><tr>';
   echo '<th><b>Subject</b></th>';
   echo '<th><b>Site URL</b></th>';
   echo '<th><b>Status</b></th>';
   echo '<th><b>Comments</b></th>';
   echo '<th colspan="2"><b>Action</b></th>';
   echo '</tr>';

   // loop showing entries
   while( $start < $site_num && $start < $end ) {

      echo '<tr>';

      echo '<td>' . $site_array[$start]["subject"] . '</td>';
      echo '<td><a href="' . $site_array[$start]["siteurl"] .
         '" target="' . $link_target . '">' .
         $site_array[$start]["siteurl"] . '</a></td>';

      if( $site_array[$start]["status"] ) {
         $query = 'SELECT * FROM ' . $status_table . ' WHERE ' .
            $status_table . '.troublesid = ' .
            $site_array[$start]["status"];
         $status_rs = mysql_query( $query )
            or die( mysql_error() );
         $status = mysql_fetch_array( $status_rs );
         echo '<td>' . $status["desc"] . '</td>';
      }
      else
         echo '<td>No problem</td>';


      echo '<td>' . $site_array[$start]["comments"] . '</td>';

      $site_array[$start]["subject"] = str_replace( '&', '%26',
         $site_array[$start]["subject"] );

      echo '<td class="actioncell"><a href="?action=edit&siteurl=' .
         $site_array[$start]["siteurl"] . '&subject=' .
         $site_array[$start]["subject"] . '">(edit)</a></td>';

      echo '<td class="actioncell">' . 
         '<a href="?action=delete&siteurl=' .
         $site_array[$start]["siteurl"] . '&subject=' .
         $site_array[$start]["subject"] .
         '" onclick="go=confirm(' .
         '\'Are you sure you want to delete the ' .
         $site_array[$start]["subject"] . ' fanlisting?\');' .
         'return go;">(delete)</a></td>';

      echo '</tr>';

      $start++;
   }

   echo '</table>';
   mysql_close( $db_link );

   if( $site_num > $show ) {
      $show_page_number = $site_num / $show;
      $j = 0;

      $url = $_SERVER["PHP_SELF"] . '?search=' . $search .
         '&order_by=' . $order_by .
         '&order_by_order=' . $order_by_order .
         '&show=' . $show;

      echo '<p>Go to page: ';
      while( $j < $show_page_number ) {
         echo '<a href="' . $url . '&page=' . $j . '">' . $j .
            '</a> ';
         $j++;
      }
      echo '</p>';
   }
}
require_once( 'footer.inc.php' );
?>