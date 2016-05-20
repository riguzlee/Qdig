<?php
/*
+----------------------------------------------------------------------+
| Qdig - A Quick Digital Image Gallery
|
| Qdig is an easy-to-use script that dynamically creates an image
| gallery or set of galleries from image files stored on a web server's
| filesystem.  Qdig supports subdirectory navigation for organized
| presentation of any size image collection.  Qdig also supports image
| captions, and can generate thumbnail images and smaller resampled
| versions of large images such as digital camera photos.  Qdig is
| simple to install, just drop it in a directory with images and/or
| subdirectories that contain images.  Converting (resampling) images
| requires either Image Magick or PHP's GD extensions and some quick-
| and-simple additional setup.  There are dozens of configurable options
| for customizing your galleries.  The script runs stand-alone, or a
| gallery may be included within another page.  Enjoy!
+----------------------------------------------------------------------+
| Copyright 2002, 2003, 2004, 2005, 2006, 2007 Hagan Fox
| This program is distributed under the terms of the
| GNU General Public License, Version 2
|
| This program is free software; you can redistribute it and/or modify
| it under the terms of the GNU General Public License, Version 2 as
| published by the Free Software Foundation.
|
| This program is distributed in the hope that it will be useful,
| but WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
| GNU General Public License for more details.
|
| You should have received a copy of the GNU General Public License,
| Version 2 along with this program; if not, visit GNU's Home Page
| http://www.gnu.org/
+----------------------------------------------------------------------+
CVS: $Id: index.php,v 1.151 2007/02/11 06:26:22 haganfox Exp $
*/
$qdig_version = '1.2.9.4';
$mtime = microtime();
$mtime = explode(" ",$mtime);
$start_time = $mtime[1] + $mtime[0];

/*
+------------+
|  Settings  |
+------------+
*/

// Site Settings ------------------------------------------------------+

/**
* Site Link (optional)
*
* The link is a location (URL) and the title is the link text.
*/
$site_lnk_title = 'Home Page';  // Link text
$site_lnk_url   = '';           // URL - Using '' will disable the Site Link.

/**
* Copyright text (optional)
*
* If this is set, the text will appear at the bottom of your gallery.
* See http://www.whatiscopyright.org/ for information about copyrights.
*/
$copyright['txt'] = 'Images copyright &copy; 2005, 2006, 2007 original creators or assignees. All rights reserved.';

/**
* Admin Script
*/
$admin['script_file'] = 'admin.php';  // Location of Gallery Management Script
$admin['full_url']    = '';           // Full URL of Gallery Management Script
                                      // in case just script_file isn't enough.

/**
* Chroot Directory
*
* Defines the topmost directory of the gallery tree.  Commented out by default.
* Typically used when including a gallery within a web page.
* See http://qdig.sourceforge.net/Support/ChrootDirectory for details.
*/
//$chroot_dir = 'images/';  // Note: You may also want to add this path to the
                            // $qdig_files location (e.g. 'images/qdig-files/')

// Layout Settings ----------------------------------------------------+
// These are in roughly the same order as they appear in the output.

/**
* HTML Header
*
* These work for stand-alone mode only, since othewise headers are suppressed.
*/
$header['title_text_1'] = 'Image Gallery';  // Leading text in title
$header['title_delim']  = '|';    // Delimiter character(s)
$header['title_cntr']   = TRUE;   // Include an 'x of y' counter.
$header['title_text_2'] = '';     // Trailing text in title
$header['img_name']     = TRUE;   // Include image name in title
$header['nav_links']    = TRUE;   // Include nav. links in html header.
$header['icon']         = TRUE;   // Use the qdig-camera icon.
$header['force_disa']   = FALSE;  // Force HTML headers to be suppressed.
$header['force_ena']    = FALSE;  // Force HTML headers to be produced.

/**
* Directory Navigation (Contains pwd and subdirectories.)
*/
$dir_nav['enable']     = TRUE;    // Enable Directory Navigation.  If disabled,
                                  // subdirectories will be isolated galleries.
$dir_nav['small']      = FALSE;   // Use small text for Dir. Navigation row.
$dir_nav['fname_ena']  = FALSE;   // Display the filename.
$dir_nav['cntr_ena']   = TRUE;    // Display the counter.
$dir_nav['path_delim'] = '&gt;';  // Delimiter between path elements
$dir_nav['dir_is_new'] = 0 * 24 * 60 * 60; // New if less than this many seconds
$dir_nav['new_flag']   = '&nbsp;
      <small style="font-size:8pt; vertical-align:top; padding-bottom:1px; text-decoration:none;
       font-weight:normal; color:black; background-color:yellow;">&nbsp;New&nbsp;</small>';
                                  // String to display with dir name if "new"
$dir_nav['sort_age']   = FALSE;   // Sort directories by time-since-modified
                                  // (most recent first) rather than by alpha.
$dir_nav['sort_rev']   = FALSE;   // Reverse the sort order of directories.
$dir_nav['icon']       = FALSE;   // Display a camera icon in directory links.
$dir_nav['updir_ena']  = TRUE;    // Display an up-one-level link.
$dir_nav['row_width']  = '100%';  // Width of the Directory Navigation row

/**
* Control Bar (Contains visitor preferences: links style and default view)
*/
$ctrl_bar['enable']   = TRUE;   // Enable control bar.
$dir_nav['prefs_ena'] = TRUE;   // Display a Preferences link, which toggles
                                // the Control Bar for a more compact gallery.
$ctrl_bar['vw_ctrl']  = TRUE;   // Enable control bar Links Style chooser.
$ctrl_bar['sz_ctrl']  = FALSE;  // Enable control bar Default Size chooser.
$ctrl_bar['small']    = TRUE;   // Use small text for control bar.

/**
* Image Links Placement and Sorting
*/
$img_links_above     = FALSE;  // Locate image links (numerals, thumbnails)
                               // above the image.  Sensible default is below.
$img_links_sort_date = FALSE;  // Sort images by date, not alphabetically.
$img_links_sort_rev  = FALSE;  // Sort images in reverse order.

/**
* Thumbnail Image Links
*/
$thmb_default = TRUE;  // Default to thumbs view if thumbs-capable.
$thmb_enable  = TRUE;  // Enable thumbs view.  Safe to leave TRUE.
$thmb_onfly   = TRUE;  // Create thumbnails 'on the fly' in their
                       // own request so pages load more quickly.
// Wrapping -- Don't wrap only one or two thumbnails.
$thmb_row['maxwidth'] = 680;  // Approx. max. width of thumbnails row
$thmb_row['softwrap'] = 85;   // A percentage, 51 to 99

/**
* Filename and Numeral Text Image Links
*/
$txtlinks_default = 'none';  // If no text-link type is specified, default
                             // to name, num, or none.
$namelinks_disa   = FALSE;   // Disable 'names' view option in the Control
                             // Bar size chooser.  (Avoids the sidebar.)
$namelinks_small  = TRUE;    // Use small text for filename links.
$namelinks_trunc  = 16;      // Number of filename characters to display
// Numeral Text Image Links
$nmrl_row['small']   = TRUE;   // Use small text for numeral links.
$nmrl_row['pad_top'] = '2px';  // padding above rows of numeral links
// Wrapping -- Don't wrap only one or two links.
$nmrl_row['maxcount'] = 23;    // maximum number of numeral links per row
$nmrl_row['softwrap'] = 85;    // percentage, 51 to 99

/**
* Upper Gallery Navigation Row immediately above the image
*
* If nothing on the row is enabled, the navigation row is omitted.
*/
$upr_nav['enable']    = TRUE;
$upr_nav['sizer']     = FALSE;  // Show resizing links.  Overrides 'full_link'.
$upr_nav['full_link'] = FALSE;  // Show `Full Size' link if image is resized.
$upr_nav['prv_next']  = TRUE;   // Show `Previous' / `Next' links.
$upr_nav['wrap']      = FALSE;  // Wrap at Prev / Next at last / first image.
$upr_nav['frst_last'] = TRUE;   // Show ` |<< ' and ` >>| ' links.
$upr_nav['cntr']      = FALSE;  // Show `x of y' counter if no $dir_nav counter.
$upr_nav['cntr_bold'] = FALSE;  // Bold font for `x of y' message
$upr_nav['sml_txt']   = FALSE;  // Use small text.
$upr_nav['width']     = '500';  // Width of this navRow's table
$upr_nav['pad_top']   = '0px';  // Padding above row

/**
* Image Display
*/
$caption['min_width'] = 300;    // Minimum width for an image caption
$caption['padding']   = '3px';  // Padding around caption text
$caption['nl2br']     = FALSE;  // Automatically insert <br /> tags in captions.
$caption['left_just'] = FALSE;  // Left-justify caption (otherwise centered).
$caption['above']     = FALSE;  // Place caption above the image, not below it.
// Settings for making the displayed image an active link
$img_link['next']    = TRUE;   // Link to the next image from the one displayed.
$img_link['wrap']    = FALSE;  // Link back to first image from the last one.
$img_link['wrap_up'] = TRUE;   // Link the last image to the directory above.
// Other scripts have the following, so some people must like it.
$img_link['full']  = FALSE;  // If the image is a resized version, link to the
                             // full sized version.  Disables 'next' and 'wrap'.
$img_link['file']  = FALSE;  // Full size link goes directly to the image file.

/**
* Lower Gallery Navigation Row below the image and caption
*
* If nothing on the row is enabled, the navigation row is omitted.
*/
$lwr_nav['enable']    = TRUE;
$lwr_nav['sizer']     = FALSE;  // Show resizing links.  Overrides 'full_link'.
$lwr_nav['full_link'] = TRUE;   // Show `Full Size' link if image is resized.
$lwr_nav['prv_next']  = TRUE;   // Show `previous' / `next' links.
$lwr_nav['wrap']      = FALSE;  //  Wrap at prev / next at last / first image.
$lwr_nav['frst_last'] = TRUE;   // Show ` |<< ' and ` >>| ' links.
$lwr_nav['cntr']      = TRUE;   // Show `x of y' counter if no $dir_nav counter.
$lwr_nav['cntr_bold'] = TRUE;   // Bold font for `x of y' message
$lwr_nav['sml_txt']   = FALSE;  // Use small text for this navigation row.
$lwr_nav['width']     = '500';  // Width of this navRow's table
$lwr_nav['pad_top']   = '2px';  // Padding above row

/**
* Gallery Footer Row
*/
$footer['pad_top']     = '5px';     // Extra space above the footer line
$site_link_fnt_size    = '0.75em';  // Font size for Site Home Link
$copyright['fnt_size'] = '0.75em';  // Font size for Copyright Message
$qdig_homelink['ena']  = TRUE;      // Show the Qdig Home link.
$qdig_homelink['fnt_size'] = '0.75em';  // Qdig Home link Font size

// Color and CSS Style Settings ---------------------------------------+
// See http://qdig.sourceforge.net/Qdig/ColorSchemes

// HTML Header CSS settings are only effective for stand-alone Qdig.
$header['css_bgcolor']     = '#f8f8f8';  // Page Background
$header['css_text_color']  = '#333333';  // Text
$header['css_link_color']  = '#004080';  // Link
$header['css_visit_color'] = '#006699';  // Visited Link
$header['css_bg_img_url']  = '';         // URL of a tiled background image.
                                         // Example: '../images/qdig-bg.jpg'
$header['css_bg_logo']     = FALSE;      // Use a background logo.
$header['css_logo_url']    = '';         // URL of logo image, else use cam-icon
$header['css_logo_pos']    = '99% 99%';  // CSS position of the bg_logo.
$header['css_img_bg']      = '#eeeeee';  // Image background (when loading)
                                         // 'transparent' is a valid "color".
$header['css_img_border']  = '#cccccc';  // Displayed-image border
$header['css_img_brdr_w']  = '1px';      // Width of displayed-image border
$header['css_thm_border']  = '#cccccc';  // Thumbnail images' border
$header['css_thm_brdr_w']  = '0px';      // Width of thumbnail-image border
$header['css_thm_opacity'] = '100';      // Setting of 1 to 99 fades thumbs.
                                         // (Opacity uses invalid CSS and
                                         // slows rendering speed.)
$header['css_opacity_moz'] = FALSE;      // Opacity for older gecko browsers
$header['css_thm_hilite']  = '#f9f99f';  // Hilight border for current-thumb.
$header['css_thm_hl_w']    = '2px';      // Width of highlight border
$header['css_user_def']    = '';         // User-defined CSS rules
// Set these to '' to disable.
$sidebar_bg_clr         = '#eeeeee';  // Sidebar background
$sidebar_margin_top     = '4px';      // Margin above the sidebar
$sidebar_height         = '';         // '' is "auto".  example: '350px'
$copyright['color']     = '#cccccc';  // Copyright text
$admin['color']         = '#cccccc';  // Admin link text
$qdig_homelink['color'] = '#cccccc';  // Qdig Home Link text
$grayout_color          = '#999999';  // Grayed-out text
// Gallery Table and Image Table (Possibly for an embedded gallery)
// Use '' for default background color.
$gallery_table_bg_clr   = '';  // Background color of the gallery table.
$image_table_bg_clr     = '';  // Background color of image area.
// Arbitrary code can be inserted before or after the gallery table.
$pre_gallery            = '';  // User-defined output before gallery
$post_gallery           = '';  // User-defined output after gallery


// Image Conversion and Alternate-size Settings -----------------------+
// Thumbnail settings are in the Layout Settings section.

/**
* Writable Directories
*
* Folders containing your original images may be read-only by the web server
* daemon, but the script needs write permissions to write empty caption .txt
* files and converted-image files (thumbnail and alternate-sized images).
*/
$qdig_files = 'qdig-files/'; // The root of the writable tree.  Setup is easy:
                             // Create the directory.  Give it 2777 permissions
                             // (`chmod 2777').  Visit the gallery once.  Then
                             // change the permissions to something sane (0755).
//$cnvrtd_dir = 'qdig-converted';  // Name of the resampled images subdirectory.
                                   // Uncomment this and comment out $qdig_files
                                   // for the behavior of previous releases.
$convrtd_subdir = 'converted-images/';  // Subdir for resampled images
$caption_subdir = 'captions';           // Subdir for captions
$touch_captions = TRUE;  // Create empty caption .txt files, if found missing.

/**
* Image Magick and GD Settings
*
* If you have both Image Magick and GD, Qdig uses IM except on a Win32 server.
* To use the one that isn't the default, set the default one to FALSE.
*/
$convert_magick = TRUE;  // Use Image Magick, if available, to convert images.
$convert_cmd    = '/usr/bin/convert';  // Full pathname to `convert'.
// Example $convert_cmd for Win32 users:
//$convert_cmd    = '"C:\\Program Files\\ImageMagick-5.5.3-Q16\\convert.exe"';
$convert_GD     = TRUE;  // Use PHP GD, if available, to convert images.
$convert_GD_ver = '';    // '' is auto-detect (recommended).  Else '1' or '2'.

/**
* Sizes to convert / display
*
* Enable or disable any of the alternate sizes (to save bandwidth,
* fit a layout, etc.).  Experiment, but here are some suggestions:
* Old Defaults: TRUE,TRUE,TRUE,TRUE,FALSE
* Basic: FALSE,TRUE,FALSE,FALSE,TRUE
*/
$disp_size['0'] = FALSE;  // 'S'  | These cause resizing links
$disp_size['1'] = TRUE;   // 'M'  | in the Control Bar and
$disp_size['2'] = FALSE;  // 'L'  | Navigation Bar to disappear
$disp_size['3'] = FALSE;  // 'XL' | if set to FALSE and
$disp_size['4'] = TRUE;   // 'FS' | appear if set to TRUE.
// If no size is specified, use this size as the default.
$default_img_size = '1';  // '1' is medium.  Must be an enabled size.

/**
* Thumbnail Image Conversion Settings
*/
$cnvrt_thmb['size'] = 30;  // Thunbnail image height in pixels.
                           // Sizes: 10 is tiny, 20 is small, 35 is medium,
                           //        50 is large, 75 is jumbo
$cnvrt_thmb['qual'] = 60;  // Thumbnail image quality.  Large thumbnails
                           // may look better, but will have increased file
                           // size, if you increase this a bit.
$cnvrt_thmb['sharpen'] = '0.6x0.6';  // Level of sharpening for thumbnails.
$cnvrt_thmb['single']  = FALSE;      // Convert thumb in a singleton directory.
$cnvrt_thmb['mesg_on'] = FALSE;   // Produce a message when a thunbnail image
                                 // is auto-generated.
$cnvrt_thmb['no_prof'] = FALSE;  // Strip image profile data to reduce size.
                                 // (May be incompatible with some servers.)
$cnvrt_thmb['prefix']  =         // Filename prefix for thumbnail images.
  "thm{$cnvrt_thmb['size']}_";   // Use "thm_" for externally generated thumbs.

/**
* Alternate-sized Image Conversion Settings
*
* ['prefix']  is the filename prefix for the generated file.
* ['sharpen'] is the sharpen pramater passed to ImageMagick.
* ['maxwid']  is the size setting.  Other dimensions are calculated.
* ['qual']    is the compression quality level.
* ['txt']     is the image size text used inline in a message.  (Language Setting)
* ['label']   is the text used for a link.  (Language Setting)
*/
$cnvrt_alt['indiv']   = TRUE;   // Convert alternates one-at-a-time rather than
                                // all the images in a directory at once.
$cnvrt_alt['mesg_on'] = TRUE;   // Produce a message when an image is converted.
$cnvrt_alt['no_prof'] = FALSE;  // Strip image profile data to reduce size.
                                // (May be incompatible with some servers.)
// small
$cnvrt_size['0']['prefix']  = 'sml_';
$cnvrt_size['0']['sharpen'] = '0.6x0.8';
$cnvrt_size['0']['maxwid']  = 512;
$cnvrt_size['0']['qual']    = 87;
// medium
$cnvrt_size['1']['prefix']  = 'med_';
$cnvrt_size['1']['sharpen'] = '0.6x0.8';
$cnvrt_size['1']['maxwid']  = 640;
$cnvrt_size['1']['qual']    = 89;
// large
$cnvrt_size['2']['prefix']  = 'lrg_';
$cnvrt_size['2']['sharpen'] = '0.6x0.9';
$cnvrt_size['2']['maxwid']  = 800;
$cnvrt_size['2']['qual']    = 90;
// x-large
$cnvrt_size['3']['prefix']  = 'xlg_';
$cnvrt_size['3']['sharpen'] = '0.6x0.9';
$cnvrt_size['3']['maxwid']  = 1024;
$cnvrt_size['3']['qual']    = 91;
// actual
$cnvrt_size['4']['prefix']  = '../';

