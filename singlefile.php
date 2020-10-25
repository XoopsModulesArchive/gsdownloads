<?php

// ------------------------------------------------------------------------- //
// XOOPS - PHP Content Management System //
// <http://xoops.codigolivre.org.br> //
// ------------------------------------------------------------------------- //
// Based on: //
// myPHPNUKE Web Portal System - http://myphpnuke.com/ //
// PHP-NUKE Web Portal System - http://phpnuke.org/ //
// Thatware - http://thatware.org/ //
// ------------------------------------------------------------------------- //
// This program is free software; you can redistribute it and/or modify //
// it under the terms of the GNU General Public License as published by //
// the Free Software Foundation; either version 2 of the License, or //
// (at your option) any later version.  //
//   //
// This program is distributed in the hope that it will be useful, //
// but WITHOUT ANY WARRANTY; without even the implied warranty of //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the //
// GNU General Public License for more details. //
//   //
// You should have received a copy of the GNU General Public License //
// along with this program; if not, write to the Free Software //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA //
// ------------------------------------------------------------------------- //
// gsdownloads  //
// by Eric R. Evans  //
// GiantSpider Publisher  //
// http://www.giantspider.biz  //
// based on mydownloads  //
// ------------------------------------------------------------------------- //
include 'header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
$mytree = new XoopsTree($xoopsDB->prefix('gsdownloads_cat'), 'cid', 'pid');
// Used to view just a single DL file information. Called from the rating pages
$lid = (int)$_GET['lid'];
//$cid = $_GET['cid'];
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
$q = 'SELECT d.lid, d.cid, d.title, d.url, d.homepage, d.version, d.size, d.platform, d.price, d.logourl, d.status, d.date, d.hits, d.rating, d.votes, d.comments, t.description FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' d, ' . $xoopsDB->prefix('gsdownloads_text') . " t WHERE d.lid=$lid AND d.lid=t.lid AND status>0";
$result = $xoopsDB->query($q);
[$lid, $cid, $title, $url, $homepage, $version, $size, $platform, $price, $logourl, $status, $time, $hits, $rating, $votes, $comments, $description] = $xoopsDB->fetchRow($result);
echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\"><tr><td align=\"center\">\n";
echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\" bgcolor=\"cccccc\"><tr><td>\n";
$pathstring = '<a href=index.php>' . _MD_MAIN . '</a>&nbsp;:&nbsp;';
$nicepath = $mytree->getNicePathFromId($cid, 'title', 'viewcat.php?op=');
$pathstring .= $nicepath;
echo '<b>' . $pathstring . '</b>';
echo '</td></tr></table><br>';
echo '<table width="100%" cellspacing=0 cellpadding=10 border=0>';
$rating = number_format($rating, 2);
$dtitle = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
$url = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5);
$url = urldecode($url);
$homepage = htmlspecialchars($homepage, ENT_QUOTES | ENT_HTML5);
$version = htmlspecialchars($version, ENT_QUOTES | ENT_HTML5);
$size = htmlspecialchars($size, ENT_QUOTES | ENT_HTML5);
$platform = htmlspecialchars($platform, ENT_QUOTES | ENT_HTML5);
$price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);
$logourl = htmlspecialchars($logourl, ENT_QUOTES | ENT_HTML5);
#$logourl = urldecode($logourl);
$datetime = formatTimestamp($time, 's');
$description = $myts->displayTarea($description, 0);
include 'include/dlformat.php';
echo "</td></tr></table>\n";
echo "</td></tr></table>\n";
CloseTable();
include 'footer.php';
