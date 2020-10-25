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
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
if ($submit) {
    if (!$xoopsUser) {
        $sender = 0;
    } else {
        $sender = $xoopsUser->uid();
    }

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    if (0 != $sender) {
        // Check if REG user is trying to report twice.

        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_broken') . " WHERE lid=$lid AND sender=$sender");

        [$count] = $xoopsDB->fetchRow($result);

        if ($count > 0) {
            redirect_header('index.php', 2, _MD_ALREADYREPORTED);

            exit();
        }
    }

    // Check if the sender is trying to vote more than once.

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_broken') . " WHERE lid=$lid AND ip = '$ip'");

    [$count] = $xoopsDB->fetchRow($result);

    if ($count > 0) {
        redirect_header('index.php', 2, _MD_ALREADYREPORTED);

        exit();
    }

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_broken') . '_reportid_seq');

    $query = 'INSERT INTO ' . $xoopsDB->prefix('gsdownloads_broken') . " (reportid, lid, sender, ip) VALUES ($newid, $lid, $sender, '$ip')";

    $xoopsDB->query($query) || die('');

    redirect_header('index.php', 2, _MD_THANKSFORINFO);

    exit();
}
$lid = (int)$_GET['lid'];
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
echo '<div><h4>' . _MD_REPORTBROKEN . '</h4><br><br>';
echo "<form action='brokenfile.php' method='post'>";
echo "<input type='hidden' name='lid' value='$lid'>";
echo _MD_THANKSFORHELP;
echo '<br>' . _MD_FORSECURITY . '<br><br>';
echo "<input type='submit' name='submit' value='" . _MD_REPORTBROKEN . "'>";
echo "&nbsp;<input type='button' value='" . _MD_CANCEL . "' onclick='javascript:history.go(-1)'>";
echo '</form></div>';
CloseTable();

include 'footer.php';
