<?php
/*
Plugin Name: iPhone Webclip Manager
Plugin URI: http://www.cdcstudios.com/wordpress-plugins/iphone-webclip-manager/
Description: Adds a webclip icon to your headers. 
Version: 0.5
Author: Chris O'Rourke
Author URI: http://www.cdcstudios.com
*/


/*
Copyright (C) 2008 Chris O'Rourke

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

http://www.gnu.org/licenses/gpl.txt

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

*/   

function get_webclip_type($name)
{
  if (preg_match("/\.gif$/i", $name))
  {
    return "gif";
  }
  if (preg_match("/\.ico$/i", $name))
  {
    return "x-icon";
  }
  if (preg_match("/\.jp[e]?g$/i", $name))
  {
    return "jpg";
  }
  if (preg_match("/\.png$/i", $name))
  {
    return "png";
  }
  return "";
}

function create_webclip_uri($webclip_location)
{
  // if an absolute location entered, don't try to add the site url...
  if (preg_match("/^http/", $webclip_location))
  {
    $webclip_url=$webclip_location;
  }
  else
  {
    $webclip_url= get_settings('siteurl') . '/' . $webclip_location;
  }
  return $webclip_url;
}

// if you've stored a webclip location in the database, retrieve it
// and put it into the headers. The filetype of the webclip is determined from
// it's name if no location stored, then fail quietly and do nothing at all
function add_webclip_to_headers() 
{
  $webclip_location=create_webclip_uri(get_option('fm_webclip_location'));
  if ($webclip_location) 
  {
    $webclip_type=get_webclip_type($webclip_location);
    if ($webclip_type)
    {
      print '	<link rel="apple-touch-icon" href="'. $webclip_location .'" type="image/'. $webclip_type .'" />';
      print "\n";
      print '	<link rel="apple-touch-icon" href="'. $webclip_location .'" type="image/'. $webclip_type .'" />';
      print "\n";
    }
    else
    {
      echo '	<link rel="icon" href="'. $webclip_location .'" />';
      print "\n";
      echo '	<link rel="shortcut icon" href="'. $webclip_location .'" />';
      print "\n";
    }
  }
}
// insert webclip into header using WP hooks
add_action('wp_head', 'add_webclip_to_headers');


// options menu


// add the saved items to the database if not already there
function webclip_mgr_add_options()
{
	add_option('fm_webclip_location', '');
}
// call to actually set up
webclip_mgr_add_options();

// create hook for new submenu
add_action('admin_menu', 'webclip_mgr_admin_menu');

// title of page, name of option in menu bar, which function lays out the html
function webclip_mgr_admin_menu()
{
  add_options_page(__('Webclip Manager Options'), __('Webclips'), 5, basename(__FILE__), 'webclip_mgr_options_page');
}

// html layout for option page, plus detection/update on new settings
function webclip_mgr_options_page()
{
  $updated = false;

  // did the user enter a new/changed location?
  if (isset($_POST['fm_webclip_location']))
  {
    $fm_webclip_location = $_POST['fm_webclip_location'];
    // remember the change in the database
    update_option('fm_webclip_location', $fm_webclip_location);
    // and remember to note the update to user
    $updated = true;
  }
  // either way make sure we have the latest value at hand
  $fm_webclip_location = get_option('fm_webclip_location');

  // notify the user that we updated something
  if ($updated)
  {
    ?>
    <div class="updated"><p><strong>Options saved.</strong></p></div>
    <?php
  }
  
  // now tack on any beginning http that might be needed
  $webclip_url=create_webclip_uri($fm_webclip_location);


  // print the form page
  ?>
  <div class="wrap">
	  <h2>Webclip Settings</h2>
	  <form name="form1" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		  <fieldset class="options">

			  <legend>Webclip in WordPress Pages</legend>
			  <table width="100%" cellspacing="2" cellpadding="5" class="editform">
			  <tr valign="top">
				  <th width="33%" scope="row">Location:</th>
				  <td><input name="fm_webclip_location" type="text" width="60" value="<?php echo $fm_webclip_location; ?>"/>
				  <br />You can enter a pathname relative to your WordPress installation, or an absolute (starting with http) address.
				  If it worked, your icon should show up below after updating:
				  <?php if ($webclip_url) { 
				    echo '<p><img src="'. $webclip_url .'" />'; } 
				    echo "from $webclip_url</p>\n";
				  ?>
				  </td>
			  </tr>
			  </table>
		  </fieldset>

		  <p class="submit">
		    <input type="submit" name="update_webclips" value="Update Options &raquo;" />
		  </p>
	  </form>
  
  </div>
  
  <?php 
}


?>