// Language Settings --------------------------------------------------+
// Text that appears in the output may be configured here.

/**
* Header
*/
$header['lang_code'] = 'en';
$header['charset']   = 'iso-8859-1';

/**
* Directory Navigation
*/
$dir_nav['main_txt']        = 'Main';
$dir_nav['choose_main_txt'] = 'Choose a gallery:';
$dir_nav['choose_main_title_txt'] = 'Please choose a gallery.';
$dir_nav['choose_sub_txt']  = '';
$dir_nav['choose_sub_title_txt']  = 'Please choose a gallery.';
$dir_nav['empty_dir_txt']   = 'No gallery!';
$dir_nav['empty_dir_title_txt']   = 'Sorry, no gallery here.';
$dir_nav['image_txt']       = 'Image';
$dir_nav['go_to_txt']       = 'Go to';
$dir_nav['up_level_txt']    = 'Up&nbsp;a&nbsp;level..';
$dir_nav['up_title_txt']    = 'Go up one level';
$dir_nav['current_txt']     = 'Current location:';
$dir_nav['prefs_title_txt'] = 'Change your visitor preferences';
$dir_nav['prefs_txt']       = 'Preferences';

/**
* Gallery Navigation Links
*/
$nav_lnk['prv_txt']   = '&lt;&lt; Previous';
$nav_lnk['prv_msg']   = 'Previous Image';
$nav_lnk['next_txt']  = 'Next &gt;&gt;';
$nav_lnk['next_msg']  = 'Next Image';
$nav_lnk['last_txt1'] = '&lt;&lt; Last';
$nav_lnk['last_txt2'] = '&gt;&gt;|';
$nav_lnk['last_msg']  = 'Last Image';
$nav_lnk['frst_txt1'] = 'First &gt;&gt;';
$nav_lnk['frst_txt2'] = '|&lt;&lt;';
$nav_lnk['frst_msg']  = 'First Image';
$nav_lnk['image']     = 'Image';

/**
* Text shown if there is no image to display
*/
$empty_gallery_msg   = 'Empty gallery!';

// Text/Messages for controlView() (links view on control bar)
$ctrl_links_mesg['links_style'] = 'Links Style:';
$ctrl_links_mesg['thumbs_txt'] = 'Thumbs';
$ctrl_links_mesg['names_txt']  = 'Names';
$ctrl_links_mesg['nums_txt']   = 'Numbers';
$ctrl_links_mesg['none_txt']   = 'None';
$ctrl_links_mesg['thumbs_msg'] = 'Switch to Thumbnail Links';
$ctrl_links_mesg['names_msg']  = 'Switch to Filename Links';
$ctrl_links_mesg['nums_msg']   = 'Switch to Calendar-Style Links';
$ctrl_links_mesg['none_msg']   = 'Disable Direct Image Links';

/**
* Image size title text
*/
$cnvrt_size['0']['label'] = 'S';
$cnvrt_size['1']['label'] = 'M';
$cnvrt_size['2']['label'] = 'L';
$cnvrt_size['3']['label'] = 'XL';
$cnvrt_size['4']['label'] = 'FS';
$img_sz_labels['ctrl']['default_size'] = 'Default Size:';
$img_sz_labels['ctrl']['str1'] = 'Change the default image size to ';
$img_sz_labels['nav']['str1']  = 'See the ';
$img_sz_labels['nav']['str1a'] = 'Return to the ';
$cnvrt_size['0']['txt']  = 'Small';
$cnvrt_size['1']['txt']  = 'Medium';
$cnvrt_size['2']['txt']  = 'Large';
$cnvrt_size['3']['txt']  = 'Extra Large';
$cnvrt_size['4']['txt']  = 'Full Size';
$cnvrt_size['4']['txt2'] = 'default';
$img_sz_labels['ctrl']['str2'] = '.';
$img_sz_labels['nav']['str2']  = ' version of this image.';

/**
* Admin link
*/
$admin['link_title']  = 'Edit Caption';
$admin['link_text']   = 'Admin';
$admin['before_link'] = '(';
$admin['after_link']  = ')';

/**
* Image conversion messages (e.g. "Generated a new Large converted image for image.jpg")
*/
$cnvrt_mesgs['generated']  = 'Generated a new ';
$cnvrt_mesgs['generating'] = 'Generating a new ';
$cnvrt_mesgs['thumb']      = 'thumbnail';
$cnvrt_mesgs['converted']  = ' converted';
$cnvrt_mesgs['image_for']  = ' image for ';
$cnvrt_mesgs['using IM']   = " using 'convert'";
$cnvrt_mesgs['using GD']   = ' using GD';
$cnvrt_mesgs['on-the-fly'] = ''; // was: ' (on-the-fly)'

/**
* Miscellaneous
*/
$lang['nav_cntr_txt']  = 'of'; // Counter ("x of n")
$lang['Forbidden']     = 'Forbidden';
$lang['diag_messages'] = 'Diagnostic Messages';

// Security Settings  ------------------------------------------------+

/**
* File creation mask.  Determines default permissions for created files, dirs.
*
* Examples: umask(002)  // `drwxrwxr-x' and `-rw-rw-r--' (world readable)
*           umask(007)  // `drwxrwx---' and `-rw-rw----' (not world readable)
*/
umask(002);

/**
* Paranoia Settings
*/
$safe_captions    = TRUE;   // Disable HTML in Captions.  Convert special
                            // characters (<>&"') to `HTML entities'
$check_security   = TRUE;   // Perform a security check for world-writability.
$ignore_dir_links = TRUE;   // Ignore gallery directories if they're symlinks.
$ignore_img_links = TRUE;   // Ignore image files if they're symlinks.
$pathname_maxlen  = 100;    // Max. number of characters in a pathname.
$imgname_maxlen   = 100;    // Max. number of characters in an image filename.
$extra_paranoia   = FALSE;  // Do extra-strict checking for '..'.
$ignore_dotfiles  = FALSE;  // Ignore files that start with '.'.
$ignore_dotdirs   = FALSE;  // Ignore directories that start with '.'.
// HTML Header settings are only effective for stand-alone Qdig.
$header['zap_frames']    = FALSE; // Break out of a frameset.
$header['ie_imgbar_off'] = TRUE;  // Suppress IE6's image toolbar.

// Miscellaneous Settings ---------------------------------------------+

/**
* Server Compatibility
*/
// Disable use of certain PHP functions for compatibility with some servers.
$is_readable_disa = FALSE;  // Set to TRUE if is_readable() causes trouble.
$file_exists_disa = FALSE;  // Set to TRUE if file_exists() causes trouble.
$max_exec_time    = 30;     // Max. execution time in seconds
$compat_quote     = TRUE;   // Add and extra "s to exec() command on Win32.
                            // For Win98 this should be set to FALSE.
$exclude_gif      = FALSE;  // Exclude GIF images.
// HTML Header settings are only effective for stand-alone Qdig.
$header['meta_cache'] = FALSE;  // Use a Cache-Control meta tag.  For servers
                                // that cause repeated reloading of thumbs.
$header['cache_sec']  = '3600'; // Number of seconds for the cache to expire.

/**
* Path Settings  (Override defaults.)
*/
$qdig_url = '';       // Self-referring URL path.  Examples: '/photos/' or
                      // '/photos/qdig.php' or '/~someuser/qdig/index.php'
// The next two are the same location; as a URL and as a filesystem path.
$url_base_path = '';  // Base URL path to the images (not the script)
                      // Examples: '/photos/qdig/' or '/~someuser/qdig/'
$fs_base_path  = '';  // Filesystem path to the root dir of the gallery.
                      // Ex.: '/home/someuser/public_html/qdig/' or '../qdig'

/**
* Et-cetera
*/
$excl_dirs[] = 'Private';         // Ignore a directory with its name
$excl_dirs[] = 'qdig-converted';  // included here.  Do not  end these
$excl_dirs[] = '';                // name(s) with '/'.
$excl_imgs[] = 'qdig-bg.jpg';     // Ignore any image with its name
$excl_imgs[] = 'favicon.png';     // included here.  Add as many of
$excl_imgs[] = '';                // these as you wish.
$excl_img_pattern = '_thumb';     // Don't display files containing this string.
$excl_main   = FALSE;  // Exclude all images in the root (Main) directory.
$extra_param = '';     // Extra parameter(s) to include in URLs.
                       // Examples: 'incl=qdig.php&amp;' 'a=foo&amp;b=bar&amp;'
$anchor = '';          // Include an intra-page anchor in URLs.  For embedded
                       // galleries, use '#qdig' to jump down to the gallery.
$keep_params = FALSE;  // Keep extra GET parameters in the URLs.

/**
* Debugging Setting
*/
$diag_messages = FALSE; // Produce diagnostic messages.  This will also enable
                        // verbose PHP error reporting.

/**
* Get External Settings
*/
if (function_exists('qdig_settings')) {
	qdig_settings($qdig_version);
}

// Settings Notes -----------------------------------------------------+
// (To make upgrading easier, put notes about your settings here.)

/*
+-------------------+
|  Adapt to Server  |
+-------------------+
*/

// Get global variables and protect them from register_globals.
$get_vars = ($_GET) ? $_GET : $HTTP_GET_VARS;
$post_vars = ($_POST) ? $_POST : $HTTP_POST_VARS;
$cookie_vars = ($_COOKIE) ? $_COOKIE : $HTTP_COOKIE_VARS;
$request_vars = ($_REQUEST)
	? $_REQUEST
	: array_merge($get_vars, $post_vars, $cookie_vars);
if (ini_get('register_globals')) {
	if (!is_array($request_vars)) { securityExit('Security Violation'); }
	foreach($request_vars as $k=>$v) {
		if (preg_match('/^(GLOBALS|_SERVER|_GET|_POST|_COOKIE|_FILES|_ENV|_REQUEST|_SESSION|qdig-files)$/i', $k)) {
			securityExit('Security violation'); }
		${$k}=''; unset(${$k});
	}
}
$server_vars = ($_SERVER) ? $_SERVER :  $HTTP_SERVER_VARS;
// Disallow some special characters in the query string.
if (preg_match('/(%00|%3c|<)/', $server_vars['QUERY_STRING'])
	|| preg_match('!\\\\0!', @$get_vars['Qwd'])) {
	securityExit('Invalid request.'); }
$php_self = @$server_vars['PHP_SELF'];
$script_name = @$server_vars['SCRIPT_NAME'];
if (! $request_uri = @$server_vars['REQUEST_URI']) {  // Not provided by IIS.
	$request_uri = @$php_self.'?'.@$server_vars['argv'][0];  // Close enough
}
// Suppress harmless Notices and annoying Warnings.  Restored at the end.
if (!isset($orig_err_rep_level)) {
	$orig_err_rep_level = ($diag_messages == TRUE)
		? error_reporting(E_ALL)
		: error_reporting(E_ALL ^E_NOTICE ^E_WARNING);
}
// Establish self-referring URL.
if (empty($qdig_url)) {
	$tmp = explode('?', @$request_uri);
	$qdig_url = @$tmp['0'];
	unset($tmp);
}
if (empty($qdig_url)) {
	$qdig_url = @$script_name;
}
// Safe mode?
if (ini_get('safe_mode')) {
	$safe_mode = TRUE;
} else {
	$safe_mode = FALSE;
	@ini_set('max_execution_time', $max_exec_time);
}
// Which OS?
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
	$platform = 'Win32';
} elseif (strtoupper(substr(PHP_OS, 0, 3)) == 'MAC') {
	$platform = 'Macintosh';
} elseif (strtoupper(substr(PHP_OS, 0, 3)) == 'LIN') {
	$platform = 'Linux';
} else {
	$platform = 'Unix';
}
// Included Qdig?
if (realpath(__FILE__) == realpath(@$_SERVER['SCRIPT_FILENAME'])
	|| ! $tmp = get_included_files())
{
	$is_included = FALSE;
} else {
	$is_included = TRUE;
}

/**
* Produce an icon image if ?image=cam-icon
*/
if (@$get_vars['image'] == 'cam-icon') {
	cam_icon();
	die();
}
if (@$get_vars['image'] == 'clear-dot') {
	clear_dot();
	die();
}

/*
+-------------+
|  Functions  |
+-------------+
*/

// http://qdig.sourceforge.net/Qdig/FunctionsList

/**
* The encoded icon image.
*/
function cam_icon() 
{
	header('Content-type: image/png');
	header('Content-length: 346');
	echo base64_decode(
'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAALHRFWHRDcmVhdGlvbiBUaW1lAFRo'.
'dSAyNiBEZWMgMjAwMiAxMToxOTowNiAtMDcwMOKR1KkAAAAHdElNRQfSDBoSGhzS+Jz0AAAACXBI'.
'WXMAAAsSAAALEgHS3X78AAAABGdBTUEAALGPC/xhBQAAACdQTFRF////ZmZmMzMzZjMzM2ZmM2Yz'.
'ZjNmzMzMmZnMmZmZmZn/zP//ZmbMVprHTAAAAAF0Uk5TAEDm2GYAAABxSURBVHjaVU+LEsAgCAqr'.
'pbb//95Be9OdHoRSpSwAMKC8wCT+Qv6FJod9HZVbbgd0E0A8XAmt8rCLDyKChVF0QxS5IyQwyRbP'.
'mRbauxzkUuQdeIS8BY54E68u4VpaPZty1ohiHfCTm35u4KtY+9a3cgC4QwQQbFlZCQAAAABJRU5E'.
'rkJggg==');
} // End cam_icon()

