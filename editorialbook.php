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
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
if ($_POST['submit1']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $editorialuser = 0;
    } else {
        $editorialuser = $xoopsUser->uid();
    }

    //Make sure only 1 anonymous from an IP in a single day.

    $anonwaitdays = 1;

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    $editorial = $myts->addSlashes($_POST['editorial']);

    // Check if editorial is Null

    if ('' == $editorial) {
        redirect_header('editorialbook.php?lid=' . $lid . '', 4, _MD_NOEDITORIAL);

        exit();
    }

    $datetime = time();

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_editorials') . " SET editorialuser='$editorialuser',editorial='$editorial', editorialhostname='$ip', editorialtimestamp='$datetime' WHERE lid=$lid") or $eh::show('0013');

    $ratemessage = _MD_EDITAPPRE . '<br>' . sprintf(_MD_THANKYOU, $xoopsConfig[sitename]);

    redirect_header('index.php', 4, $ratemessage);

    exit();
}
if ($_POST['delete']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $editorialuser = 0;
    } else {
        $editorialuser = $xoopsUser->uid();
    }

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    $editorial = $myts->addSlashes($_POST['editorial']);

    //All is well. Delete Line Item Rate to Editorial DB.

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . ' WHERE lid=' . $lid . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $ratemessage = _MD_EDITORIALDEL . '<br>' . sprintf(_MD_THANKYOU, $xoopsConfig[sitename]);

    redirect_header('index.php', 4, $ratemessage);

    exit();
}
if ($_POST['submit']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $editorialuser = 0;
    } else {
        $editorialuser = $xoopsUser->uid();
    }

    //Make sure only 1 anonymous from an IP in a single day.

    $anonwaitdays = 1;

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    $editorial = $myts->addSlashes($_POST['editorial']);

    // Check if editorial is Null

    if ('' == $editorial) {
        redirect_header('editorialbook.php?lid=' . $lid . '', 4, _MD_NOEDITORIAL);

        exit();
    }

    //All is well. Add to Line Item Rate to Editorial DB.

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_editorials') . '_editorialid_seq');

    $datetime = time();

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_editorials') . " (editorialid, lid, editorialuser, editorial, editorialhostname, editorialtimestamp) VALUES ($newid, $lid, $editorialuser, '$editorial', '$ip', $datetime)") or $eh('0013');

    $ratemessage = _MD_EDITAPPRE . '<br>' . sprintf(_MD_THANKYOU, $xoopsConfig[sitename]);

    redirect_header('index.php', 4, $ratemessage);

    exit();
}
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
// Check if ADMINISTRATOR has already posted an editorial.
$submitter = $xoopsUser->uid();
$result350 = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . " WHERE lid=$lid and editorialuser=$submitter");
[$editorialuserDB] = $xoopsDB->fetchRow($result350);
if ($editorialuserDB) {
    $result = $xoopsDB->query('SELECT title FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

    [$title] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $submitter = $xoopsUser->uid();

    $result2000 = $xoopsDB->query('SELECT editorial FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . " WHERE lid=$lid AND editorialuser=$submitter");

    [$editorial] = $xoopsDB->fetchRow($result2000);

    $editorial = htmlspecialchars($editorial, ENT_QUOTES | ENT_HTML5);

    echo '
<hr>
<table border=0 cellpadding=1 cellspacing=0 width="80%"><tr><td>
<h5>' . _MD_EDITEDITORIAL . "</h5>
<h4>$title</h4>";

    echo " </td></tr>
<tr><td align=\"center\">
<form method=\"POST\" action=\"editorialbook.php\">
<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

    echo "<textarea name=\"editorial\" cols=\"75\" rows=\"10\">$editorial</textarea>\n";

    echo '<br><br><input type="submit" name="submit1" value="' . _MD_EDITORIALEDITIT . "\"\n>";

    echo '&nbsp;<input type="submit" name="delete" value="' . _MD_EDITORIALDELETE . "\"\n>";

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . "\" onclick=\"javascript:history.go(-1)\">\n";

    echo '</form></td></tr></table>';
} else {
    $result = $xoopsDB->query('SELECT title FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

    [$title] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    echo '
<hr>
<table border=0 cellpadding=1 cellspacing=0 width="80%"><tr><td>
<h5>' . _MD_WRITEEDITORIAL . "</h5>
<h4>$title</h4>";

    echo " </td></tr>
<tr><td align=\"center\">
<form method=\"POST\" action=\"editorialbook.php\">
<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

    echo "<textarea name=\"editorial\" cols=\"75\" rows=\"10\"></textarea>\n";

    echo '<br><br><input type="submit" name="submit" value="' . _MD_EDITORIALIT . "\"\n>";

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . "\" onclick=\"javascript:history.go(-1)\">\n";

    echo '</form></td></tr></table>';
}
CloseTable();

include 'footer.php';
