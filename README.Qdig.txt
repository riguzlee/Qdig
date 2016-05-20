Qdig - Quick Digital Image Gallery
==================================
README.txt

Qdig is an easy-to-use PHP script that presents your digital
image files as an online gallery or set of galleries dynamically
from a directory or tree of directories.

Qdig's home on the web is at http://qdig.sourceforge.net/

Qdig installation is simple -- just put the script in a directory
that contains some image files and/or subdirectories with image
files, then browse that directory's URL.  Qdig is a one-file program
and doesn't require a DBMS.

Qdig can generate thumbnails and smaller resampled versions of large
images using either Image Magick or PHP's GD extensions.  Writing
converted (resampled) images to disk requires some simple additional
setup steps (create a directory and set permissions on it twice).
Shell access is preferred, but not required, to perform these steps.

Gallery management is also easy.  Most users can completely manage
their galleries using their favorite FTP or SCP file management program.

Starting with version 1.2.0 you can edit image captions on-the-fly by
clicking a link on an image-display page. This feature uses admin.php,
the Qdig Gallery Management Script.  The admin script is also easy to
install; typically you only need to edit it and add your username and
password.

Featureful, Adaptable, and Usable

Qdig has features to help you present your images the way you want them
presented.  You can easily add image captions; you can offer each image
in multiple sizes; you can change the color scheme; you can enable,
disable, and choose the placement of various gallery elements; you can
choose the size, quality level and placement of thumbnail images.  These
are just examples of the dozens of configuration settings, which are
documented with comments in the script.

The script works stand-alone or you can include() it within another PHP
script and it will automatically adapt by suppressing the HTML header
and footer.  Qdig should be easy to integrate into your web site, portal,
content management system or now even weblog.  It generates standards-
compliant XHTML 1.0 output using the following tags: <a>, <img>, <center>,
<small>, <div>, <span>, <table>, <tr>, <td>, <b>, and sometimes <br>.

An embedded gallery will take on the style of the calling page because
the CSS style elements for a stand-alone gallery are in the HTML header.
Here are a few example lines to put into your CSS stylesheet.

img.qdig-image { background-color:#eeeeee; border:1px solid #cccccc; }
img.qdig-thumb { background-color:#eeeeee; border:0px solid #cccccc; }
td.qdig-caption { font-family:Verdana, Arial, Helvetica, sans-serif; }
#qdig a { font-weight:bold; text-decoration: none; }
#qdig a:visited { color:green; font-weight:bold; text-decoration:none; }
#qdig a:hover { text-decoration:underline; }

Qdig is designed with usability in mind.  Resampling images to similar
sizes gives your gallery a consistent look and conserves bandwidth.
Gallery visitors can be allowed to change some preferences to suit
themselves.  Qdig gallery pages take up minimal space in the browser
window.  Qdig tries to be efficient with valuable screen pixels,
so visitors can scroll less and view images more.


INSTALLATION, LICENSE, and CHANGELOG
====================================

See INSTALL.txt, LICENSE.txt, and CHANGELOG.txt


QDIG DESIGN GOALS
=================

Ease-of-use
  Qdig should be very quick and simple to install and to use, at least
  for basic functionality.  Once Qdig is installed, galleries should be
  easy to manage, even for inexperienced computer users.

Security
  Qdig should be able to work with as little security risk as possible.
  The script works with PHP's `register_globals' flag turned off.
  Generating thumbnails, alternate-sized images and .txt files for
  captions requires write permissions, but only within the directory
  tree specified for the purpose of storing those files.

Standards-compliance
  The generated output and the script itself should comply to applicable
  standards (XHTML 1.0 Transitional and PEAR Coding Standards respectively).


$Id: README.txt,v 1.9 2005/04/02 07:36:27 haganfox Exp $