function clear_dot() 
{
    header('Content-type: image/gif');
    header('Content-length: 43');
    echo base64_decode(
'R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
}

/**
* Security Exit
*/
function securityExit($mesg = 'Unspecified Error')
{
	global $lang;	
      header('HTTP/1.0 403 Forbidden');
      exit("<html>\n <head><title>403 {$lang['Forbidden']}</title></head>
 <body>{$lang['Forbidden']}: $mesg</body>\n</html>");
} // End securityExit()

/**
* Return 'TRUE' or 'FALSE' string based on a variable's status.
*/
function trueFalse($data, $true = 'TRUE', $false = 'FALSE')
{
	if (@$data == TRUE) {
		return $true;
	} else {
		return $false;
	}
} // End trueFalse()

/**
* Return non-Qdig GET parameters, ready for prepending to a query string.
*/
function keepParams()
{
	global $get_vars, $extra_param;
	if (!empty($extra_param)) { return; }
	$params = '';
	foreach($get_vars as $param => $value) {
		$qdig_params = array('Qwd', 'Qif', 'Qiv', 'Qis', 'Qtmp', 'image', 'Makethumb');
		if (in_array($param, $qdig_params)) { continue; }
		$params .= $param.'='.$value.'&amp;';
	}
	return $params;
}

/**
* Get the names of image files in a directory.
*/
function getImageFilenames($path)
{
	global $rootdir, $is_readable_disa, $convert_GD, $ignore_img_links,
		$img_links_sort_rev, $img_links_sort_date, $imgname_maxlen, $exclude_gif,
		$excl_main, $excl_imgs, $excl_img_pattern, $extra_paranoia, $ignore_dotfiles;
	if ($excl_main == TRUE && $path == $rootdir) { return; }
	if ($convert_GD == TRUE) {
		$img_exts = '\.jpg$|\.jpeg$|\.jpe$|\.png$';
	} else {
		$img_exts = '\.jpg$|\.jpeg$|\.jpe$|\.png$|\.bmp$';
	}
	if ($exclude_gif == FALSE) {
		$img_exts .= '|\.gif$';
	}
	$pwd_handle = opendir($path);
	$i = 100;
	while (($file = readdir($pwd_handle)) != false) {
		if ($file == '.' || $file == '..') { continue; }
		if ($extra_paranoia == TRUE && is_file($path.'/'.$file)) {
			if (strpos(stripslashes(rawurldecode($file)), '..')
				|| ($file[0] == '.' && $file[1] == '.'))
			{
				securityExit('Updir ("..") is not allowed in a filename.');
			}
			if (strlen($file) > $imgname_maxlen) {
				securityExit('Filename length exceed.  Increase $imgname_maxlen?');
			}
		}
		if ($ignore_dotfiles == TRUE && $file[0] == '.') { continue; }
		if (in_array($file, $excl_imgs)) { continue; }
		if (strpos('*'.$file, $excl_img_pattern)) { continue; }
		if (is_file($path.'/'.$file)
			&& ($is_readable_disa == TRUE || is_readable($path.'/'.$file))
			&& ! ($ignore_img_links == TRUE && is_link($path.'/'.$file))
			&& preg_match("/".$img_exts."/i", $file))
		{
			$mod_date = filemtime($path.'/'.$file).$i;
			$img_files[$mod_date] = $file;
			$i++;
		}
	}
	closedir($pwd_handle);
	if (isset($img_files)) {
		if ($img_links_sort_date == TRUE) {
			ksort($img_files);
		} else {
			natcasesort($img_files);
		}
		foreach($img_files as $img) {
			$sorted_files[]=$img;
		}
		if ($img_links_sort_rev == TRUE) {
			return (array_reverse($sorted_files));
		} else {
			return $sorted_files;
		}
	}
} // End getImageFilenames()

/**
* Check to see if at least one potential gallery directory exists.
*/
function checkForDirs($path)
{
	global $qdig_files_topdir, $cnvrtd_dir, $is_readable_disa,
		$ignore_dir_links, $excl_dirs, $ignore_dotdirs;
	$pwd_handle = opendir($path);
	while (($file = readdir($pwd_handle)) != FALSE) {
		if($file == '..'
			|| $file == '.'
			|| $file == $qdig_files_topdir
			|| $file == $cnvrtd_dir
			|| @in_array($file, $excl_dirs)
			|| ($ignore_dir_links == TRUE && is_link($path.'/'.$file))
			|| ! ($is_readable_disa == TRUE || is_readable($path.'/'.$file)))
		{
			continue;
		}
		if ($ignore_dotdirs == TRUE && $file[0] == '.') { continue; }
		if (is_dir($path.'/'.$file)) {
			$has_dir = TRUE;
			closedir($pwd_handle);
			return $has_dir;
		}
	}
	closedir($pwd_handle);
} // End checkForDirs()

/**
* Check for existence of at least one image file in a directory.
*/
function checkForImgs($path)
{
	global $is_readable_disa, $ignore_img_links, $ignore_dotfiles;
	$pwd_handle = opendir($path);
	while (($file = readdir($pwd_handle)) != FALSE) {
		if(is_dir($path.'/'.$file)
			|| ($ignore_img_links == TRUE && is_link($path.'/'.$file))
			|| ! ($is_readable_disa == TRUE || is_readable($path.'/'.$file)))
		{
			continue;
		}
		if ($ignore_dotfiles == TRUE && $file[0] == '.') { continue; }
		if (preg_match('/\.jpg$|\.jpeg$|\.jpe$|\.png$|\.gif$|\.bmp$/i', $file)) {
			$has_img = TRUE;
			closedir($pwd_handle);
			return $has_img;
		}
	}
	closedir($pwd_handle);
} // End checkForImgs()

/**
* Get the names of gallery directories in a directory.
*
* A gallery directory is one that contains at least one image or potential
* gallery directory.  Returns an array with the dirname and its age.
*/
function getDirNames($path)
{
	global $qdig_files_topdir, $cnvrtd_dir, $is_readable_disa,
		$ignore_dir_links, $dir_nav, $excl_dirs, $ignore_dotdirs;
	$dir_handle = opendir($path);
	while (($file = readdir($dir_handle)) != false) {
		if($file == '..'
			|| $file == '.'
			|| ! is_dir($path.'/'.$file)
			|| ($ignore_dir_links == TRUE && is_link($path.'/'.$file))
			|| ! ($is_readable_disa == TRUE || is_readable($path.'/'.$file))
			|| @in_array($file, $excl_dirs)
			|| $file == $qdig_files_topdir
			|| $file == $cnvrtd_dir)
		{
			continue;
		}
		if ($ignore_dotdirs == TRUE && $file[0] == '.') { continue; }
		$dirs[]=$file;
	}
	closedir($dir_handle);
	if (isset($dirs)) {
		$timeofday = gettimeofday();
		$unixtime = $timeofday['sec'];
		$i = 0;
		natcasesort($dirs);
		foreach($dirs as $dir) {
			if (checkForImgs($path.'/'.$dir) || checkForDirs($path.'/'.$dir)
				&& ($is_readable_disa == TRUE || is_readable($path)))
			{
				$dirmodified = filemtime($path.'/'.$dir);
				$dir_age = 1000 * ($unixtime - $dirmodified) + $i;
				$sorted_dirs[$dir] = $dir_age;
				$i++;
			}
		}
		if (isset($sorted_dirs)) {
			if ($dir_nav['sort_rev'] == TRUE) {
				return (array_reverse($sorted_dirs));
			} else {
				return $sorted_dirs;
			}
		}
	}
} // End getDirNames()

/*
* Get the image number and filename of the requested image.
*
* If none is requested or the file is non-existent, default to the first image.
*/
function getReqdImage()
{
	global $pwd, $imgs, $get_vars, $imgname_maxlen, $extra_paranoia;
	if (empty($imgs)) { return; }
	if (isset($get_vars['Qif'])) {
		$imagefile = stripslashes(rawurldecode($get_vars['Qif']));
		if (strlen($imagefile) > $imgname_maxlen
			|| ($imagefile[0] == '.' && $imagefile[1] == '.'))
		{
			securityExit('Filename (Qif=) is too long or starts with "..".');
		}
		// Redundant, but what the heck.
		if ($extra_paranoia == TRUE
			&& strpos(stripslashes(rawurldecode($imagefile)), '..'))
		{
			securityExit('Updir ("..") not allowed in a filename.');
		}
	}
	if (isset($imagefile) && is_file($pwd.'/'.$imagefile)) {
		$reqd_img_file = $imagefile;
		// Look up the image's index number
		$imgs_flip = array_flip($imgs);
		$reqd_img_num = $imgs_flip[$imagefile];
	} else {
		$reqd_img_file = $imgs[0];
		$reqd_img_num = 0;
	}
	return array('file' => $reqd_img_file, 'num' => $reqd_img_num);
} // End getReqdImage()

/**
* Convert a string to a useful path starting with './'.
*
* click/buzz/./pop//boom/../bam/  becomes  ./click/buzz/pop/boom/bam
*/
function cleanPath($path)
{
	$path = stripslashes(rawurldecode($path));
	$path_array = explode('/', $path);
	$clean_path = '.';
	foreach($path_array as $dir) {
		if ($dir == '' || $dir == '.' || $dir == '..' || $dir == '...') { continue; }
		$clean_path .='/'.$dir;
	}
	return $clean_path;
} // End cleanPath()

/**
* rawurlencode() a file's path but keep the slashes.
*/
function urlPath($path)
{
	$decoded = rawurldecode($path);     // Decode if encoded
	$cleaned = cleanPath($decoded);
	$encoded = rawurlencode($cleaned);  // Encode
	$encoded_path = str_replace('%2F', '/', $encoded);  // replace slashes
	return $encoded_path;
} // End rawurlencodePath()

/**
* Build an 'x of n' counter.
*/
function imageCounter($image_num)
{
	global $imgs, $lang;
	$num_imgs = count($imgs);
	$img_num = $image_num + 1;
	if ($num_imgs > 0) {
		$str = "$img_num {$lang['nav_cntr_txt']} $num_imgs";
		return $str;
	}
} // End imageCounter()

/**
* Get which version of GD is installed, if any.
*
* Returns the version (1 or 2) of the GD extension.
*/
function gdVersion()
{
	global $convert_GD_ver;
	if (! extension_loaded('gd')) { return; }
	if ($convert_GD_ver == 1 || $convert_GD_ver == 2) {
		return $convert_GD_ver;
	}
	// We don't need to use phpinfo() if the PHP version is recent.
	if (version_compare('4.3.2', phpversion(), '<=')) {	return '2';	}
	// Rely on phpinfo() for PHP < 4.3.2, or otherwise use a fail-safe choice.
	if (preg_match('/phpinfo/', ini_get('disable_functions'))) { return '1'; }
	ob_start();
	phpinfo(8);
	$info = ob_get_contents();
	ob_end_clean();
	$info = stristr($info, 'gd version');
	preg_match('/\d/', $info, $gd);
	return $gd[0];
} // End gdVersion()

/**
* Create a directory recursively (like `mkdir -p $dir').
*
* For security './' is prepended if missing.
*/
function mkRecursiveDir($dir)
{
	global $file_exists_disa;
	$path_array = explode('/', $dir);
	$path = '.';
	foreach($path_array as $dir) {
		if ($dir == '' || $dir == '.' || $dir == '..' || $dir == '...') { continue; }
		$path .= '/'.$dir;
		if ((($file_exists_disa == TRUE && ! is_file($path))
				|| ! file_exists($path))
			&& is_writable(dirname($path)))
		{
			mkdir($path, 0777);
		}
	}
} // End mkRecursiveDir()

/**
* Generate thumbnail images for images that do not have thumbnails yet.
*/
function createThumbs($cnvrt_thmb)
{
	global $platform, $pwd, $imgs, $convert_magick, $cnvrt_path, $convert_cmd,
		$convert_GD, $thmbs_ena, $convert_writable, $caption_path,
		$touch_captions, $cnvrt_mesgs, $file_exists_disa, $compat_quote;
	if ($thmbs_ena == FALSE || empty($imgs) || $convert_writable == FALSE) { return; }
	if ($cnvrt_thmb['single'] == FALSE && count($imgs) < 2) { return; }  // one-image gallery
	if ($convert_GD == TRUE && ! ($gd_version = gdVersion())) {return; }
	if (! isset($cnvrt_thmb['size'])) {
		$cnvrt_thmb['size'] = 35;
	}
	if (! isset($cnvrt_thmb['qual'])) {
		$cnvrt_thmb['qual'] = 65;
	}
	if ($cnvrt_thmb['mesg_on'] == TRUE) {
		$str = '';
	}
	if ($convert_magick == TRUE) {
		if ($cnvrt_thmb['no_prof'] == TRUE) {
			$strip_prof = ' +profile "*"';
		} else {
			$strip_prof = '';
		}
		if ($platform == 'Win32' && $compat_quote == TRUE) {
			$winquote = '"';
		} else {
			$winquote = '';
		}
	}
	foreach($imgs as $img_file) {
		if ($touch_captions == TRUE && is_dir($caption_path)) {
			$caption_file = $caption_path.'/'.$img_file.'.txt';
			if (($file_exists_disa == TRUE && ! is_file($caption_file))
				|| ! file_exists($caption_file))
			{
				touch($caption_file);
			}
		}
		$orig_img = $pwd.'/'.$img_file;
		$cnvrtd_img = $cnvrt_path.'/'.$cnvrt_thmb['prefix'].$img_file;
		if (($file_exists_disa == TRUE && ! is_file($cnvrtd_img))
			|| ! file_exists($cnvrtd_img))
		{
			$img_size = GetImageSize($orig_img);
			$height   = $img_size[1];
			$th_maxdim = $height;
			$cnvt_percent = round(($cnvrt_thmb['size'] / $th_maxdim) * 100, 2);
			// convert it
			if ($convert_magick == TRUE) {
				// Image Magick image conversion
				exec($winquote.$convert_cmd
					.' -geometry '.$cnvt_percent.'%'
					.' -quality '.$cnvrt_thmb['qual']
					.' -sharpen '.$cnvrt_thmb['sharpen'].$strip_prof
					.' "'.$orig_img.'"'.' "'.$cnvrtd_img.'"'.$winquote);
				$using = $cnvrt_mesgs['using IM'];
			} elseif ($convert_GD == TRUE) {
				// GD image conversion
				if (preg_match('/\.jpg$|\.jpeg$/i', $img_file) == TRUE
					&& (imageTypes() & IMG_JPG) == TRUE)
				{
					$src_img = imageCreateFromJpeg($orig_img);
				} elseif (preg_match('/\.png$/i', $img_file) == TRUE
					&& (imageTypes() & IMG_PNG) == TRUE)
				{
					$src_img = imageCreateFromPng($orig_img);
				} elseif (preg_match('/\.gif$/i', $img_file) == TRUE
					&& (imageTypes() & IMG_GIF) == TRUE)
				{
					$src_img = imageCreateFromGif($orig_img);
				} else {
					continue;
				}
				$src_width  = imageSx($src_img);
				$src_height = imageSy($src_img);
				$dest_width = $src_width * ($cnvt_percent / 100);
				$dest_height = $src_height * ($cnvt_percent / 100);
				if ($gd_version >= 2) {
					$dst_img = imageCreateTruecolor($dest_width, $dest_height);
					imageCopyResampled($dst_img, $src_img, 0, 0, 0, 0,
						$dest_width, $dest_height, $src_width, $src_height);
				} else {
					$dst_img = imageCreate($dest_width, $dest_height);
					imageCopyResized($dst_img, $src_img, 0, 0, 0, 0,
						$dest_width, $dest_height, $src_width, $src_height);
				}
				imagedestroy($src_img);
				if (preg_match('/\.jpg$|\.jpeg$/i', $img_file) == TRUE
					&& (imageTypes() & IMG_JPG) == TRUE)
				{
					imageJpeg($dst_img, $cnvrtd_img, $cnvrt_thmb['qual']);
				} elseif (preg_match('/\.png$/i', $img_file) == TRUE
					&& (imageTypes() & IMG_PNG) == TRUE)
				{
					imagePng($dst_img, $cnvrtd_img);
				} elseif (preg_match('/\.gif$/i', $img_file) == TRUE
					&& (imageTypes() & IMG_GIF) == TRUE)
				{
					imageGif($dst_img, $cnvrtd_img);
				}
				imagedestroy($dst_img);
				$using = $cnvrt_mesgs['using GD'].$gd_version;
			}
			if ($cnvrt_thmb['mesg_on'] == TRUE && is_file($cnvrtd_img)) {
				$str .= "  <small>\n"
					.'   '.$cnvrt_mesgs['generated']
					.$cnvrt_mesgs['thumb'].$cnvrt_mesgs['image_for']
					.$img_file.$using.".\n"
					."  </small>\n  <br />\n";
			}
		}
	}
	if (isset($str)) {
		return $str;
	}
} // End createThumbs()

/**
* Figure out how big a thumb will be, based on input image
*/
function predictThumbSize($orig_img)
{
	global $cnvrt_thmb;
	$img_size = GetImageSize($orig_img);
	$width    = $img_size[0];
	$height   = $img_size[1];
	$th_maxdim = $height;
	$cnvt_percent = round(($cnvrt_thmb['size'] / $th_maxdim) * 100, 2);
	$dest_width   = round($width * ($cnvt_percent / 100));
	$dest_height  = round($height * ($cnvt_percent / 100));
	$attr = sprintf("height=\"%s\" width=\"%s\"", $dest_height, $dest_width);
	return array($dest_width, $dest_height, $img_size[2], $attr);
} // End predictThumbSize()

/**
* Generate images of alternate sizes.
*/
function resizeImage($cnvrt_arry)
{
	global $platform, $imgs, $cnvrt_path, $reqd_image, $convert_writable,
		$convert_magick, $convert_GD, $convert_cmd, $cnvrt_alt, $cnvrt_mesgs,
		$compat_quote;
	if (empty($imgs) || $convert_writable == FALSE) { return; }
	if ($convert_GD == TRUE && ! ($gd_version = gdVersion())) {return; }
	if ($cnvrt_alt['no_prof'] == TRUE) {
		$strip_prof = ' +profile "*"';
	} else {
		$strip_prof = '';
	}
	if ($cnvrt_alt['mesg_on'] == TRUE) {
		$str = '';
	}
	foreach($imgs as $img_file) {
		if ($cnvrt_alt['indiv'] == TRUE && $img_file != $reqd_image['file']) { continue; }
		$orig_img   = $reqd_image['pwd'].'/'.$img_file;
		$cnvrtd_img = $cnvrt_path.'/'.$cnvrt_arry['prefix'].$img_file;
		if (! is_file($cnvrtd_img)) {
			$img_size = GetImageSize($orig_img);
			$height  = $img_size[1];
			$width   = $img_size[0];
			$area    = $height * $width;
			$maxarea = $cnvrt_arry['maxwid'] * $cnvrt_arry['maxwid'] * 0.9;
			$maxheight = ($cnvrt_arry['maxwid'] * .75 + 1);
			if ($area > $maxarea
				|| $width > $cnvrt_arry['maxwid']
				|| $height > $maxheight)
			{
				if (($width / $cnvrt_arry['maxwid']) >= ($height / $maxheight)) {
					$dim = 'W';
				}
				if (($height / $maxheight) >= ($width / $cnvrt_arry['maxwid'])) {
					$dim = 'H';
				}
				if ($dim == 'W') {
					$cnvt_percent = round(((0.9375 * $cnvrt_arry['maxwid']) / $width) * 100, 2);
				}
				if ($dim == 'H') {
					$cnvt_percent = round(((0.75 * $cnvrt_arry['maxwid']) / $height) * 100, 2);
				}
				// convert it
				if ($convert_magick == TRUE) {
					// Image Magick image conversion
					if ($platform == 'Win32'
						&& $compat_quote == TRUE)
					{
						$winquote = '"';
					} else {
						$winquote = '';
					}
					exec($winquote.$convert_cmd
						.' -geometry '.$cnvt_percent.'%'
						.' -quality '.$cnvrt_arry['qual']
						.' -sharpen '.$cnvrt_arry['sharpen'].$strip_prof
						.' "'.$orig_img.'"'.' "'.$cnvrtd_img.'"'.$winquote);
					$using = $cnvrt_mesgs['using IM'];
				} elseif ($convert_GD == TRUE) {
					// GD image conversion
					if (preg_match('/\.jpg$|\.jpeg$/i', $img_file) == TRUE
						&& (imageTypes() & IMG_JPG) == TRUE)
					{
						$src_img = imageCreateFromJpeg($orig_img);
					} elseif (preg_match('/\.png$/i', $img_file) == TRUE
						&& (imageTypes() & IMG_PNG) == TRUE)
					{
						$src_img = imageCreateFromPng($orig_img);
					} elseif (preg_match('/\.gif$/i', $img_file) == TRUE
						&& (imageTypes() & IMG_GIF) == TRUE)
					{
						$src_img = imageCreateFromGif($orig_img);
					} else {
						continue;
					}
					$src_width  = imageSx($src_img);
					$src_height = imageSy($src_img);
					$dest_width = $src_width * ($cnvt_percent / 100);
					$dest_height = $src_height * ($cnvt_percent / 100);
					if ($gd_version >= 2) {
						$dst_img = imageCreateTruecolor($dest_width, $dest_height);
						imageCopyResampled($dst_img, $src_img, 0, 0, 0, 0,
							$dest_width, $dest_height, $src_width, $src_height);
					} else {
						$dst_img = imageCreate($dest_width, $dest_height);
						imageCopyResized($dst_img, $src_img, 0, 0, 0, 0,
							$dest_width, $dest_height, $src_width, $src_height);
					}
					imageDestroy($src_img);
					if (preg_match('/\.jpg$|\.jpeg$/i', $img_file) == TRUE
						&& (imageTypes() & IMG_JPG) == TRUE)
					{
						imageJpeg($dst_img, $cnvrtd_img, $cnvrt_arry['qual']);
					} elseif (preg_match('/\.png$/i', $img_file) == TRUE
						&& (imageTypes() & IMG_PNG) == TRUE)
					{
						imagePng($dst_img, $cnvrtd_img);
					} elseif (preg_match('/\.gif$/i', $img_file) == TRUE
						&& (imageTypes() & IMG_GIF) == TRUE)
					{
						imageGif($dst_img, $cnvrtd_img);
					}
					imageDestroy($dst_img);
					$using = $cnvrt_mesgs['using GD'].$gd_version;
				}
				if ($cnvrt_alt['mesg_on'] == TRUE
					&& is_file($cnvrtd_img))
				{
					$str .= "  <small>\n"
						.'   '.$cnvrt_mesgs['generated'].$cnvrt_arry['txt'].$cnvrt_mesgs['converted']
						.$cnvrt_mesgs['image_for'].$img_file.$using.".\n"
						."  </small>\n  <br />\n";
				}
			}
		}
	}
	if (isset($str)) {
		return $str;
	}
} //End resizeImage()

/**
* Produce the HTML header for a stand-alone gallery.
*
* Only produce a header if this is a stand-alone gallery.
*/
function htmlHeader($header)
{
	global $is_included, $imgs, $qdig_url, $request_uri, $anchor, $extra_param,
		$subdirs, $reqd_image, $is_readable_disa, $dir_nav;
	if ($header['force_disa'] == TRUE) { return ''; }
	$str = '';
	if ($header['force_ena'] == TRUE || $is_included == FALSE) {
		if (!empty($header['title_text_1'])) {
			$title = $header['title_text_1'];
		}
		$title_loc = basename($reqd_image['pwd']);
		if ($title_loc != '.') {
			$title .= " {$header['title_delim']} $title_loc";
		} elseif (isset($subdirs)) {
			$title .= " {$header['title_delim']} {$dir_nav['main_txt']}";
		}
		if ($header['title_cntr'] == TRUE
			&& isset($reqd_image['num'])
			&& $title_cntr = imageCounter($reqd_image['num']))
		{
			$title .= " {$header['title_delim']} $title_cntr";
		}
		if (!empty($header['title_text_2'])) {
			$title .= " {$header['title_delim']} {$header['title_text_2']}";
		}
		if ($header['img_name'] == TRUE && isset($reqd_image['file'])) {
			$title .= " {$header['title_delim']} {$reqd_image['file']}";
		}
		if ($header['meta_cache'] == TRUE) {
			$meta_cache = '<meta http-equiv="Cache-Control" content="max-age='.$header['cache_sec']."\" />\n ";
		} else {
			$meta_cache = '';
		}
		if (!empty($header['css_bg_img_url'])) {
			$bg_image = 'body { background-image: url("'.$header['css_bg_img_url'].'"); background-attachment: fixed; }'."\n  ";
		} elseif ($header['css_bg_logo'] == TRUE) {
			if (!empty($header['css_logo_url'])
				&& is_file($header['css_logo_url']))
			{
				$icon = $header['css_logo_url'];
			} else {
				$icon = "$qdig_url?image=cam-icon";
			}
			$bg_image = "body { background-image:url(\"$icon\");"
				."background-position:{$header['css_logo_pos']};\n"
				."   background-repeat:no-repeat; background-attachment:fixed; }\n  ";
		} else {
			$bg_image = '';
		}
		if ($header['ie_imgbar_off'] == TRUE) {
			$ie_imgtoolbar = '<meta http-equiv="imagetoolbar" content="no" />'."\n ";
		} else {
			$ie_imgtoolbar = '';
		}
		if ($header['zap_frames'] == TRUE) {
			$zap_frames = ' <script type="text/javascript"> <!--'
				."\n  if (top.frames.length > 1) { top.location=\"$request_uri\"; }\n"
				."  // -->\n </script>\n";
		} else {
			$zap_frames = '';
		}
		$str = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="{$header['lang_code']}">
<head>
 $meta_cache<meta http-equiv="Content-Type" content="text/html; charset={$header['charset']}" />
 <meta http-equiv="Content-Language" content="{$header['lang_code']}" />
 <meta http-equiv="Content-Style-Type" content="text/css" />
 $ie_imgtoolbar<meta name="description" content="An online image gallery" />
 <meta name="keywords" content="qdig,image gallery,photo album,online gallery,web photo album,digital image gallery,web gallery,photos,images,album,gallery,digital images,digital camera,digital photos,digicam,digital camera photos,presentation,presentation software" />
 <meta name="robots" content="index,follow" />
 <meta name="MSSmartTagsPreventParsing" content="true" />\n
EOT;
		if ($header['nav_links'] == TRUE) {
			if ($reqd_image['pwd'] != '.') {
				$str .= ' <link rel="top" href="'.$qdig_url.'?'.$extra_param.'Qwd=.&amp;Qiv='
					.$reqd_image['view'].'&amp;Qis='.$reqd_image['size'].$anchor.'" />'."\n";
				$up_one = urlPath(dirname($reqd_image['pwd']));
				$str .= ' <link rel="up" href="'.$qdig_url.'?'.$extra_param.'Qwd='.$up_one
					.'&amp;Qiv='.$reqd_image['view'].'&amp;Qis='.$reqd_image['size'].$anchor.'" />'
					."\n";
			}
			if (!empty($imgs) && $reqd_image['num'] != 0) {
				$first_img = rawurlencode($imgs[0]);
				$str .= ' <link rel="first" href="'.$qdig_url.'?'.$extra_param.'Qwd='.$reqd_image['pwd']
					.'&amp;Qif='.$first_img.'&amp;Qiv='.$reqd_image['view']
					.'&amp;Qis='.$reqd_image['size'].$anchor.'" />'."\n";
				$prev_img = rawurlencode($imgs[$reqd_image['num'] - 1]);
				$str .= ' <link rel="prev" href="'.$qdig_url.'?'.$extra_param.'Qwd='
					.$reqd_image['pwd'].'&amp;Qif='.$prev_img.'&amp;Qiv='.$reqd_image['view']
					.'&amp;Qis='.$reqd_image['size'].$anchor.'" />'."\n";
			}
			$num_imgs = count($imgs);
			if (!empty($imgs) && $reqd_image['num'] + 1 != $num_imgs) {
				$next_img = rawurlencode($imgs[$reqd_image['num'] + 1]);
				$str .= ' <link rel="next" href="'.$qdig_url.'?'.$extra_param.'Qwd='.$reqd_image['pwd']
					.'&amp;Qif='.$next_img.'&amp;Qiv='.$reqd_image['view'].'&amp;Qis='
					.$reqd_image['size'].$anchor.'" />'."\n";
				$last_img = rawurlencode($imgs[$num_imgs - 1]);
				$str .= ' <link rel="last" href="'.$qdig_url.'?'.$extra_param.'Qwd='.$reqd_image['pwd']
					.'&amp;Qif='.$last_img.'&amp;Qiv='.$reqd_image['view'].'&amp;Qis='
					.$reqd_image['size'].$anchor.'" />'."\n";
			}
		}
		if ($header['css_thm_opacity'] > 0
			&& $header['css_thm_opacity'] < 100)
		{
			$alpha = $header['css_thm_opacity'];
			$opacity = $alpha / 100;
			if ($header['css_opacity_moz'] == TRUE) {
				$moz_opacity = " -moz-opacity:$opacity;";
				$moz_opacity_cur = ' -moz-opacity:100;';
			} else {
				$moz_opacity = '';
				$moz_opacity_cur = '';
			}
			$thm_opacity = "
    filter:alpha(opacity=$alpha);$moz_opacity opacity:$opacity;";
			$thm_opacity_curr = "
    filter:alpha(opacity=100);$moz_opacity_cur opacity:1.0;";
		} else {
			$thm_opacity = '';
			$thm_opacity_curr = '';
		}
		if ($header['icon'] == TRUE) {
			$str .= <<<EOT
 <link rel="icon" href="$qdig_url?image=cam-icon" type="image/png" />
 <link rel="SHORTCUT ICON" href="$qdig_url?image=cam-icon" type="image/png" />\n
EOT;
		}
		$str .= <<<EOT
 <title>$title</title>
 <style type="text/css"> <!--
  /* Bare-bones CSS style properties for a stand-alone Qdig gallery */
  body { font-family:Arial, Helvetica, Geneva, sans-serif;
    background-color:{$header['css_bgcolor']}; margin:1px; }
  {$bg_image}body, td { font-size:14px; color:{$header['css_text_color']}; }
  small { font-size:0.85em; }
  a { font-weight:bold; color: {$header['css_link_color']};
    text-decoration: none; }
  a:visited { font-weight:bold; color:{$header['css_visit_color']};
    text-decoration:none; }
  a:hover { text-decoration:underline; }
  img { border:0px; }
  img.qdig-image { background-color:{$header['css_img_bg']};
    border:{$header['css_img_brdr_w']} solid {$header['css_img_border']}; }
  img.qdig-thumb { background-color:{$header['css_img_bg']};
    border:{$header['css_thm_brdr_w']} solid {$header['css_thm_border']};$thm_opacity }
  img#qdig-thumb-current { border-color:{$header['css_thm_hilite']};
    border-left:{$header['css_thm_hl_w']} solid {$header['css_thm_hilite']};
    border-right:{$header['css_thm_hl_w']} solid {$header['css_thm_hilite']};$thm_opacity_curr }
  div.qdig-caption { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:13px; }
  .qdig-grayout {  } {$header['css_user_def']} // -->
 </style>
$zap_frames</head>
<body>
<div align="center" style="padding-top:0px;">\n
EOT;
	}
	return $str;
} // End htmlHeader()

/**
* Produce the HTML footer for a stand-alone gallery.
*
* Only produce a footer if this script is running stand-alone.
*/
function htmlFooter($header)
{
	global $is_included;
	if ($header['force_disa'] == TRUE) { return "\n"; }
	$str = "\n";
	if ($header['force_ena'] == TRUE || $is_included == FALSE) {
		$str = "</div>\n</body>\n</html>";
	}
	return $str;
} // End htmlFooter()

/**
* Display the current gallery directory with links to higher-level directories.
*/
function dirnavPath($dir_nav)
{
	global $imgs, $subdirs, $qdig_url, $extra_param, $anchor, $reqd_image,
		$dir_nav, $rootdir;
	$i        = 0;
	$num_imgs = count($imgs);
	$split_path = explode('/', urlPath($reqd_image['pwd']));
	$path_pos = count($split_path);
	$split_root = explode('/', urlPath($rootdir));
	$root_pos = count($split_root);
	$path     = $rootdir;
	$str      = '';
	$path_delim = $dir_nav['path_delim'];
	foreach($split_path as $dir) {
		$i++;
		if ($i < $root_pos) { continue; }
		if ($dir == $split_path[$root_pos - 1] && $reqd_image['pwd'] == $rootdir) {
			if (empty($imgs) && !empty($dir_nav['choose_main_txt'])) {
				$str = '<span title="'.$dir_nav['choose_main_title_txt'].'">'
					.$dir_nav['choose_main_txt'].'</span><br />'."\n";
			} elseif (empty($imgs)) {
				$str = '<b>'.$dir_nav['main_txt'].'</b><br />'."\n";
			} else {
				$str = '<b>'.$dir_nav['main_txt'].'</b>'."\n";
			}
			if ($dir_nav['fname_ena'] == TRUE && !empty($reqd_image['file'])) {
				$str .= ' '.$dir_nav['path_delim'].' '.$reqd_image['file'];
			}
			if ($dir_nav['cntr_ena'] == TRUE && $num_imgs > 1) {
				$str .= '&nbsp; (';
				if (! $dir_nav['fname_ena'] == TRUE) {
					$str .= $dir_nav['image_txt'].' ';
				}
				$str .= imageCounter($reqd_image['num']).") \n";
			}
			continue;
		} elseif ($dir == $split_path[$root_pos - 1]) {
			$str .= <<<EOT
    <a href="$qdig_url?{$extra_param}Qwd=.&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
     title="{$dir_nav['go_to_txt']} {$dir_nav['main_txt']}">{$dir_nav['main_txt']}</a>\n
EOT;
			continue;
		}
		$path = $path.'/'.$dir;
		$dirlabel = rawurldecode($dir);
		if ($i < $path_pos) {
			$str .= <<<EOT
    {$dir_nav['path_delim']} <a href="$qdig_url?{$extra_param}Qwd=$path&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
     title="{$dir_nav['go_to_txt']} $dirlabel">$dirlabel</a>
EOT;
		} else {
		$str .= <<<EOT
    {$dir_nav['path_delim']} <span title="{$dir_nav['current_txt']} $dirlabel"><b>$dirlabel</b></span>
EOT;
			if ($dir_nav['fname_ena'] == TRUE && $num_imgs > 0) {
				$str .= ' '.$dir_nav['path_delim'].' '.$reqd_image['file'];
			}
			if ($dir_nav['cntr_ena'] == TRUE && $num_imgs > 1) {
				$str .= '&nbsp; (';
				if (! $dir_nav['fname_ena'] == TRUE) {
					$str .= $dir_nav['image_txt'].' ';
				}
				$str .= imageCounter($reqd_image['num']).") \n";
			}
			if (empty($imgs) && !empty($subdirs)) {
				if (!empty($dir_nav['choose_sub_txt'])) {
					$str .= '<br /><span title="'.$dir_nav['choose_sub_title_txt']
						.'">'.$dir_nav['choose_sub_txt']. "</span><br />\n";
				} else {
					$str .= "<br />\n";
				}
			} elseif (empty($imgs)) {
				$str .= '<br /><br /><span title="'.$dir_nav['empty_dir_title_txt']
					.'">'.$dir_nav['empty_dir_txt']."</span>\n";
			}
		}
	}
	return $str;
} // End dirnavPath()

/**
* Produce navigation links to subdirectory galleries.
*/
function subdirLinks($dir_nav)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $subdirs,
		$rootdir;
	if (isset($imgs)) {
		if ($dir_nav['icon'] == 1) {
			$icon = '<img src="'.$qdig_url.'?image=cam-icon" alt="camera icon" />&nbsp;';
		} else {
			$icon = '';
		}
		$tag1 = '&nbsp;';
		$bkt1 = '<b>[</b>';
		$bkt2 = '<b>]</b>';
		$tag2 = '';
	} else {
		$icon = '';
		$tag1 = '<br />';
		$bkt1 = '';
		$bkt2 = '';
		$tag2 = '<br />';
	}
	$str = '';
	if (isset($subdirs)) {
		if ($dir_nav['sort_age'] == TRUE) {
			if ($dir_nav['sort_rev'] == TRUE) {
				arsort($subdirs);
			} else {
				asort($subdirs);
			}
		}
		foreach($subdirs as $dir => $age_idx) {
			$dirurl = rawurlencode($dir);
			$dirtxt = str_replace(' ', '&nbsp;', $dir);  // replace spaces
			$dirtxt = str_replace("'", '&#39;', $dirtxt);   // replace apostrophes
			if ($age_idx < ($dir_nav['dir_is_new'] * 1000)) {
				$newdir = $dir_nav['new_flag'];
			} else {
				$newdir = '';
			}
			$str .= <<<EOT
    <span style='white-space:nowrap;'>$tag1$bkt1
     <a  href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}/$dirurl&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
      title="{$dir_nav['go_to_txt']} $dir">$icon$dirtxt$newdir</a>
    $bkt2</span>\n
EOT;
		}
	}
	if ($reqd_image['pwd'] != $rootdir && $dir_nav['updir_ena'] == TRUE) {
		$up_one = urlPath(dirname($reqd_image['pwd']));
		$str .= <<<EOT
    <span style='white-space:nowrap;'>$tag2$tag1$bkt1
     <a href="$qdig_url?{$extra_param}Qwd=$up_one&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
      title="{$dir_nav['up_title_txt']}"> {$dir_nav['up_level_txt']}</a>
    $bkt2</span>\n
EOT;
	}
	return $str;
} // End subdirLinks()

