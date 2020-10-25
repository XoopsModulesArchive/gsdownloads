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
// GNU General Public Li ense for more details. //
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
include '/cache/config.php';
include '/ulconf/exten.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
$eh = new ErrorHandler(); //ErrorHandler object
$mytree = new XoopsTree($xoopsDB->prefix('gsdownloads_cat'), 'cid', 'pid');
function bad_ext($filename)
{
    global $ext;

    $exp = explode('.', $filename);

    $nb = count($exp);

    for ($i = 0; $i < $nb; $i++) {
        if ($exp[($nb - 1)] == $ext[$i]) {
            return false;
            exit;
        }
    }

    return true;
}
if ($_POST['submit']) {
    if (!$xoopsUser) {
        redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

        exit();
    }

    if (!isset($_POST['submitter'])) {
        $submitter = $xoopsUser->uid();
    } else {
        $submitter = (int)$_POST['submitter'];
    }

    $x = 0;

    global $ext;

    $exp = explode('.', $userfile);

    $nb = count($exp);

    for ($i = 0; $i < $nb; $i++) {
        if ($exp[($nb - 1)] == $ext[$i]) {
            $x = 1;
        }
    }

    if (file_exists($gsdownloads_uphome . $userfile_name)) {
        echo '<center><br><br><b>' . _MD_NOFILE . '<br><br>';
    } elseif ($userfile_size > $gsdownloads_sizemax) {
        echo '<center><br><br><b>' . _MD_FILETOOBIG . '<br><br>';
    } elseif (1 == $x) {
        echo "<center><br><br><b>Transfert de Fichiers</b><br><br>$userfile_name<br><br><b><font color=\"red\">ce type de fichiers n est pas accepte</font></b><br><br><a href=javascript:history.back();>Cliquez ici</a></center><br><br><br><br><br>";
    } else {
        copy($userfile, $gsdownloads_uphome . $userfile_name);

        if (is_uploaded_file($userfile)) {
            echo '<center><br><br><b>' . _MD_FILEUPSUK . '<br><br>';
        } else {
            echo "<center><br><br><b>Transfert de Fichiers</b><br><br>$userfile_name<br><br><b>" . _MD_PROBLEMCOPY . '<br><br>';
        }
    }

    // Check if Title exist

    if ('' == $_POST['title']) {
        $eh::show('1001');
    }

    // Check if URL exist

    if (($_POST['url']) || ('' != $_POST['url'])) {
        $url = $_POST['url'];
    }

    if ('' == $url) {
        $eh::show('1016');
    }

    /* // Check if HomePage exist
    if ($_POST["homepage"]=="") {
    $eh->show("1001");
    } */

    // Check if Description exist

    if ('' == $_POST['description']) {
        $eh::show('1008');
    }

    if (!empty($_POST['cid'])) {
        $cid = (int)$_POST['cid'];
    } else {
        $cid = 0;
    }

    $url = $myts->addSlashes($url);

    $title = $myts->addSlashes($_POST['title']);

    $homepage = $myts->addSlashes($_POST['homepage']);

    $version = $myts->addSlashes($_POST['version']);

    $size = (int)$_POST['size'];

    $platform = $myts->addSlashes($_POST['platform']);

    $description = $myts->addSlashes($_POST['description']);

    $date = time();

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_downloads') . '_lid_seq');

    $q = 'INSERT INTO ' . $xoopsDB->prefix('gsdownloads_downloads') . " (lid, cid, title, url, homepage, version, size, platform, logourl, submitter, status, date, hits, rating, votes, comments) VALUES ($newid, $cid, '$title', '$url', '$homepage', '$version', $size, '$platform', '', $submitter, 0, $date, 0, 0, 0, 0)";

    $xoopsDB->query($q) or $eh::show('0013');

    if (0 == $newid) {
        $newid = $xoopsDB->getInsertId();
    }

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_text') . " (lid, description) VALUES ($newid, '$description')") or $eh::show('0013');

    // copy($userfile,$gsdownloads_uphome.$userfile_name) or die ("fuck off!");

    redirect_header('index.php', 2, _MD_RECEIVED . '<br>' . _MD_WHENAPPROVED . '');

    exit();
}
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MUSTREGFIRST);

    exit();
}
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
echo "<table width=\"100%\" cellspacing=0 cellpadding=1 border=0><tr><td colspan=2>\n";
echo "<table width=\"100%\" cellspacing=0 cellpadding=8 border=0><tr><td>\n";
echo "<br><br>\n";
echo '<li>' . _MD_SUBMITONCE . "</li>\n";
echo '<li>' . _MD_ALLPENDING . "</li>\n";
echo '<li>' . _MD_DONTABUSE . "</li>\n";
echo '<li>' . _MD_TAKEDAYS . "</li>\n";
echo "<form action=\"submit.php\" method=post enctype='multipart/form-data'>\n";
echo '<table width="80%"><tr>';
echo '<td align="right" nowrap><b>' . _MD_FILETITLE . '</b></td><td>';
echo '<input type="text" name="title" size="50" maxlength="100">';
echo '</td></tr><tr><td align="right" nowrap><b>' . _MD_DLURL . '</b></td><td>';
echo '<input type="text" name="url" size="50" maxlength="250" value="http://">';
echo '</td></tr>';
echo '<tr><td align="right" nowrap><b>' . _MD_CATEGORY . '</b></td><td>';
$mytree->makeMySelBox('title', 'title');
echo "</td></tr>\n";
echo '<tr><td align="right" nowrap><b>' . _MD_PAYPAL . "</b></td><td>\n";
echo "<input type=\"text\" name=\"homepage\" size=\"50\" maxlength=\"100\"></td></tr>\n";
echo '<tr><td align="right" nowrap><b>' . _MD_VERSIONC . "</b></td><td>\n";
echo "<input type=\"text\" name=\"version\" size=\"10\" maxlength=\"10\"></td></tr>\n";
echo '<tr><td align="right" nowrap><b>' . _MD_FILESIZEC . "</b></td><td>\n";
echo '<input type="text" name="size" size="10" maxlength="8">' . _MD_BYTES . "</td></tr>\n";
echo '<tr><td align="right" nowrap><b>' . _MD_PLATFORMC . "</b></td><td>\n";
echo "<input type=\"text\" name=\"platform\" size=\"45\" maxlength=\"50\"></td></tr>\n";
/* echo "<tr><td align=\"right\" nowrap>logourl</td><td>\n";
echo "<input type=\"text\" name=\"logourl\" size=\"50\" maxlength=\"60\"></td></tr>\n";*/
echo '<tr><td align="right" valign="top" nowrap><b>' . _MD_DESCRIPTIONC . "</b></td><td>\n";
echo "<textarea name=description cols=50 rows=6></textarea>\n";
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"MAX_FILE_SIZE\" VALUE=\"$gsdownloads_sizemax\">";
echo '<tr><td align=right><b>' . _MD_UPLOAD . ':</b></td><td><input name="userfile" type="file"></td></tr>';
// echo "</td><td>\n";
// echo "</td></tr>\n";
echo "</table>\n";
echo '<br>';
echo '<input type="hidden" name="submitter" value="' . $xoopsUser->uid() . '"></input>';
echo '<center><input type="submit" name="submit" class="button" value="' . _MD_SUBMIT . "\"></input>\n";
echo '&nbsp;<input type=button value=' . _MD_CANCEL . " onclick=\"javascript:history.go(-1)\"></input></center>\n";
echo "</form>\n";
echo '</td></tr></table></td></tr></table>';
CloseTable();

include 'footer.php';
