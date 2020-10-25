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
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
$mytree = new XoopsTree($xoopsDB->prefix('gsdownloads_cat'), 'cid', 'pid');
if ($_POST['submit']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

        exit();
    }

    $ratinguser = $xoopsUser->uid();

    $lid = (int)$_POST['lid'];

    // Check if Title exist

    if ('' == $_POST['title']) {
        $eh::show('1001');
    }

    // Check if URL exist

    if ('' == $_POST['url']) {
        $eh::show('1016');
    }

    // Check if HOMEPAGE exist

    if ('' == $_POST['homepage']) {
        $eh::show('1016');
    }

    // Check if Description exist

    if ('' == $_POST['description']) {
        $eh::show('1008');
    }

    $url = $myts->addSlashes($url);

    $logourl = $myts->addSlashes($_POST['logourl']);

    $cid = (int)$_POST['cid'];

    $title = $myts->addSlashes($_POST['title']);

    $homepage = $myts->addSlashes($_POST['homepage']);

    $version = $myts->addSlashes($version);

    $size = $myts->addSlashes($size);

    $platform = $myts->addSlashes($platform);

    $price = $myts->addSlashes($price);

    $description = $myts->addSlashes($_POST['description']);

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_mod') . '_requestid_seq');

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_mod') . " (requestid, lid, cid, title, url, homepage, version, size, platform, price, logourl, description, modifysubmitter) VALUES ($newid, $lid, $cid, '$title', '$url', '$homepage', '$version', $size, '$platform', '&price', '$logourl', '$description', $ratinguser)") or $eh::show('0013');

    redirect_header('index.php', 2, _MD_THANKSFORINFO);

    exit();
}
$lid = (int)$_GET['lid'];
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

    exit();
}
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
echo '<table width="80%" align="center">';
$result = $xoopsDB->query('SELECT cid, title, url, homepage, version, size, platform, price, logourl FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE lid=' . $lid . ' AND status>0');
echo _MD_REQUESTMOD . '<br><br>';
[$cid, $title, $url, $homepage, $version, $size, $platform, $price, $logourl] = $xoopsDB->fetchRow($result);
$title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);
$url = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5);
$homepage = htmlspecialchars($homepage, ENT_QUOTES | ENT_HTML5);
$version = htmlspecialchars($version, ENT_QUOTES | ENT_HTML5);
$size = htmlspecialchars($size, ENT_QUOTES | ENT_HTML5);
$platform = htmlspecialchars($platform, ENT_QUOTES | ENT_HTML5);
$price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);
$logourl = htmlspecialchars($logourl, ENT_QUOTES | ENT_HTML5);
# $logourl = urldecode($logourl);
$result2 = $xoopsDB->query('SELECT description FROM ' . $xoopsDB->prefix('gsdownloads_text') . " WHERE lid=$lid");
[$description] = $xoopsDB->fetchRow($result2);
$description = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);
echo '<form action="modfile.php" method="post">';
echo '<table width="80%"><tr><td align="right">';
echo '<b>' . _MD_FILEID . '</b></td><td>';
echo $lid;
echo '</td></tr><tr><td align="right"><b>' . _MD_FILETITLE . '</b></td><td>';
echo "<input type=\"text\" name=\"title\" size=\"50\" maxlength=\"100\" value=\"$title\">";
echo '</td></tr><tr><td align="right"><b>' . _MD_DLURL . '</b></td><td>';
echo '<input type="text" name="url" size="50" maxlength="250" value="' . $url . '">';
echo '</td></tr>';
echo '<tr><td align="right"><b>' . _MD_CATEGORYC . '</b></td><td>';
$mytree->makeMySelBox('title', 'title', $cid);
echo "</td></tr><tr><td></td><td></td></tr>\n";
echo '<tr><td align="right"><b>' . _MD_HOMEPAGEC . "</b></td><td>\n";
echo '<input type="text" name="homepage" size="50" maxlength="100" value="' . $homepage . "\"></td></tr>\n";
echo '<tr><td align="right"><b>' . _MD_VERSIONC . "</b></td><td>\n";
echo '<input type="text" name="version" size="10" maxlength="10" value="' . $version . "\"></td></tr>\n";
echo '<tr><td align="right"><b>' . _MD_FILESIZEC . "</b></td><td>\n";
echo "<input type=\"text\" name=\"size\" size=\"10\" maxlength=\"8\" value=\"$size\">" . _MD_BYTES . "</td></tr>\n";
echo '<tr><td align="right"><b>' . _MD_PLATFORMC . "</b></td><td>\n";
echo "<input type=\"text\" name=\"platform\" size=\"45\" maxlength=\"50\" value=\"$platform\"></td></tr>\n";
echo '<tr><td align="right"><b>' . _MD_PRICEC . "</b></td><td>\n";
echo "<input type=\"text\" name=\"price\" size=\"45\" maxlength=\"50\" value=\"$price\"></td></tr>\n";
# echo "<tr><td align=\"right\">logo</td><td>\n";
# echo "<input type=\"text\" name=\"logourl\" size=\"50\" maxlength=\"60\" value=\"$logourl\"></td></tr>\n";
echo '<tr><td align="right" valign="top"><b>' . _MD_DESCRIPTIONC . "</b></td><td>\n";
echo '<textarea name="description" cols="60" rows="5">' . $description . "</textarea>\n";
echo "</td></tr>\n";
echo "<tr><td colspan=\"2\" align=\"center\"><br>\n";
echo "<input type=\"hidden\" name=\"logourl\" value=\"$logourl\"></input>";
echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\"></input>";
echo '</input><input name="submit" type="submit" value="' . _MD_SUBMIT . '"></input></form>';
echo '</td></tr></table>';
CloseTable();

include 'footer.php';