/**
* Build a list of Text Name or Text Numeral Links to the images.
*/
function imageTextLinks($nmrl_row)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $namelinks_ena,
		$namelinks_small, $namelinks_trunc, $nav_lnk;
	if (count($imgs) > 1) {
		if ($namelinks_ena == FALSE) {
			$str = " <!-- text navigation links -->\n";
			$str .= ' <div align="center" style="padding-top:'.$nmrl_row['pad_top'].';">'."\n";
			// prepare wrap data
			$links_count = 0;
			$links_wrap = -1;
			$links_wide = 0;
			foreach($imgs as $x) {
				$links_count++;
				$links_wrap++;
				if ($links_count > $nmrl_row['maxcount'])
				{
					$links_count = $links_wrap;
					$wrapped = FALSE;
					$links_wide++;
				}
				if ($links_wrap > ($nmrl_row['maxcount'] * ($nmrl_row['softwrap'] / 100))
					&& $wrapped == FALSE)
				{
					$links_wrap = 0;
					$wrap[]  = TRUE;
					$wrapped = TRUE;
				} else {
					$wrap[]  = FALSE;
					$wrapped = FALSE;
				}
			} // end prepare wrap data
			if ($nmrl_row['small'] == TRUE) {
				$tag_bfr_num_lnk = '<small>';
				$tag_aft_num_lnk = '</small>';
			} else {
				$tag_bfr_num_lnk = '';
				$tag_aft_num_lnk = '';
			}
		} else {
			$str = '';
			if ($namelinks_small == TRUE) {
				$tag_bfr_name_lnk = '<small>';
				$tag_aft_name_lnk = '</small>';
			} else {
				$tag_bfr_name_lnk = '';
				$tag_aft_name_lnk = '';
			}
		}
		$imgs_flipped = array_flip($imgs);
		foreach($imgs_flipped as $img_num) {
			if (isset($wrap)
				&& $wrap[$img_num] == TRUE
				&& $links_wide > 0
				&& $namelinks_ena == FALSE)
			{
				$str .= " </div><!-- wrap numerals row -->\n";
				$str .= " <div align=\"center\" style=\"padding-top:{$nmrl_row['pad_top']};\">\n";
				$links_wide--;
			}
			// pad single- and double-numeral links
			if ($img_num < 10) {
				$pad = '&nbsp;';
			} else {
				$pad = '';
			}
			if ($img_num < 100) {
				$pad .= '&nbsp;';
			}
			$num = $img_num + 1;
			$img = $imgs[$img_num];
			$imgurl = rawurlencode($img);
			$str .= <<<EOT
  <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$imgurl&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
   title="{$nav_lnk['image']} $num - $img">
EOT;
			if ($namelinks_ena == TRUE) {
				// Strip extension from filename
				$ext = strrchr($img, '.');
				$img = substr($img, 0, -strlen($ext));
				// Truncate long names
				if (strlen($img) > $namelinks_trunc) {
					$img_lnk_txt = substr($img, 0, $namelinks_trunc - 2).'...';
				} else {
					$img_lnk_txt = $img;
				}
				$str .= <<<EOT
$tag_bfr_name_lnk&nbsp;$img_lnk_txt&nbsp;$tag_aft_name_lnk</a><br />\n
EOT;
			} else {
				$num = $img_num + 1;
				$str .= <<<EOT
$tag_bfr_num_lnk$pad$num$pad$tag_aft_num_lnk</a>&nbsp;\n
EOT;
			}
		}
		if ($namelinks_ena == FALSE) {
			$str .= " </div>\n";
		}
		return $str;
	}
} //End imageTextLinks()

