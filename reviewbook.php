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
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include '/cache/config.php';
include 'catconfig.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
if (!$xoopsUser) {
    redirect_header('index.php', 4, _MD_CANTREVIEW);

    exit();
}
if ($_POST['submit1']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $reviewuser = 0;

        $ratinguser = 0;
    } else {
        $reviewuser = $xoopsUser->uid();

        $ratinguser = $xoopsUser->uid();
    }

    //Make sure only 1 anonymous from an IP in a single day.

    $anonwaitdays = 1;

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    $review = $myts->addSlashes($_POST['review']);

    $totradio = (int)$_POST['totradio'];

    // Check if review is Null

    if ('' == $review) {
        redirect_header('reviewbook.php?lid=' . $lid . '', 4, _MD_NOREVIEW);

        exit();
    }

    //All is well. Add to Line Item Rate to review DB.

    $datetime = time();

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_reviews') . " SET review='$review', reviewhostname='$ip', reviewtimestamp='$datetime' WHERE lid=$lid AND reviewuser='$reviewuser' ") or $eh::show('0013');

    //All is well. Add totalvote data to the votedata table.

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_votedata') . " SET rating=$totradio, ratinghostname='$ip', ratingtimestamp='$datetime' WHERE lid=$lid AND ratinguser='$ratinguser' ") or $eh::show('0013');

    //All is well. Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.

    updaterating($lid);

    //All is well. Add category votes to votecat DB.

    for ($x = 1; $x < $gsdownloads_catnum + 1; $x++) {
        $catradio[$x] = $_POST['catradio' . $x . ''];

        $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_votecat') . " SET rating=$catradio[$x], ratinghostname='$ip', ratingtimestamp='$datetime' WHERE lid=$lid AND ratingcat=$x AND ratinguser='$ratinguser' ") or $eh::show('0013');
    }

    $ratemessage = _MD_REVIEWAPPRE . '<br>' . sprintf(_MD_THANKYOU, $xoopsConfig[sitename]);

    redirect_header('index.php', 4, $ratemessage);

    exit();
}
if ($_POST['delete']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $reviewuser = 0;

        $ratinguser = 0;
    } else {
        $reviewuser = $xoopsUser->uid();

        $ratinguser = $xoopsUser->uid();
    }

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    $review = $myts->addSlashes($_POST['review']);

    //All is well. Delete Line Item Rate to review DB.

    global $xoopsDB, $_GET, $eh;

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid=$lid and reviewuser=$reviewuser";

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . " WHERE lid=$lid and ratinguser=$ratinguser";

    $xoopsDB->query($query) or $eh::show('0013');

    updaterating($lid);

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . " WHERE lid=$lid and ratinguser=$ratinguser";

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_REVIEWDELETED);

    exit();
}
if ($_POST['submit']) {
    $eh = new ErrorHandler(); //ErrorHandler object

    if (!$xoopsUser) {
        $reviewuser = 0;

        $ratinguser = 0;
    } else {
        $reviewuser = $xoopsUser->uid();

        $ratinguser = $xoopsUser->uid();
    }

    //Make sure only 1 anonymous from an IP in a single day.

    $anonwaitdays = 1;

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    $review = $myts->addSlashes($_POST['review']);

    $totradio = (int)$_POST['totradio'];

    // Check if review is Null

    if ('' == $review) {
        redirect_header('reviewbook.php?lid=' . $lid . '', 4, _MD_NOREVIEW);

        exit();
    }

    // Check if REG user is trying to originally submit twice.

    $result = $xoopsDB->query('SELECT ratinguser FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . " WHERE lid=$lid");

    while (list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
        if ($ratinguserDB == $ratinguser) {
            redirect_header('index.php', 4, _MD_VOTEONCE);

            exit();
        }
    }

    //All is well. Add to Line Item Rate to review DB.

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_reviews') . '_reviewid_seq');

    $datetime = time();

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_reviews') . " (reviewid, lid, reviewuser, review, reviewhostname, reviewtimestamp) VALUES ($newid, $lid, $reviewuser, '$review', '$ip', $datetime)") or $eh('0013');

    //All is well. Add totalvote data to the votedata table.

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_votedata') . " (ratingid, lid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES ($newid, $lid, $ratinguser, $totradio, '$ip', $datetime)") or $eh('0013');

    //All is well. Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.

    updaterating($lid);

    //All is well. Add category votes to votecat DB.

    for ($x = 1; $x < $gsdownloads_catnum + 1; $x++) {
        $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_votecat') . '_ratingid_seq');

        $catradio[$x] = $_POST['catradio' . $x . ''];

        $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_votecat') . " (ratingid, lid, ratinguser, rating, ratingcat, ratinghostname, ratingtimestamp) VALUES ($newid, $lid, $ratinguser, $catradio[$x], $x, '$ip', $datetime)") or $eh('0013');
    }

    $ratemessage = _MD_REVIEWAPPRE . '<br>' . sprintf(_MD_THANKYOU, $xoopsConfig[sitename]);

    redirect_header('index.php', 4, $ratemessage);

    exit();
}
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
$submitter = $xoopsUser->uid();
$result350 = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid=$lid AND reviewuser=$submitter");
[$reviewuserDB] = $xoopsDB->fetchRow($result350);
if ($reviewuserDB) {
    $result = $xoopsDB->query('SELECT title FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

    [$title] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $submitter = $xoopsUser->uid();

    $result2000 = $xoopsDB->query('SELECT review FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid=$lid AND reviewuser=$submitter");

    [$review] = $xoopsDB->fetchRow($result2000);

    $review = htmlspecialchars($review, ENT_QUOTES | ENT_HTML5);

    $result3000 = $xoopsDB->query('SELECT rating FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . " WHERE lid=$lid AND ratinguser=$submitter");

    [$totrate] = $xoopsDB->fetchRow($result3000);

    $result4000 = $xoopsDB->query('SELECT rating, ratingcat FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . " WHERE lid=$lid AND ratinguser=$submitter");

    echo '
<hr>
<table border=0 cellpadding=1 cellspacing=0 width="80%"><tr><td>
<h4>' . _MD_EDITREVIEW . " $title</h4>";

    echo ' </td></tr></table>';

    echo "<center><b>$gsdownloads_totname</b></center>";

    echo '<table border=1 cellpadding=1 cellspacing=0 width="50%"> <tr><td align=left></td>';

    echo '<form method="POST" action="reviewbook.php">';

    for ($x = 1; $x < $gsdownloads_totrate + 1; $x++) {
        echo "<td align=center>$x</td>";
    }

    echo '</tr>';

    echo '<tr><td align=right width=90>' . _MD_RATING . '</td>';

    for ($x = 1; $x < $gsdownloads_totrate + 1; $x++) {
        if ($x == $totrate) {
            echo "<td align=center><INPUT TYPE=RADIO NAME='totradio' VALUE='$x' checked></td>";
        } else {
            echo "<td align=center><INPUT TYPE=RADIO NAME='totradio' VALUE='$x'></td>";
        }
    }

    echo '</tr></table>';

    echo "<img src='images/shim.gif' width=1 height=4>";

    echo "<center><b>$gsdownloads_features</b></center>";

    echo ' <table border=1 cellpadding=1 cellspacing=0 width="50%"> <tr><td align=left></td>';

    // while(list($catrate, $ratingcat)=$xoopsDB->fetchRow($result4000))

    for ($x = 1; $x < $gsdownloads_maxrate + 1; $x++) {
        echo "<td align=center>$x</td>";
    }

    echo '</tr>';

    $x = 1;

    while (list($catrate, $ratingcat) = $xoopsDB->fetchRow($result4000)) {
        echo '<tr>';

        echo "<td align=right width=90>$gsdownloads_catname[$x]</td>";

        for ($y = 1; $y < $gsdownloads_maxrate + 1; $y++) {
            if ($y == $catrate) {
                echo "<td align=center><INPUT TYPE=RADIO NAME='catradio" . $x . "' VALUE=$y checked></td>";
            } else {
                echo "<td align=center><INPUT TYPE=RADIO NAME='catradio" . $x . "' VALUE=$y></td>";
            }
        }

        $x++;
    }

    $x = 0;

    echo '</table>';

    echo '<br>';

    echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

    echo "<textarea name=\"review\" cols=\"50\" rows=\"10\">$review</textarea>\n";

    echo '<br><br><input type="submit" name="submit1" value="' . _MD_REVIEWEDITIT . "\"\n>";

    echo '&nbsp;<input type="submit" name="delete" value="' . _MD_DELETE . "\"\n>";

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . "\" onclick=\"javascript:history.go(-1)\">\n";

    echo '</form></td></tr></table>';
} else {
    $result = $xoopsDB->query('SELECT title FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

    [$title] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    echo '
<hr>
<table border=0 cellpadding=1 cellspacing=0 width="80%"> <tr><td>
<h4>' . _MD_REVIEW . " $title.</h4>";

    echo '</td></tr></table>';

    echo "<center><b>$gsdownloads_totname</b></center>";

    echo '<table border=1 cellpadding=1 cellspacing=0 width="50%">';

    echo '<td></td>';

    echo '<form method="POST" action="reviewbook.php">';

    for ($x = 1; $x < $gsdownloads_totrate + 1; $x++) {
        echo "<td align=center>$x</td>";
    }

    echo '</tr>';

    echo '<tr><td align=right width=90>' . _MD_RATING . '</td>';

    $totcheckrate = (int)($gsdownloads_totrate / 2);

    for ($x = 1; $x < $gsdownloads_totrate + 1; $x++) {
        if ($x == $totcheckrate) {
            echo "<td align=center><INPUT TYPE=RADIO NAME='totradio' VALUE='$x' checked></td>";
        } else {
            echo "<td align=center><INPUT TYPE=RADIO NAME='totradio' VALUE='$x'></td>";
        }
    }

    echo '</tr></table>';

    echo "<img src='images/shim.gif' width=1 height=4>";

    echo "<center><b>$gsdownloads_features</b></center>";

    echo ' <table border=1 cellpadding=1 cellspacing=0 width="50%"> <tr><td align=left></td>';

    for ($x = 1; $x < $gsdownloads_maxrate + 1; $x++) {
        echo "<td align=center>$x</td>";
    }

    echo '</tr>';

    $checkrate = (int)($gsdownloads_maxrate / 2);

    for ($x = 1; $x < $gsdownloads_catnum + 1; $x++) {
        echo '<tr>';

        echo "<td align=right width=90>$gsdownloads_catname[$x]</td>";

        for ($y = 1; $y < $gsdownloads_maxrate + 1; $y++) {
            if ($y == $checkrate) {
                echo "<td align=center><INPUT TYPE=RADIO NAME='catradio" . $x . "' VALUE=$y checked></td>";
            } else {
                echo "<td align=center><INPUT TYPE=RADIO NAME='catradio" . $x . "' VALUE=$y></td>";
            }
        }

        echo '</tr>';
    }

    echo '</table>';

    echo "</td></tr>
<tr><td align=center>
<input type=\"hidden\" name=\"lid\" value=\"$lid\">";

    echo "<textarea name=\"review\" cols=\"50\" rows=\"10\"></textarea>\n";

    echo '<br><br><input type="submit" name="submit" value="' . _MD_REVIEWIT . "\"\n>";

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . "\" onclick=\"javascript:history.go(-1)\">\n";

    echo '</form></td></tr></table>';
}
CloseTable();

include 'footer.php';