/**
* Build a list of Thumbnail Image Links to the images in the current directory.
*/
function imageThumbsLinks($thmb_row)
{
	global $pwd, $imgs, $qdig_url, $extra_param, $anchor, $reqd_image,
		$cnvrt_path, $cnvrt_thmb, $nav_lnk, $thumbs_msg, $thmb_onfly,
		$file_exists_disa, $cnvrt_mesgs, $url_base_path, $diag_messages,
		$safe_mode, $header;
	// set a default maxwidth (if necessary)
	if (empty($thmb_row['maxwidth'])) {
		$thmb_row['maxwidth'] = 500;
	}
	// set a default softwrap (if necessary)
	if (empty($thmb_row['softwrap'])
		|| $thmb_row['softwrap'] < 50
		|| $thmb_row['softwrap'] > 99)
	{
		$thmb_row['softwrap'] = 75;
	}
	if (isset($imgs) && count($imgs) > 1) {
		$str = " <!-- thumbnail-image links -->\n";
		$str .= " <div align=\"center\" style=\"padding-top:2px; white-space:nowrap\">\n";
		// prepare wrap data
		$thumbs_width = 0;
		$thumbs_wrap = 0;
		$thumbs_wide = 0;
		$num     = 0;
		$wrapped = FALSE;
		foreach($imgs as $image) {
			$thmbs[$image]['image']  = $image;
			$thmb_file = $cnvrt_path.'/'.$cnvrt_thmb['prefix'].$image;
			$thmbs[$image]['thumb']  = $thmb_file;
			$thmb      = $cnvrt_thmb['prefix'].rawurlencode($image);
			$thmbs[$image]['cnvurl'] = $url_base_path.urlPath($cnvrt_path)."/$thmb";
			$imgurl    = rawurlencode($image);
			$thmbs[$image]['imgurl'] = $imgurl;
			$num++;
			$thmbs[$image]['num'] = $num;
			if (($file_exists_disa == TRUE && is_file($thmb_file))
				|| file_exists($thmb_file))
			{
				$exists = TRUE;
				$thmbs[$image]['exists'] = TRUE;
			} else {
				$exists = FALSE;
				$thmbs[$image]['exists'] = FALSE;
			}
			# RAR... get either the size of the real thumb, or the size it will be
			if ($exists == TRUE) {
				if ($diag_messages == TRUE) {
					if ($safe_mode == TRUE) {
						$img_size = predictThumbSize($pwd.'/'.$image);
					} else {
						$img_size = GetImageSize($thmb_file);
					}
				} else {
					if ($safe_mode == TRUE) {
						$img_size = predictThumbSize($pwd.'/'.$image);
					} else {
						$img_size = @GetImageSize($thmb_file);
					}
				}
			} elseif ($thmb_onfly == TRUE) {
				# This thumb doesn't exist yet, the URL will be one to make the conversion.
				$img_size = predictThumbSize($pwd.'/'.$image);
				$thmbs[$image]['cnvurl'] = <<<EOT
$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$imgurl&amp;Makethumb=Y
EOT;
				# Add to the thumbs message to let the user know we're going to make on fly.
				if ($cnvrt_thmb['mesg_on'] == TRUE) {
					$thumbs_msg .= "  <small>\n"
						.'   '.$cnvrt_mesgs['generating']
						.$cnvrt_mesgs['thumb'].$cnvrt_mesgs['image_for']
						.$image.$cnvrt_mesgs['on-the-fly'].".\n"
						."  </small>\n  <br />\n";
				}
			} else {
				$img_size = predictThumbSize($pwd.'/'.$image);
			}
			$thumb_width  = $img_size[0] + ($header['css_thm_brdr_w'] * 2);
			$thumbs_width = $thumbs_width + $thumb_width;
			$thumbs_wrap  = $thumbs_wrap + $thumb_width;
			if ($thumbs_width > $thmb_row['maxwidth']) {
				$thumbs_width = $thumbs_wrap;
				$wrapped = FALSE;
				$thumbs_wide++;
			}
			$thmbs[$image]['img_size'] = $img_size;
			if ($thumbs_wrap > ($thmb_row['maxwidth'] * ($thmb_row['softwrap'] / 100))
				&& $wrapped == FALSE)
			{
				$thumbs_wrap = 0;
				$thmbs[$image]['wrap'] = TRUE;
				$wrap[]  = TRUE;
				$wrapped = TRUE;
			} else {
				$thmbs[$image]['wrap'] = FALSE;
				$wrap[] = FALSE;
			}
		} // end prepare wrap data
		foreach($thmbs as $thm) {
			$thumb_id = '';
			if ($reqd_image['num'] == $thm['num'] - 1) {
				$thumb_id = 'id="qdig-thumb-current"';
			}
			if ($thm['wrap'] == TRUE && $thumbs_wide > 0) {
				$str .= " </div><!-- wrap thubms row -->\n"
					." <div align=\"center\" style=\"padding-top:2px; white-space:nowrap;\">\n";
				$thumbs_wide--;
			}
			if ($thmb_onfly == FALSE
				&& ! (($file_exists_disa == TRUE && is_file($thm['thumb']))
					|| file_exists($thm['thumb'])))
			{
				$thm['cnvurl'] = "$qdig_url?image=clear-dot";
			}
			$str .= <<<EOT
  <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif={$thm['imgurl']}&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
   title="{$nav_lnk['image']} {$thm['num']} - {$thm['image']}"><img class="qdig-thumb" $thumb_id
   src="{$thm['cnvurl']}"
   alt="{$thm['image']}" {$thm['img_size']['3']} /></a>\n
EOT;
		}
		$str .= " </div>\n";
		return $str;
	}
} // End imageThumbsLinks()

/**
* Produce a Directory Navigation Row.
*
* Contains the path to the current gallery directory and subdirectory links.
*/
function dirNav($dir_nav)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $subdirs,
		$reqd_img_size_tmp, $chroot_dir;
	// Skip it if there are no subdirectories
	if (! isset($subdirs)
		&& ((empty($chroot_dir) && $reqd_image['pwd'] == '.')
			|| (!empty($chroot_dir) && $reqd_image['pwd'] == './'.$chroot_dir )))
	{
		return array('str' => '', 'ctrl_bar_ena' => TRUE);
	}
	// Show it if it's enabled.
	if ($dir_nav['enable'] == FALSE) { return array('str' => '', 'ctrl_bar_ena' => TRUE); }
	$str = <<<EOT
\n <!-- directory navigation -->
 <td colspan="2">
 <table summary="Directory Navigation"
  cellpadding="0" cellspacing="0" width="{$dir_nav['row_width']}">
  <tr>
   <td align="center">
EOT;
	if ($dir_nav['small'] == TRUE) {
		$str .= '    <small>';
	}
	// Display path to current directory / image.
	$str .= dirnavPath($dir_nav);
	// Display navigation links to subdirectories.
	$str .= subdirLinks($dir_nav);
	// Preferences Link
	if ($dir_nav['prefs_ena'] == TRUE) {
		$ctrl_bar_ena = FALSE;
	} else {
		$ctrl_bar_ena = TRUE;
	}
	if ($dir_nav['prefs_ena'] == TRUE && count($imgs) > 1) {
		$ctrl_bar_ena = FALSE;
		if ($reqd_img_size_tmp == 'Ctrl') {
			$qtmp_ctrl = '';
		} else {
			$qtmp_ctrl = 'Ctrl';
		}
		$imgurl = rawurlencode($reqd_image['file']);
		$str .= <<<EOT
    &nbsp; <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$imgurl&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}&amp;Qtmp=$qtmp_ctrl$anchor"
     title="{$dir_nav['prefs_title_txt']}">{$dir_nav['prefs_txt']}</a>\n
EOT;
	}
	if ($dir_nav['small'] == TRUE) {
		$str .= '    </small>';
	}
	if (empty($imgs)) {
		$tag = '<tr><td>';
	} else {
		$tag = '<tr>';
	}
	$str .= <<<EOT
   </td>
  </tr>
 </table>
 </td>
 </tr>$tag\n
EOT;
	return array('str' => $str,	'ctrl_bar_ena' => $ctrl_bar_ena);
} // End dirNav()

/**
* Display the requested image.
*
* Show the requested image and the caption, if any.
*/
function displayImage($reqd_image, $labels)
{
	global $get_vars, $imgs, $qdig_url, $rootdir, $extra_param, $anchor,
		$cnvrt_path, $reqd_img_size_tmp, $nav_lnk, $cnvrt_size, $subdirs,
		$is_readable_disa, $img_link, $caption, $url_base_path, $safe_mode,
		$omit_image, $dir_nav;
	if (empty($imgs) || @$omit_image == TRUE) { return ''; }
	$str = '';
	foreach($cnvrt_size as $size_info) {
		if ($reqd_img_size_tmp == $size_info['label']
			|| (! isset($size_string) && $reqd_image['size'] == $size_info['label']))
		{
			$cnvrt_url   = urlPath($cnvrt_path);
			$size_string = $cnvrt_path.'/'.$size_info['prefix'];
			$size_str_url = $cnvrt_url.'/'.$size_info['prefix'];
			if (! is_file($size_string.$reqd_image['file'])
				|| ! ($is_readable_disa == TRUE || is_readable($size_string.$reqd_image['file'])))
			{
				$size_string = $reqd_image['pwd'].'/';
				$size_str_url = $reqd_image['pwd_url'].'/';
			}
		}
	}
	if (! isset($size_string)) {
		$size_string = $reqd_image['pwd'].'/';
		$size_str_url = $reqd_image['pwd_url'].'/';
	}
	$str = "\n <!-- requested image -->\n";
	if (@$get_vars['Qtmp'] == 'popup') {
		$pad = 'padding:0px;';
	} else {
		$pad = 'padding-top:2px;';
	}
	$str .= " <div align=\"center\" style=\"$pad\">\n";
	if (! is_file($reqd_image['pwd'].'/'.$reqd_image['file'])) {
		$img_file = $imgs[0];
		$img_num = 1;
	} else {
		$img_file = $reqd_image['file'];
		$img_num = $reqd_image['num'];
	}
	$num_imgs = count($imgs);
	if ($safe_mode == FALSE) {
		$img_size = GetImageSize($size_string.$img_file);
	} else {
		$img_size[0] = '';
		$img_size[3] = '';
	}
	if ($caption['above'] == TRUE) {
		$str .= captionBlock($img_file, $img_size[0]);
	}
	if ($img_link['wrap_up'] == TRUE 
		&& $reqd_image['pwd'] == $rootdir)
	{
		$img_link['wrap_up'] = FALSE;	
	}
	$img_url = $size_str_url.rawurlencode($img_file);
	if (@$get_vars['Qtmp'] == 'popup') {
		$str .= <<<EOT
  <a href="javascript:window.close();" title='Close Window'
  ><img class="qdig-image" src="$url_base_path$img_url"
     alt="$img_file" {$img_size['3']} /></a>
EOT;
	} else if ($num_imgs > 0) {
		if ($reqd_img_size_tmp == $cnvrt_size['4']['label']
			|| $reqd_img_size_tmp == $cnvrt_size['3']['label']
			|| $reqd_img_size_tmp == $cnvrt_size['2']['label']
			|| $reqd_img_size_tmp == $cnvrt_size['1']['label']
			|| $reqd_img_size_tmp == $cnvrt_size['0']['label']
			|| ($img_link['full'] == TRUE
				&& $size_string != $reqd_image['pwd'].'/')
				&& $reqd_image['size'] != $cnvrt_size['4']['label'])
		{
			if ($reqd_img_size_tmp != $cnvrt_size['4']['label']
				&& ! ($reqd_img_size_tmp == $cnvrt_size['3']['label']
					|| $reqd_img_size_tmp == $cnvrt_size['2']['label']
					|| $reqd_img_size_tmp == $cnvrt_size['1']['label']
					|| $reqd_img_size_tmp == $cnvrt_size['0']['label']))
			{
				$qtmp_parm  = "&amp;Qtmp={$cnvrt_size['4']['label']}";
				$title_txt1 = $labels['nav']['str1'];
				$title_txt2 = $cnvrt_size['4']['txt'];
			} else {
				$qtmp_parm  = '';
				$title_txt1 = $labels['nav']['str1a'];
				$title_txt2 = $cnvrt_size['4']['txt2'];
			}
			$title    = $title_txt1.$title_txt2.$labels['nav']['str2'];
			$file_url = rawurlencode($reqd_image['file']);
			$direct   = urlPath($reqd_image['pwd'].'/'.$img_file);
			if ($img_link['full'] == TRUE
				&& $img_link['file'] == TRUE
				&& $size_string != $reqd_image['pwd'].'/'
				&& $reqd_image['size'] != $cnvrt_size['4']['label'])
			{
				$str .= <<<EOT
  <a href="$url_base_path$direct" target="_top" title="$title"><img class="qdig-image" src="$url_base_path$img_url"
   alt="$img_file" {$img_size['3']} /></a>\n
EOT;
			} else {
				$str .= <<<EOT
  <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$file_url&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$qtmp_parm$anchor"
   title="$title"><img class="qdig-image" src="$url_base_path$img_url"
   alt="$img_file" {$img_size['3']} /></a>\n
EOT;
			}
		} elseif (is_file($reqd_image['pwd'].'/'.$img_file)
			&& $img_num == $num_imgs - 1)
		{
			if ($img_link['next'] == TRUE
				&& $img_link['wrap'] == TRUE
				&& $img_link['full'] == FALSE)
			{
				$first_url = rawurlencode($imgs[0]);
				$str .= <<<EOT
  <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$first_url&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
   title="{$nav_lnk['frst_msg']}"><img class="qdig-image" src="$url_base_path$img_url"
   alt="$img_file" {$img_size['3']} /></a>\n
EOT;
			} elseif ($img_link['next'] == TRUE
				&& $img_link['wrap_up'] == TRUE
				&& $img_link['full'] == FALSE)
			{
				$wd    = strrchr($reqd_image['pwd_url'], '/');
				$wd_up = substr($reqd_image['pwd_url'], 0, -strlen($wd));
				$str .= <<<EOT
  <a href="$qdig_url?{$extra_param}Qwd=$wd_up&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
   title="{$dir_nav['up_level_txt']}"><img class="qdig-image" src="$url_base_path$img_url"
   alt="$img_file" {$img_size['3']} /></a>\n
EOT;
			} else {
				$str .= '  <img class="qdig-image" src="'.$url_base_path.$img_url
					.'" alt="'.$img_file.'" '.$img_size[3].' />'."\n";
			}
		} elseif(is_file($reqd_image['pwd'].'/'.$img_file) && $num_imgs > 1) {
			if ($img_link['next'] == TRUE && $img_link['full'] == FALSE) {
				$next_url = rawurlencode($imgs[$img_num + 1]);
				$str .= <<<EOT
  <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$next_url&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
   title="{$nav_lnk['next_msg']}"><img class="qdig-image" src="$url_base_path$img_url"
   alt="$img_file" {$img_size['3']} /></a>\n
EOT;
			} else {
				$str .= '  <img class="qdig-image" src="'.$url_base_path.$img_url
					.'" alt="'.$img_file.'" '.$img_size[3].' />'."\n";
			}
		}
	} elseif(is_file($reqd_image['pwd'].'/'.$img_file)) {
		$str .= '  <img class="qdig-image" src="'.$url_base_path.$img_url
			.'" alt="'.$img_file.'" '.$img_size[3].' />'."\n";
	}
	if ($caption['above'] != TRUE) {
		$str .= captionBlock($img_file, $img_size[0]);
	}
	$str .= " </div>\n";
	return $str;
} // End displayImage()

/**
* Produce an Image Caption table.
*
* The block is as wide as the image or else $caption['min_width'] pixels.
*/
function captionBlock($img_file, $img_width)
{
	global $pwd, $caption, $safe_captions, $caption_path, $touch_captions,
		$is_readable_disa, $file_exists_disa, $safe_mode, $diag_messages;
	$caption_file = $caption_path.'/'.$img_file.'.txt';
	if ($touch_captions == TRUE) {
		if ((($file_exists_disa == TRUE && ! is_file($caption_file))
				|| ! file_exists($caption_file))
			&& is_writable($caption_path))
		{
			touch($caption_file);
		}
	}
	if (is_file($caption_file)
		&& ($is_readable_disa == TRUE || is_readable($caption_file)))
	{
		$file_size = filesize($caption_file);
		if (! $file_size == 0 ) {
			// set caption width
			if ($safe_mode == TRUE) {
				$caption_width = '100%';
			} elseif ($img_width < $caption['min_width'] ) {
				$caption_width = $caption['min_width'];
			} else {
				$caption_width = ($img_width - ($caption['padding'] * 2));
			}
			if ($caption['left_just'] == TRUE) {
				$txt_align = 'left';
			} else {
				$txt_align = 'center';
			}
			// display caption
			$str = " <div title='Image Caption'"
				." class='qdig-caption' style='width:{$caption_width}px;"
				." padding:{$caption['padding']}; text-align:$txt_align;'>\n";
			$txt = '';
			if ($diag_messages == TRUE) {
				$fd = fopen ($caption_file, 'r');
			} else {
				$fd = @fopen ($caption_file, 'r');
			}
			if (!empty($fd)) {
				while (!feof ($fd)) {
					$buffer = fgets($fd, 4096);
					// disallow / allow HTML in captions
					if ($safe_captions == TRUE) {
						$txt .= htmlspecialchars($buffer, ENT_QUOTES);
					} else {
						$txt .= $buffer;
					}
				}
			fclose ($fd);
			} else {
				return;
			}
			if ($safe_captions == TRUE && $caption['nl2br'] == TRUE) {
				$str .= nl2br($txt);
			} else {
				$str .= $txt;
			}
			$str .= " </div>";
			return $str;
		}
	}
} // End captionBlock()

/**
* Produce a `Previous Image'or `Last Image` link for a Gallery Navigation Row.
*
* Link to the previous image, or optionally to the last if this is first image.
*/
function prevLink($img_num, $nav_wrap)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $nav_lnk, $styl_grayout;
	$num_imgs = count($imgs);
	if ($num_imgs > 1) {
		if ($img_num < 1 && $nav_wrap == FALSE) {
			$txt = $nav_lnk['prv_txt'];
			$str = <<<EOT
     <span $styl_grayout><b>$txt</b></span>\n
EOT;
			return $str;
		}
		if ($img_num < 1 || $img_num > $num_imgs) {
			$last_image_file = $imgs[$num_imgs - 1];
			$image = rawurlencode($last_image_file);
			$msg = $nav_lnk['last_msg'];
			$txt = $nav_lnk['last_txt1'];
		} else {
			$image = rawurlencode($imgs[$img_num - 1]);
			$msg = $nav_lnk['prv_msg'];
			$txt = $nav_lnk['prv_txt'];
		}
		$str = <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$image&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
      title="$msg">$txt</a>\n
EOT;
		return $str;
	}
} // End prevLink()

/**
* Produce a `Next Image' or `First Image' link for a Gallery Navigation Row.
*
* Link to the next image, or optionally to the first if this is the last image.
*/
function nextLink($nav_wrap)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image,
		$nav_lnk, $styl_grayout;
	$num_imgs = count($imgs);
	if ($reqd_image['num'] + 1 == $num_imgs && $nav_wrap == FALSE) {
		$str = <<<EOT
     <span $styl_grayout><b>{$nav_lnk['next_txt']}</b></span>\n
EOT;
		return $str;
	}
	if ($num_imgs > 1) {
		if ($reqd_image['num'] + 1 == $num_imgs) {
			$image = rawurlencode($imgs[0]);
			$msg = $nav_lnk['frst_msg'];
			$txt = $nav_lnk['frst_txt1'];
		} else {
			$image = rawurlencode($imgs[$reqd_image['num'] + 1]);
			$msg = $nav_lnk['next_msg'];
			$txt = $nav_lnk['next_txt'];
		}
		$str = <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$image&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
      title="$msg">$txt</a>\n
EOT;
		return $str;
	}
} // End nextLink()

/**
* Produce a `Last Image' (` >>| ') link for a Gallery Navigation Row.
*/
function lastLink($reqd_image)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $nav_lnk,
		$styl_grayout;
	$num_imgs = count($imgs);
	if ($num_imgs > 1) {
		if ($reqd_image['num'] + 1 == $num_imgs) {
			$str = <<<EOT
     <span $styl_grayout><b>{$nav_lnk['last_txt2']}</b></span>\n
EOT;
		} else {
			$last_image_file = $imgs[count($imgs) - 1];
			$file = rawurlencode($last_image_file);
			$str = <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$file&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
      title="{$nav_lnk['last_msg']}">{$nav_lnk['last_txt2']}</a>\n
EOT;
		}
		return $str;
	}
} // End lastLink()

/**
* Build a `First Image' (` |<< ') link for a Gallery Navigation Row.
*/
function firstLink($img_num)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $nav_lnk,
		$styl_grayout;
	if (count($imgs) > 1) {
		if ($img_num == 0) {
			$str = <<<EOT
     <span $styl_grayout><b>{$nav_lnk['frst_txt2']}</b></span>\n
EOT;
		} else {
			$image = rawurlencode($imgs[0]);
			$str = <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$image&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}$anchor"
      title="{$nav_lnk['frst_msg']}">{$nav_lnk['frst_txt2']}</a>\n
EOT;
		}
		return $str;
	}
} // End firstLink()

/**
* Build a Gallery Navigation Row table.
*
* Includes Prev/Next links and/or Home link, as set by user settings.
*/
function navRow($nav)
{
	global $imgs, $reqd_image, $cnvrt_path, $prev_img_msg, $next_img_msg,
		$img_sz_labels, $dir_nav, $is_readable_disa;
	if ($nav['enable'] == FALSE) { return ''; }
	$str = '';
	if (! is_dir($cnvrt_path)
		|| ! ($is_readable_disa == TRUE || is_readable($cnvrt_path)))
	{
		$nav['sizer'] = FALSE;
		$nav['full_link'] = FALSE;
	}
	if ($nav['sizer'] == TRUE) { // Sizer overrides Full Size link.
		$nav['full_link'] = FALSE;
	}
	$nav_full =  navFull($nav['sml_txt'], $img_sz_labels['nav']);
	if ($nav_full['show_full_link'] == FALSE) { $nav['full_link'] = FALSE; }
	if ($dir_nav['cntr_ena'] == TRUE && $dir_nav['enable'] == TRUE) {
		$nav['cntr'] = FALSE;
	}
	// Don't build a table if there's no content
	$num_imgs = count($imgs);
	if ($num_imgs > 1
			&& (($nav['prv_next'] == TRUE)
				|| ($nav['frst_last'] == TRUE)
				|| ($nav['cntr'] == TRUE))
		|| ($num_imgs > 0
			&& ($nav['sizer'] == TRUE) || ($nav['full_link'] == TRUE))
	)
	{
		if ($nav['sml_txt'] == TRUE) {
			$tag_bfr_lnk = '<small>';
			$tag_aft_lnk = '</small>';
		} else {
			$tag_bfr_lnk = '';
			$tag_aft_lnk = '';
		}
		if (isset($nav['pad_top'])) {
			$pad_top = $nav['pad_top'];
		} else{
			$pad_top = '0px';
		}
		$str = <<<EOT
\n <!-- gallery navigation -->
 <div align="center" style="padding-top:$pad_top;">
  <table summary="Gallery Navigation"
   width="{$nav['width']}" cellpadding="1" cellspacing="0">
   <tr>\n
EOT;
		if ($num_imgs > 1) {
			if ($nav['frst_last'] == TRUE) {
				$str .= "    <td width=\"5%\" style='white-space:nowrap; text-align:center;'>$tag_bfr_lnk\n"
					.firstLink($reqd_image['num'])
					."    $tag_aft_lnk</td>\n";
			}
			if ($nav['prv_next'] == TRUE) {
				$str .= "    <td width=\"14%\" style='white-space:nowrap; text-align:center;'>$tag_bfr_lnk\n"
					.prevLink($reqd_image['num'], $nav['wrap'])
					."    $tag_aft_lnk</td>\n";
			}
			if ($nav['cntr'] == TRUE
				&& ( $nav['sizer'] == TRUE || $nav['full_link'] == TRUE))
			{
				$str .= "    <td width=\"10%\" style='white-space:nowrap; text-align:center;'>\n";
			} elseif ($nav['cntr'] == TRUE ) {
				$str .= "    <td width=\"30%\" style='white-space:nowrap; text-align:center;'>\n";
			}
			if ($nav['cntr'] == TRUE) {
				if ($nav['cntr_bold'] == TRUE) {
					$bold_before = '<b>';
					$bold_after = '</b>';
				} else {
					$bold_before = '';
					$bold_after = '';
				}
				$str .= '     '.$tag_bfr_lnk.$bold_before.imageCounter($reqd_image['num']).$bold_after.$tag_aft_lnk."\n"
					."    </td>\n";
			}
		}
		if (($nav['cntr'] == TRUE && $num_imgs > 1)
			&& ($nav['sizer'] == TRUE || $nav['full_link'] == TRUE ))
		{
			$str .= "    <td width=\"20%\" style='white-space:nowrap; text-align:center;'> &nbsp;";
		} elseif ($nav['sizer'] == TRUE || $nav['full_link'] == TRUE ) {
			$str .= "    <td width=\"30%\" style='white-space:nowrap; text-align:center;'>\n";
		}
		if ($nav['sizer'] == TRUE) {
			$str .= navSize($nav['sml_txt'], $img_sz_labels['nav'])."    </td>\n";
		}
		if ($nav['sizer'] == FALSE
			&& $nav['full_link'] == TRUE)
		{
			$str .= $nav_full['str']."    </td>\n";
		}
		if ($num_imgs > 1) {
			if ($nav['prv_next'] == TRUE) {
				$str .= "    <td width=\"14%\" style='white-space:nowrap; text-align:center;'>$tag_bfr_lnk\n"
					.nextLink($nav['wrap'])."    $tag_aft_lnk</td>\n";
			}
			if ($nav['frst_last'] == TRUE) {
				$str .= "    <td width=\"5%\" style='white-space:nowrap; text-align:center;'>$tag_bfr_lnk\n"
					.lastLink($reqd_image['num'])."    $tag_aft_lnk</td>\n";
			}
		}
		$str .= "   </tr>\n  </table>\n </div>\n";
	}
	return $str;
} // End navRow()

/**
* Produce a Gallery Footer Row table.
*
* Includes Site Home Link, Copyright, and Quig Home link.
*/
function footerRow()
{
	global $imgs, $reqd_image, $site_lnk_url, $site_lnk_title, $copyright,
		$qdig_homelink, $footer, $rootdir;
	if (@$footer['omit'] == TRUE) { return ''; }
	if (empty($site_lnk_url)
		&& empty($site_lnk_title)
		&& $qdig_homelink['ena'] == FALSE)
	{
		return '';
	}
	if (empty($imgs) && $reqd_image['pwd'] == $rootdir) {
		$tag = '<small><small><br /></small></small>'."\n ";
	} else {
		$tag = '';
	}
	if (!empty($site_lnk_url) && !empty($site_lnk_title)) {
		$site_lnk_on = TRUE;
		$copyright_align_str = ' align="center"';
	} else {
		$site_lnk_on = FALSE;
		$copyright_align_str = ' align="left"';
	}
	if (count($imgs) < 1) {
		unset($copyright['txt']);
	}
	if (!empty($copyright['txt'])) {
		$copyright_on = TRUE;
	} else {
		$copyright_on = FALSE;
	}
	if ($qdig_homelink['ena'] == FALSE && $site_lnk_on == TRUE) {
		$copyright_align_str = ' align="right"';
	} elseif ($qdig_homelink['ena'] == FALSE) {
		$copyright_align_str = ' align="center"';
	}
	if ($site_lnk_on == FALSE
		&& $copyright_on == FALSE
		&& $qdig_homelink['ena'] == TRUE)
	{
		$qdighome_alone = TRUE;
	} else {
		$qdighome_alone = FALSE;
	}
	// don't produce an empty table
	if ($site_lnk_on == TRUE
		|| $copyright_on == TRUE
		|| $qdig_homelink['ena'] == TRUE)
	{
		$str = <<<EOT
\n <!-- table footer row of image dispaly area -->
 $tag<div align="center" style="padding-top:{$footer['pad_top']};">
  <table summary="Gallery Footer"
   width="100%" cellpadding="1" cellspacing="0">
   <tr>\n
EOT;
		if ($site_lnk_on == TRUE) {
			$str .= "    <td width=\"20%\" nowrap=\"nowrap\">\n"
				.siteHomeLink($site_lnk_url, $site_lnk_title)."    </td>\n";
		}
		if ($copyright_on == TRUE) {
			$str .= '    <td width=" 60%" nowrap="nowrap" '.$copyright_align_str.">\n"
				.showCopyright($copyright)."    </td>\n";
		}
		if ($qdig_homelink['ena'] == TRUE) {
			$str .= "    <td width=\" 20%\" nowrap=\"nowrap\">\n"
				.qdigHomelink($qdighome_alone)."    </td>\n";
		}
		$str .= "   </tr>\n  </table>\n </div>\n";
		return $str;
	}
} // End footerRow()

/**
* Display either the Text or Thumbnail Image Links.
*/
function displayImageLinks($thmb_row, $nmrl_row)
{
	global $thmbs_ena, $convert_readable;
	if ($thmbs_ena == TRUE && $convert_readable == TRUE) {
		$str = imageThumbsLinks($thmb_row);
	} else {
		$str = imageTextLinks($nmrl_row);
	}
	return $str;
} // End displayImageLinks()

/**
* Produce a `Default View' chooser for a Gallery Control Bar.
*/
function controlView($ctrl_links_mesg)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $thmb_enable,
	    $styl_grayout, $ctrl_bar, $convert_readable, $namelinks_disa;
	if (count($imgs) < 2) { return; }
	$reqd_file = rawurlencode($reqd_image['file']);
	$str = "   <td align=\"center\">\n";
	if ($ctrl_bar['small'] == TRUE) {
		$str .= "    <div style=\"padding-top:2px;\"><small>\n";
	} else {
		$str .= "    <div style=\"padding-top:2px;\">\n";
	}
	$str .= "     {$ctrl_links_mesg['links_style']} <b>[</b>\n";
	if ( $reqd_image['view'] != 'thumbs'
		&& $convert_readable == TRUE
		&& $thmb_enable == TRUE)
	{
		$str .= <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv=thumbs&amp;Qis={$reqd_image['size']}$anchor"
	  title="{$ctrl_links_mesg['thumbs_msg']}">{$ctrl_links_mesg['thumbs_txt']}</a> |\n
EOT;
	} elseif ($convert_readable == TRUE && $thmb_enable == TRUE) {
		$txt = $ctrl_links_mesg['thumbs_txt'];
		$str .= <<<EOT
     <span $styl_grayout><b>$txt</b></span> |\n
EOT;
	}
	if ( $reqd_image['view'] != 'name' && $namelinks_disa == FALSE ) {
		$str .= <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv=name&amp;Qis={$reqd_image['size']}$anchor"
	  title="{$ctrl_links_mesg['names_msg']}">{$ctrl_links_mesg['names_txt']}</a> |\n
EOT;
	} elseif ($namelinks_disa == FALSE) {
		$txt = $ctrl_links_mesg['names_txt'];
		$str .= <<<EOT
     <span $styl_grayout><b>$txt</b></span> |\n
EOT;
	}
	if ( $reqd_image['view'] != 'num') {
		$str .= <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv=num&amp;Qis={$reqd_image['size']}$anchor"
	  title="{$ctrl_links_mesg['nums_msg']}">{$ctrl_links_mesg['nums_txt']}</a> |\n
EOT;
	} else {
		$txt = $ctrl_links_mesg['nums_txt'];
		$str .= <<<EOT
     <span $styl_grayout><b>$txt</b></span> |\n
EOT;
	}
	if ( $reqd_image['view'] != 'none') {
		$str .= <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv=none&amp;Qis={$reqd_image['size']}$anchor"
	  title="{$ctrl_links_mesg['none_msg']}">{$ctrl_links_mesg['none_txt']}</a>\n
EOT;
	} else {
		$txt = $ctrl_links_mesg['none_txt'];
		$str .= <<<EOT
     <span $styl_grayout><b>$txt</b></span>\n
EOT;
	}
	$str .= "     <b>]</b>\n";
	if ($ctrl_bar['small'] == TRUE) {
		$str .= "    </small></div>\n";
	} else {
		$str .= "    </div>\n";
	}
	$str .= "   </td>\n";
	return $str;
} // End controlView()

/**
* Produce a `Default Size' chooser for a Gallery Control Bar.
*/
function controlSize($labels)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $cnvrt_path,
		$styl_grayout, $ctrl_bar, $disp_size, $cnvrt_size, $is_readable_disa;
	if (count($imgs) < 2) { return; }
	if (! is_dir($cnvrt_path)
		|| ! ($is_readable_disa == TRUE || is_readable($cnvrt_path)))
	{
		return;
	}
	$reqd_file = rawurlencode($reqd_image['file']);
	if ($ctrl_bar['vw_ctrl'] == FALSE) {
		$str = "   <td align=\"center\" nowrap=\"nowrap\">\n";
	} else {
		$str = "   <td nowrap=\"nowrap\">\n";
	}
	if ($ctrl_bar['small'] == TRUE) {
		$str .= "    <div style=\"padding-top:2px;\"><small>\n";
	} else {
		$str .= "    <div style=\"padding-top:2px;\">\n";
	}
	$x = 0;
	foreach($cnvrt_size as $size_info) {
		if ($reqd_image['size'] != $size_info['label']
			&& $disp_size[$x] == TRUE)
		{
			$title = $labels['str1'].$cnvrt_size[$x]['txt'].$labels['str2'];
			$size[] = <<<EOT
     <a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv={$reqd_image['view']}&amp;Qis={$size_info['label']}$anchor"
	  title="$title">{$size_info['label']}</a>
EOT;
		} elseif ($disp_size[$x] == TRUE) {
			$size[] = <<<EOT
     <span $styl_grayout><b>{$size_info['label']}</b></span>
EOT;
		}
		$x++;
	}
	if (!empty($size)) {
		$str .= "     &nbsp;{$labels['default_size']} <b>[</b>\n";
		$num_sizes = count($size);
		$i = 0;
		foreach($size as $size_str) {
			$str .= $size_str;
			if ($i < ($num_sizes - 1)) {
				$str .= " |\n";
			}
			$i++;
		}
		$str .= "\n     <b>]</b>\n";
	} else {
		$str .= '&nbsp;';
	}
	if ($ctrl_bar['small'] == TRUE) {
		$str .= "    </small></div>\n";
	} else {
		$str .= "    </div>\n";
	}
	$str .= "   </td>\n";
	return $str;
} // End controlSize()

/**
* Produce a Size Chooser for a Gallery Navigation Row.
*/
function navSize($small_b, $labels)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $cnvrt_path,
		$reqd_img_size_tmp, $styl_grayout, $disp_size, $cnvrt_size,
		$valid_sizes, $is_readable_disa;
	if (! is_dir($cnvrt_path)
		|| ! ($is_readable_disa == TRUE || is_readable($cnvrt_path)))
	{
		return;
	}
	if ($small_b == TRUE) {
		$small_on = "<small>";
		$small_off = "</small>";
	} else {
		$small_on = '';
		$small_off = '';
	}
	if (!empty($reqd_img_size_tmp)) {
		$size_displayed = $reqd_img_size_tmp;
	} else {
		$size_displayed = $reqd_image['size'];
	}
	$n = count($disp_size) - 1;
	$full_size = $cnvrt_size[$n]['label'];
	$str = $small_on.'<b>[</b>'.$small_off."\n";
	$num_sizes = count($disp_size);
	$num_valid = count($valid_sizes);
	foreach($disp_size as $i => $enabled) {
		$size = $cnvrt_size[$i]['label'];
		if (!in_array($size, $valid_sizes)) { continue; } // skip
		if ($size == $reqd_image['size']) {
			$qtmp_txt = '';
		} else {
			$qtmp_txt = $size;
		}
		$title = $labels['str1'].$cnvrt_size[$i]['txt'].$labels['str2'];
		$file  = $cnvrt_path.'/'.$cnvrt_size[$i]['prefix'].$reqd_image['file'];
		$file2 = $cnvrt_path.'/'.@$cnvrt_size[$z]['prefix'].$reqd_image['file'];
		$z=$i;
		if ($size == $reqd_image['size'] && is_file($file2)) { $foo = TRUE; }
		if ($size == $size_displayed && is_file($file)) { $bar = TRUE; }
		if (is_file($file) && !is_file($file2)) { $bim = TRUE; }
		$reqd_file = rawurlencode($reqd_image['file']);
		if ((is_file($file) || is_file($file2) || $size == $full_size && @$bar == TRUE && $bim == FALSE)
			&& $size != $size_displayed)
		{
			$size_txt[] = <<<EOT
     $small_on<a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}&amp;Qtmp={$qtmp_txt}$anchor"
      title="$title">{$size}</a>$small_off
EOT;
		} else {
			$size_txt[] = <<<EOT
     $small_on<span $styl_grayout><b>{$size}</b></span>$small_off
EOT;
		}
	}
	$num_to_display = count($size_txt);
	foreach ($size_txt as $txt) {
		$str .= $txt;
		$num_to_display--;
		if ($num_to_display > 0) {
			$str .= " |\n";
		} else {
			$str .= "\n     ".$small_on.'<b>]</b>'.$small_off."\n";
		}
	}
	return $str;
} // End navSize()

/**
* Produce an `Full Size' chooser for a Gallery Navigation Row.
*/
function navFull($small_b, $labels)
{
	global $imgs, $qdig_url, $extra_param, $anchor, $reqd_image, $cnvrt_path,
		$reqd_img_size_tmp, $styl_grayout, $disp_size, $cnvrt_size,
		$is_readable_disa;
	if (! isset($reqd_image['file'])
		|| ! is_dir($cnvrt_path)
		|| ! ($is_readable_disa == TRUE || is_readable($cnvrt_path)))
	{
		return; // no use, so never mind
	}
	if ($small_b == TRUE) {
		$small_on = '<small>';
		$small_off = '</small>';
	} else {
		$small_on = '';
		$small_off = '';
	}
	$size_info = $cnvrt_size['4'];
	$full_label = $size_info['label'];
	if ($reqd_img_size_tmp != $full_label
		&& $reqd_image['size'] != $full_label)
	{
		$show_full_link = TRUE;
	} else {
		$show_full_link = FALSE;
	}
	$title = $labels['str1'].$size_info['txt'].$labels['str2'];
	foreach($cnvrt_size as $size_info) {
		if ($reqd_image['size'] == $size_info['label'] ) {
			$file = $cnvrt_path.'/'.$size_info['prefix'].$reqd_image['file'];
			if (! is_file($file)) {
				$show_full_link = FALSE;
			}
		}
	}
	$file = $reqd_image['pwd'].'/'.$reqd_image['file'];
	if (is_file($file)
		&& ($is_readable_disa == TRUE || is_readable($file))
		&& $show_full_link == TRUE)
	{
		$reqd_file = rawurlencode($reqd_image['file']);
		$str = <<<EOT
     $small_on<a href="$qdig_url?{$extra_param}Qwd={$reqd_image['pwd_url']}&amp;Qif=$reqd_file&amp;Qiv={$reqd_image['view']}&amp;Qis={$reqd_image['size']}&amp;Qtmp=$full_label$anchor"
      title="$title">{$size_info['txt']}</a>$small_off\n
EOT;
	} else {
		$str = "&nbsp;\n";
	}
	return array('str' => $str, 'show_full_link' => $show_full_link);
} // End navFull()

/**
* Produce a Gallery Sidebar if appropriate.
*/
function sideBar($namelinks_ena, $thmbs_ena, $bg_clr, $margin_top, $height)
{
	global $imgs;
	if (count($imgs) < 2) { return; } // no sidebar if no image links
	if (empty($height) || $height == 'auto') {
		$height = 'auto';
		$scroll = '';
		$valign = 'top';
	} else {
		$scroll = 'overflow:auto;';
		$valign = 'top';
	}
	if ($namelinks_ena == TRUE) {
		$arr['sb_on'] = TRUE;
		$str = <<<EOT
\n <!-- Begin Qdig sidebar -->
 <td valign="$valign">
 <div style="margin-top:$margin_top; background-color:$bg_clr; height:$height;
  width:auto; $scroll white-sapce:nowrap; padding-top:2px; padding-bottom:2px;">\n
EOT;
		$str .= displayImageLinks('', '');
		$str .= " </div>\n </td>\n <!-- End Qdig sidebar -->\n";
		$arr['str'] = $str;
		return $arr;
	}
} // End sideBar()

/**
* Produce a `Site Home' link for the Gallery Footer.
*/
function siteHomeLink($site_lnk_url, $site_lnk_title)
{
	if (!empty($site_lnk_url) && !empty ($site_lnk_title)) {
		global $site_link_fnt_size;
		$str = <<<EOT
     <div title="Site Link" style="font-size:$site_link_fnt_size;">
      <a href="$site_lnk_url" title="$site_lnk_title">$site_lnk_title</a>&nbsp;
     </div>\n
EOT;
		return $str;
	}
} // End siteHomeLink()

/**
* Produce the copyright text for the Gallery Footer.
*/
function showCopyright($copyright)
{
	if (!empty($copyright['txt'])) {
		$str = <<<EOT
     <div title="Copyright Text" style="color:{$copyright['color']};
      font-size: {$copyright['fnt_size']}; font-weight:normal;">
      {$copyright['txt']}
     </div>\n
EOT;
		return $str;
	}
} // End showCopyright()

/**
* Produce an Admin link to the current image's admin.php caption-edit page.
*/
function adminLink($admin)
{
	global $reqd_image, $php_self, $caption_path;
	$str = '';
	if (isset($reqd_image['file'])
		&& (!empty($admin['script_file']) || !empty($admin['full_url']))
		&& (!empty($admin['full_url']) || @is_file($admin['script_file']))
		&& !empty($php_self))
	{
		if (empty($admin['full_url'])) {
			$admin_script_dir = substr(urlPath(dirname($php_self)), 1);
			$admin['full_url'] = $admin_script_dir.'/'.$admin['script_file'];
		}
		$admin_caption_dir = substr(urlPath($caption_path), 1); // No leading '.'  
		$admin_img = rawurlencode($reqd_image['file']);
		$str = <<<EOT
\n      {$admin['before_link']}<a href="{$admin['full_url']}?op=details&amp;D=$admin_caption_dir&amp;F=$admin_img.txt&amp;R=Qdig"
       title="{$admin['link_title']}" style="color:{$admin['color']};
       font-weight:normal;">{$admin['link_text']}</a>{$admin['after_link']}
EOT;
	}
	return $str;
} // End adminLink()

/**
* Produce a link to the Qdig script home page for the Gallery Footer.
*/
function qdigHomelink($qdighome_alone)
{
	global $qdig_homelink;
	if ($qdig_homelink['ena'] == TRUE) {
		global $site_lnk_url, $reqd_image;
		if ($qdighome_alone == TRUE
			|| (empty($site_lnk_url) && empty($reqd_image['file'])))
		{
			$txt_align = "center";
		} else {
			$txt_align = "right";
		}
		$str = <<<EOT
     <div title="Qdig Home Link"
      style="color:{$qdig_homelink['color']}; font-size:{$qdig_homelink['fnt_size']}; text-align:$txt_align;">
      Gallery by
      <a href="http://qdig.sourceforge.net/"
       title="Qdig Gallery Script Home Page" style="color:{$qdig_homelink['color']};
       font-weight:normal;">Qdig</a>\n     </div>\n
EOT;
		return $str;
	}
} // End qdigHomelink()

/*
+---------+
|  Logic  |
+---------+
*/

/**
* Image is a popup target (experimental).
*/
if (@$get_vars['Qtmp'] == 'popup') {
	$header['nav_links'] = FALSE;
	//$footer['omit']    = TRUE;
}

/**
* Set base directory if using alternate base paths
*/
if (!empty($fs_base_path)) {
	$base_dir = realpath($fs_base_path);
	if (!empty($base_dir)) {
		$orig_wd = getcwd();
		chdir($base_dir);
	}
}

/**
* Start creating diagnostic messages
*/
if ($diag_messages == TRUE) {
	$diag_mesgs = "<p><small>\n<b>{$lang['diag_messages']}:</b><br />\n";
	error_reporting(E_ALL);
} else {
	$diag_mesgs = '';
}

/**
* $chroot_dir sanity check
*/
if (!empty($chroot_dir)) {
	$rootdir = cleanPath($chroot_dir);
} else {
	$rootdir = '.';
	$chroot_dir = '';
}
if (!@is_dir($rootdir)) {
	exit("<html>\n <body>
	Chroot directory <b>$chroot_dir</b> doesn't exist.<br />
	Check gallery script configuration.\n </body>\n</html>");
}

/**
* Establish working directory.
*/
if (!empty($get_vars['Qwd'])) {
	if (strlen($get_vars['Qwd']) > $pathname_maxlen
		|| (strlen($get_vars['Qwd']) > 1 && $get_vars['Qwd'][0] == '.' && $get_vars['Qwd'][1] == '.')) {
		securityExit('Pathname (Qwd=) is too long or starts with "..".');
	}
	$pwd_tmp = cleanPath($get_vars['Qwd']);
} else {
	$pwd_tmp = '.';
}
if (strlen($pwd_tmp) <= strlen($rootdir)) {
	$pwd = $rootdir;
} elseif (strpos($pwd_tmp, $rootdir) === 0) {
	$pwd = rawurldecode($pwd_tmp);
}
if (! is_dir($pwd)
	|| ! ($is_readable_disa == TRUE || is_readable($pwd)))
{
	$pwd = $rootdir;
}
if ($extra_paranoia == TRUE
	&& (strpos(stripslashes(rawurldecode($pwd)), '..')
		|| empty ($pwd)
		|| $pwd[0] != '.'
		|| (strlen($pwd) > 1 && $pwd[1] == '.')))
{
	securityExit('Updir ("..") is not allowed in a pathname (Qwd=).');
}
// Encode $pwd for use in URLs.
$pwd_url = urlPath($pwd);
// Sanitize URL base path.
if (!empty($url_base_path)) {
	$url_base_path = strstr(urlPath($url_base_path), '/').'/';
}

/**
* Establish requested size.
*/
foreach($disp_size as $i => $ena) {
	$size_labels[] = $cnvrt_size[$i]['label'];
	if ($ena == TRUE) {
		$valid_sizes[] = $cnvrt_size[$i]['label'];
	}
}
if (isset($get_vars['Qis'])
	&& strlen($get_vars['Qis']) < 9
	&& in_array($get_vars['Qis'], $valid_sizes))
{
	$reqd_img_size = $get_vars['Qis'];
} elseif (in_array($cnvrt_size[$default_img_size]['label'], $valid_sizes)) {
	$reqd_img_size = $cnvrt_size[$default_img_size]['label'];
} else {
	$reqd_img_size = $valid_sizes['0'];
}

/**
* Establish temp size, if any.
*/
if (isset($get_vars['Qtmp']) && strlen($get_vars['Qtmp']) < 9) {
	$reqd_img_size_tmp = $get_vars['Qtmp'];
} else {
	$reqd_img_size_tmp = FALSE;
}
if (in_array($reqd_img_size_tmp, $size_labels)
	&& !in_array($reqd_img_size_tmp, $valid_sizes))
{
	$reqd_img_size_tmp = FALSE;
}

/**
* Get non-Qdig GET parmameters, if any
*/
if ($keep_params == TRUE) {
	$extra_param .= keepParams();
}

/**
* Establish Image Conversion and Captions Paths' roots
*/
if (!empty($qdig_files)) {
	$cnvrtd_files_root = cleanPath("$qdig_files/$convrtd_subdir").'/';
	$captions_root = cleanPath("$qdig_files/$caption_subdir").'/';
	$qdig_files   = substr(cleanPath($qdig_files), 2);
	$chroot_dir   = substr(cleanPath($chroot_dir), 2);
	$qdf_parts    = explode('/', $qdig_files);
	$chroot_parts = explode('/', $chroot_dir);
	foreach($qdf_parts as $i => $qdf_part) {
		if (@$chroot_parts[$i] == $qdf_part) { continue; }
		$qdig_files_topdir = $qdf_part;
	}
} else {
	$cnvrtd_files_root = '';
	$captions_root = '';
	$qdig_files = '';
}

/**
* Get the array of subdirectory names.
*/
$subdirs = getDirNames($pwd);
// Don't enable directory navigation if it's not usable
if  ($dir_nav['enable'] == FALSE
	|| ($pwd == '.' && ! isset($subdirs)))
{
	$dir_nav['prefs_ena'] == FALSE;
}

/**
* Build style strings for color settings.
*/
// Gallery table background color
if (!empty($gallery_table_bg_clr)) {
	$qdig_bg_clr_attr = 'bgcolor="'.$gallery_table_bg_clr.'" ';
} else {
	$qdig_bg_clr_attr = '';
}
// Image table background color
if (!empty($image_table_bg_clr)) {
	$img_tbl_bg_clr_attr = ' bgcolor="'.$image_table_bg_clr.'"';
} else {
	$img_tbl_bg_clr_attr = '';
}
// Grayed-out text color
if (!empty($grayout_color)) {
	$styl_grayout = 'class="qdig-grayout" style="color:'.$grayout_color.';"';
} else {
	$styl_grayout = 'class="qdig-grayout"';
}

/**
* Check for Image Magick or GD
*/
// Turn them off if they're not likely to work.
if ($convert_GD == TRUE && ! gdVersion()) {
	$convert_GD = FALSE;
}
if ($convert_magick == TRUE
	&& ($safe_mode == TRUE || ! @is_file($convert_cmd)))
{
	$convert_magick = FALSE;
}
// Prefer GD on Win32, otherwise prefer IM.
if ($convert_magick == TRUE && $convert_GD == TRUE) {
	if ($platform == 'Win32') {
		$convert_magick = FALSE;
	} else {
		$convert_GD = FALSE;
	}
}

/**
* Get the array of image filenames.
*/
// Exclude background images.
$excl_imgs[] = end($logo_arrray = explode('/', $header['css_logo_url']));
$excl_imgs[] = end($bg_img_array = explode('/', $header['css_bg_img_url']));
$imgs = getImageFilenames($pwd);

/**
* Establish Image Conversion and Caption Paths
*/
$cnvrt_path = cleanPath($cnvrtd_files_root.$pwd.'/'.$cnvrtd_dir);
if (( $convert_magick == TRUE ||  $convert_GD == TRUE)
	&& ! is_dir($cnvrt_path))
{
	mkRecursiveDir($cnvrt_path);
}
$caption_path = cleanPath($captions_root.$pwd);
if (! is_dir($caption_path) && $touch_captions == TRUE) {
	mkRecursiveDir($caption_path);
}

/*
* Security Check
*/
if ($check_security == TRUE
	&& ! ($platform == 'Win32')
	&& !empty($qdig_files)
	&& @is_writable($qdig_files)
	&& ! @$get_vars['Makethumb'] == 'Y'
	&& umask() > 0)
{
	if (@$base_dir) { $base_dir = $base_dir.'/'; }
	$path = @$base_dir.cleanPath($qdig_files).'/';
	$warning_fn = $path.'Security_Check_File--Safe_To_Delete';
	$dperms = decoct(fileperms($path)) % 10000;
	$wperms = substr($dperms, - 1); // world perms
	if (@is_dir($cnvrt_path)
		&& ($touch_captions == FALSE || @is_dir($caption_path)))
	{
		if (! is_file($warning_fn)) {
			touch($warning_fn);
		} elseif ($wperms == 7 || $wperms == 6 || $wperms == 3 || $wperms == 2)
		{ // world-writable
			$setting = '<span style="color:blue;">$check_security</span>';
			$install_txt = '<a href="http://cvs.sourceforge.net/viewcvs.py/qdig/qdig/INSTALL.txt?rev=1.20"
				title ="INSTALL.txt">INSTALL.txt</a>';
			$exit_mesg =<<<EOT
<html><head>
<title>Security Warning</title>
</head><body>
 <h3 style="color:black; background-color:pink;">Check security.</h3>
 The <b>$path</b> directory appears to be world-writable.<br /><br />
 See $install_txt for information about setting permissions on the<br />
 directory to something reasonable (like 0755 / drwxr-xr-x).  There is also<br />
 a $setting configuration setting you can use to disable the security<br />
 check that produces this message.
EOT;
			if ($dperms == '777') {
				$umask = umask();
				$exit_mesg .=<<<EOT
\n<br /><br />If you want to use "777" (rather than "2777") permissions<br />
you may also want to allow the script to create world-writable<br />
files using the following File Creation Mask setting:<br />
<b>umask(000);</b><br />
Doing so will be more convenient but "less secure" than leaving the<br />
umask setting as it is now. It will also bypass the security check<br />
that produces this message.
EOT;
			$exit_mesg .='</body></html>';
			}
			exit($exit_mesg);
		}
	}
}

/**
* Are converted images writable or readable?
*/
if (is_dir($cnvrt_path) && is_writable($cnvrt_path))
{
	$path_writable = TRUE;
} else {
	$path_writable = FALSE;
}
if (($convert_magick == TRUE || $convert_GD == TRUE)
	&& $path_writable == TRUE)
{
	$convert_writable = TRUE;
} else {
	$convert_writable = FALSE;
	$convert_magick = FALSE; // Turn off if path isn't writable
	$convert_GD = FALSE;
}
if (is_dir($cnvrt_path)
	&& ($is_readable_disa == TRUE || is_readable($cnvrt_path)))
{
	$convert_readable = TRUE;
} else {
	$convert_readable = FALSE;
}

/*
* Get the requested view
*/
if (isset($get_vars['Qiv'])
	&& strlen($get_vars['Qiv']) < 10
	&& in_array($get_vars['Qiv'], array('thumbs', 'name', 'num', 'none')))
{
	$reqd_view = $get_vars['Qiv'];
}
if ($thmb_enable == FALSE) {
	if ($reqd_view == 'thumbs') {
		$reqd_view = $txtlinks_default;
	}
	$thmb_default = FALSE;
}
if (! isset($reqd_view)
	&& $convert_readable == TRUE
	&& $thmb_default == TRUE)
{
	$reqd_view = 'thumbs';
}
if ($thmb_onfly == TRUE
	&& isset($get_vars['Makethumb'])
	&& $convert_writable == TRUE)
{
	$reqd_view = 'thumbs';
}
if (! isset($reqd_view)) {
	$reqd_view = $txtlinks_default;
}
if ($reqd_view == 'text' || $reqd_view == 'name' || $reqd_view == 'num') {
	$thmbs_ena = FALSE;
}
if ($reqd_view == 'thumbs'
	&& $convert_readable == TRUE
	&& $thmb_enable == TRUE)
{
	$thmbs_ena = TRUE;
}
if ($reqd_view == 'name') {
	$namelinks_ena = TRUE;
} else {
	$namelinks_ena = FALSE;
}
if ($reqd_view == 'none') {
	$thmbs_ena = FALSE;
	$no_lnks_view = TRUE;
} else {
	$no_lnks_view = FALSE;
}
if (!isset($thmbs_ena)) {
	$thmbs_ena = FALSE;
}

// Store the requested image file (name / num), directory, and view, and size.
$reqd_image = getReqdImage();
$reqd_image['pwd'] = $pwd;
$reqd_image['pwd_url'] = $pwd_url;
$reqd_image['view'] = $reqd_view;
$reqd_image['size'] = $reqd_img_size;

// Produce the caption-editing link.
@$copyright['txt'] .= adminLink($admin);

// Turn off $thmb_onfly if this is an embedded gallery...
if ($header['force_ena'] == TRUE && $thmb_onfly == TRUE) {
	$thmb_onfly = TRUE;
} elseif ($header['force_disa'] == TRUE || $is_included == TRUE) {
	$thmb_onfly = FALSE;
}
// ...if the image is a singleton...
if ($cnvrt_thmb['single'] == TRUE && count($imgs) < 2) {
	$thmb_onfly = FALSE;
}
// ...or if the server is in Safe Mode.
if ($safe_mode == TRUE) {
	$thmb_onfly = FALSE;
}

/**
* Create a single thumb if it's been requested.
*/
if (isset($get_vars['Makethumb'])) {
	# Reset the image 'list' to just this one
	$imgs = array($reqd_image['file']);
	$cnvrt_thmb['single'] = TRUE;
	$thumbs_msg = createThumbs($cnvrt_thmb);

	$name = $cnvrt_path.'/'.$cnvrt_thmb['prefix'].$imgs[0];

	// Get the file attriubutes for the header
	$img_size = GetImageSize($name);
	$content_types[1] = 'image/gif';
	$content_types[2] = 'image/jpg';
	$content_types[3] = 'image/png';
	$content_types[6] = 'image/bmp';
	$content_type = $content_types[$img_size[2]];
	$content_length = filesize($name);

	// Send the right headers
	header("Pragma: no-cache");
	header("Content-Length: $content_length");
	header("Content-Type: $content_type");

	// dump the picture and stop the script
	$fp = fopen($name, 'rb');
	fpassthru($fp);
	exit;
}

/**
* Determine if we need a Control Bar.
*/
if ($ctrl_bar['vw_ctrl'] == FALSE && $ctrl_bar['sz_ctrl'] == FALSE) {
	$ctrl_bar['enable'] = FALSE;
}
if ($ctrl_bar['enable'] == FALSE) {
	$dir_nav['prefs_ena'] = FALSE;
}
if ($dir_nav['enable'] == FALSE && $ctrl_bar['enable'] == TRUE) {
	$ctrl_bar['ena'] = TRUE;
	$dir_nav['prefs_ena'] = FALSE;
}

/**
* Determine if we need a display area.
*/
if (!empty($site_lnk_url)
	|| $qdig_homelink['ena'] == FALSE
	|| !empty($imgs))
{
	$display_area = TRUE;
} else {
	$display_area = FALSE;
}

/**
* Create thumbnail image(s) if necessary.
*/
if ($thmb_onfly == FALSE) {
	$thumbs_msg = createThumbs($cnvrt_thmb);
}

/**
* Create alternate image sizes.
*/
$resize_msg = '';
if ($disp_size['0'] == TRUE
	&& (($cnvrt_alt['indiv'] == FALSE
		|| $upr_nav['sizer'] == TRUE
		|| $lwr_nav['sizer'] == TRUE)
	|| $reqd_img_size == $cnvrt_size['0']['label']))
{
	$resize_msg .= resizeImage($cnvrt_size['0']);
}
if ($disp_size['1'] == TRUE
	&& (($cnvrt_alt['indiv'] == FALSE
		|| $upr_nav['sizer'] == TRUE
		|| $lwr_nav['sizer'] == TRUE)
	|| $reqd_img_size == $cnvrt_size['1']['label']))
{
	$resize_msg .= resizeImage($cnvrt_size['1']);
}
if ($disp_size['2'] == TRUE
	&& (($cnvrt_alt['indiv'] == FALSE
		|| $upr_nav['sizer'] == TRUE 
		|| $lwr_nav['sizer'] == TRUE)
	|| $reqd_img_size == $cnvrt_size['2']['label']))
{
	$resize_msg .= resizeImage($cnvrt_size['2']);
}
if ($disp_size['3'] == TRUE
	&& (($cnvrt_alt['indiv'] == FALSE
		|| $upr_nav['sizer'] == TRUE
		|| $lwr_nav['sizer'] == TRUE)
	|| $reqd_img_size == $cnvrt_size['3']['label']))
{
	$resize_msg .= resizeImage($cnvrt_size['3']);
}

/**
* Finish creating diagnostic messages.
*/
if ($diag_messages == TRUE) {
	$server_sw = $server_vars['SERVER_SOFTWARE'];
	if (preg_match('/Fedora/', $server_sw)) {
		$server_sw .= "<br />\nFedora Core 3 or later may require 
<a href='http://qdig.sourceforge.net/Support/FedoraSELinux'>adjusting the SELinux policy</a> for Apache.";
	}
	if (@file_exists($qdig_files) && @is_dir($qdig_files)) {
		$qdig_files_exists = TRUE;
		$qdig_files_perms = decoct(fileperms($qdig_files)) % 10000;
	} else {
		$qdig_files_exists = FALSE;
	}
	if (@file_exists($cnvrtd_files_root) && @is_dir($cnvrtd_files_root)) {
		$cnvrtd_root_exists = TRUE;
		$cnvrtd_root_perms = decoct(fileperms($cnvrtd_files_root)) % 10000;
	} else {
		$cnvrtd_root_exists = FALSE;
	}
	if (@file_exists($captions_root) && @is_dir($captions_root)) {
		$captions_root_exists = TRUE;
		$captions_root_perms = decoct(fileperms($captions_root)) % 10000;
	} else {
		$captions_root_exists = FALSE;
	}
	if (@file_exists($convert_cmd)) {
		$convert_cmd_exists = TRUE;
		$convert_cmd_perms = decoct(fileperms($convert_cmd)) % 10000;
	} else {
		$convert_cmd_exists = FALSE;
	}
	if (!empty($fs_base_path)) {
		$fs_path_msg = '$fs_base_path is '.$fs_base_path."<br />\n"
		.@trueFalse($base_dir, '$base_dir is '.$base_dir,
			'$base_dir (realpath($fs_base_path)) is empty')."<br />\n";
	}
	$diag_mesgs .= "Qdig version is $qdig_version,&nbsp; "
		.'PHP Version is '.phpversion()
		.trueFalse(ini_get('safe_mode'), ' with <a href="http://qdig.sourceforge.net/Support/PHPSafeMode"'
		.' title="Qdig And PHP Safe Mode">Safe Mode enabled</a>', '').' '
		.'on '.$platform."<br />\n"
		.'Server Software is '.$server_sw."<br />\n"
		.'<span style="white-space:nowrap;">'
		.'$qdig_url is <a href="'.$qdig_url.'" title="Gallery URL (w/o query string)">'.$qdig_url.'</a>'."<br />\n"
		.'$php_self is '.$php_self."<br />\n"
		.'$script_name is '.$script_name."<br />\n"
		.'Query string is '.@trueFalse(strlen($server_vars['QUERY_STRING']) > 64, '<br />&nbsp; ', '')
		.@trueFalse($server_vars['QUERY_STRING'],$server_vars['QUERY_STRING'], ' (empty)')."<br />\n"
		.'$pwd is '.$pwd.',&nbsp; '
		.'$chroot_dir is '.@trueFalse($chroot_dir, $chroot_dir, '(empty)')."<br />\n"
		.'$is_included is '.@trueFalse($is_included).',&nbsp; '
		.'$header[\'force_ena\']/[\'force_disa\'] are '.@trueFalse($header['force_ena']).'/'
		.@trueFalse($header['force_disna'])."<br />\n"
		.@$fs_path_msg.@trueFalse($url_base_path, '$url_base_path is '.$url_base_path."<br />\n", '')
		.'$qdig_files is '.@trueFalse($qdig_files, $qdig_files, '(empty)').'&nbsp; '
		.@trueFalse($qdig_files_exists, "(exists, with $qdig_files_perms perms)", "(doesn't exist)").',&nbsp; '
		.'$cnvrtd_dir is '.@trueFalse($cnvrtd_dir, $cnvrtd_dir, '(empty)')."<br />\n"
		.'$cnvrtd_files_root is '.@trueFalse($cnvrtd_files_root, $cnvrtd_files_root, '(empty)').'&nbsp; '
		.@trueFalse($cnvrtd_root_exists, "(exists, with $cnvrtd_root_perms perms)", "(doesn't exist)")."<br />\n"
		.'$captions_root is '.@trueFalse($captions_root, $captions_root, '(empty)').'&nbsp; '
		.@trueFalse($captions_root_exists, "(exists, with $captions_root_perms perms)", "(doesn't exist)")."<br />\n"
		.'$convert_magick is '.trueFalse($convert_magick).',&nbsp; '
		.'$convert_cmd is '.@trueFalse($convert_cmd, $convert_cmd, '(empty)'). '&nbsp; '
		.@trueFalse($convert_cmd_exists, "(exists, with $convert_cmd_perms perms)", "(possibly doesn't exist)")."<br />\n"
		.'$convert_GD is '.trueFalse($convert_GD).',&nbsp; ';
	if (!empty($convert_GD_ver)) {
		$ext_loaded = '';
		if (! extension_loaded('gd')) {
			$ext_loaded = ' (extension not loaded)';
		}
		$diag_mesgs .= "GD version is set to $convert_GD_ver$ext_loaded,&nbsp; ";
	} else if ($gdv = gdVersion()) {
		$diag_mesgs .= "GD version $gdv detected,&nbsp; ";
	} else {
		$diag_mesgs .= "GD is not detected,&nbsp; ";
	}
	if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
		$diag_mesgs .= "phpinfo() function is disabled<br />\n";
	} else {
		$diag_mesgs .= "phpinfo() function is enabled<br />\n";
	}
	$diag_mesgs .= '$convert_writable is '.trueFalse($convert_writable).',&nbsp; '
		.'$convert_readable is '.trueFalse($convert_readable).',&nbsp; '
		.'$reqd_view is '.$reqd_view."<br />\n"
		.'$thmbs_ena is '.trueFalse($thmbs_ena).',&nbsp; '
		.'$namelinks_ena is '.trueFalse($namelinks_ena).',&nbsp; '
		.'$check_security is '.trueFalse($check_security)."<br />\n"
		.'$is_readable_disa is '.trueFalse($is_readable_disa).',&nbsp; '
		.'$file_exists_disa is '.trueFalse($file_exists_disa).',&nbsp; '
		.'$compat_quote is '.trueFalse($compat_quote)."<br /></span>\n";
}

/*
+----------+
|  Output  |
+----------+
*/

/**
* Echo an HTML header if the script is running stand-alone.
*/
echo htmlHeader($header);

/**
* Image is a popup target (experimental).
*/
if (@$get_vars['Qtmp'] == 'popup') {
	echo displayImage($reqd_image, $img_sz_labels);
	echo footerRow();
	echo htmlFooter($header);
	exit();
}

/**
* Open the Qdig gallery table.
*/
// For injecting some output ahead of the gallery
if (!ini_get('register_globals')) { echo $pre_gallery; }
echo <<<EOT

<!-- Begin Qdig Image Gallery v$qdig_version -->
 <table summary="Image Gallery" $qdig_bg_clr_attr id="qdig"
  cellpadding="0" cellspacing="0" border="0">
 <tr>\n
EOT;

/**
* Directory Navigation
*/
$dirnav_out = dirNav($dir_nav);
echo $dirnav_out['str'];

/**
* Control Bar
*/
if ($reqd_img_size_tmp == 'Ctrl') {
	$dirnav_out['ctrl_bar_ena'] = TRUE;
}
// Show it if it's enabled
if ($ctrl_bar['enable'] == TRUE && $dirnav_out['ctrl_bar_ena'] == TRUE) {
	// Only open Control Bar if appropriate
	if (count($imgs) > 1)
	{
		$control_bar_on = TRUE;
		echo <<<EOT
\n <!-- Begin Qdig control bar -->
 <td colspan="2">
 <table summary="Control Bar"
  width="100%" cellpadding="0" cellspacing="0">
  <tr>\n
EOT;
	} else {
		$control_bar_on = FALSE;
	}

	// Control for selecting links view.
	if ($ctrl_bar['vw_ctrl'] == TRUE ) {
		echo controlView($ctrl_links_mesg);
	}

	// Size preferences
	if ($ctrl_bar['sz_ctrl'] == TRUE ) {
		echo controlSize($img_sz_labels['ctrl']);
	}

	// Close Control Bar if open
	if ($control_bar_on == TRUE) {
		echo <<<EOT
  </tr>
 </table>
 </td>
 </tr><tr>
 <!-- End Qdig control bar -->\n
EOT;
	}
}
// End Control Bar

/**
* Sidebar for Text Image Links if appropriate
*/
if ($no_lnks_view == FALSE && ! @$omit_navlinks == TRUE) {
	$namelinks = sideBar($namelinks_ena,  $thmbs_ena,
		$sidebar_bg_clr, $sidebar_margin_top, $sidebar_height);
	if (isset($namelinks['sb_on'])) {
		$sidebar = $namelinks['sb_on'];
	} else {
		$sidebar = FALSE;
	}
	echo $namelinks['str'];
} else {
	$sidebar = FALSE;
}
// End Sidebar

/**
* Main qdig image display area
*/
if ($display_area == TRUE) {
	echo <<<EOT
\n <!-- Begin Qdig gallery image display area -->
 <td$img_tbl_bg_clr_attr>
 <table summary="image display area"
  width="100%" cellpadding="2" cellspacing="0" border="0">
 <tr><td>\n\n
EOT;
} elseif (empty($subdirs) && $reqd_image['pwd'] == $rootdir) {
	echo <<<EOT
 <td$img_tbl_bg_clr_attr>
 <table summary="$empty_gallery_msg"
  width="100%" cellpadding="2" cellspacing="0" border="0">
 <tr><td>
 <br />
 <div align="center">$empty_gallery_msg</div>\n
EOT;
}

// Show Text Numeral or Thumbnail Links to all of the images (above image).
if ($sidebar == FALSE
	&& $no_lnks_view == FALSE
	&& ! @$omit_navlinks == TRUE
	&& $img_links_above == TRUE)
{
	echo displayImageLinks($thmb_row, $nmrl_row);
}

// Show an Upper Navigation Row, above the displayed image.
echo navRow($upr_nav);

// Display the requested image or else the first image.
echo displayImage($reqd_image, $img_sz_labels);

// Lower Navigation Row, below the displayed image.
echo navRow($lwr_nav);

// Show Text Numeral or Thumbnail Links to all of the images (below image).
if ($sidebar == FALSE
	&& $no_lnks_view == FALSE
	&& ! @$omit_navlinks == TRUE
	&& $img_links_above == FALSE)
{
	echo displayImageLinks($thmb_row, $nmrl_row);
}

// Gallery Footer Row (site link / copyright / qdig home link)
echo footerRow();

if ($display_area == TRUE ) {
	echo <<<EOT
\n </td></tr></table></td>
 <!-- End Qdig gallery image display area -->\n
EOT;
} elseif (empty($subdirs) && $reqd_image['pwd'] == $rootdir) {
	echo "\n".' </td></tr></table></td>';
}
// End main qdig image display area.

/**
* Echo messages, if any.
*/
if (!empty($thumbs_msg) || !empty($resize_msg) || !empty($diag_mesgs)) {
	if (!empty($diag_mesgs)){
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$end_time = $mtime[1] + $mtime[0];
		$exec_time = round(($end_time - $start_time), 3) * 1000;
		$max_time = ini_get("max_execution_time");
		$diag_mesgs .= "Execution time is $exec_time milliseconds, max_execution_time is $max_time seconds\n"
			."</small></p>\n";
	}
	$msgs = "\n <tr><td colspan='2'>\n  <div style='text-align:left;'>";
	$msgs .= "\n  <!-- messages -->\n";
	$msgs .= $resize_msg;
	$msgs .= @$thumbs_msg;
	$msgs .= $diag_mesgs;
	$msgs .= "  </div>\n </td></tr>\n";
} else {
	$msgs = '';
}

/**
* Close the Qdig gallery table.
*/
if (empty($imgs) && !empty($subdirs)) {
	$tag = '</td>';
} else {
	$tag = '';
}
echo <<<EOT
\n $tag</tr>$msgs</table>
<!-- End Qdig Image Gallery v$qdig_version -->\n\n
EOT;
// For injecting some output after the gallery
if (!ini_get('register_globals')) { echo $post_gallery; }

/**
* Echo an HTML Footer if the script is running stand-alone.
*/
echo htmlFooter($header);

// Leave the working dir and error reporting the way we found them.
if (isset($orig_wd)) {
	chdir($orig_wd);
}
error_reporting($orig_err_rep_level);

/* vim: set noexpandtab tabstop=4 shiftwidth=4: */
