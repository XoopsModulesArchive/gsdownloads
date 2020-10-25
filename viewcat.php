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
$cid = (int)$_GET['cid'];
require XOOPS_ROOT_PATH . '/header.php';
OpenTable();
mainheader();
if ('' != $_GET['show']) {
    $show = (int)$_GET['show'];
} else {
    $show = $gsdownloads_perpage;
}
if (!isset($_GET['min'])) {
    $min = 0;
} else {
    $min = (int)$_GET['min'];
}
if (!isset($max)) {
    $max = $min + $show;
}
if (isset($_GET['orderby'])) {
    $orderby = convertorderbyin($_GET['orderby']);
} else {
    $orderby = 'title ASC';
}
echo "<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td align='center'>\n";
echo "<table width='100%' cellspacing='1' cellpadding='2' border='0' class='bg3'><tr><td>\n";
$pathstring = "<a href='index.php'>" . _MD_MAIN . '</a>&nbsp;:&nbsp;';
$nicepath = $mytree->getNicePathFromId($cid, 'title', 'viewcat.php?op=');
$pathstring .= $nicepath;
echo '<b>' . $pathstring . '</b>';
echo '</td></tr></table>';
// get child category objects
$arr = [];
$arr = $mytree->getFirstChild($cid, 'title');
if (count($arr) > 0) {
    echo '</td></tr>';

    echo "<tr><td align='left'><h4>" . _MD_CATEGORIES . "</h4></td></tr>\n";

    echo "<tr><td align='center'>";

    $scount = 0;

    echo "<table width='90%'><tr>";

    foreach ($arr as $ele) {
        $title = htmlspecialchars($ele['title'], ENT_QUOTES | ENT_HTML5);

        $totaldownload = getTotalItems($ele['cid'], 1);

        echo "<td align='left'><b><a href=viewcat.php?cid=" . $ele['cid'] . '>' . $title . '</a></b>&nbsp;(' . $totaldownload . ')&nbsp;&nbsp;</td>';

        $scount++;

        if (4 == $scount) {
            echo '</tr><tr>';

            $scount = 0;
        }
    }

    echo "</tr></table><br>\n";

    echo '<hr>';
}
$fullcountresult = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE cid=$cid AND status>0");
[$numrows] = $xoopsDB->fetchRow($fullcountresult);
if ($numrows > 0) {
    $q = 'SELECT d.lid, d.title, d.url, d.homepage, d.version, d.size, d.platform, d.price, d.logourl, d.status, d.date, d.hits, d.rating, d.votes, d.comments, t.description FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' d, ' . $xoopsDB->prefix('gsdownloads_text') . ' t WHERE cid=' . $cid . ' AND d.status>0 AND d.lid=t.lid ORDER BY ' . $orderby . '';

    $result = $xoopsDB->query($q, $show, $min);

    //if 2 or more items in result, show the sort menu

    if ($numrows > 1) {
        $orderbyTrans = convertorderbytrans($orderby);

        echo '<br><small><center>' . _MD_SORTBY . '&nbsp;&nbsp;
' . _MD_TITLE . " (<a href='viewcat.php?cid=$cid&orderby=titleA'><img src='images/up.gif' border='0' align='middle' alt=''></a><a href='viewcat.php?cid=$cid&orderby=titleD'><img src='images/down.gif' border='0' align='middle' alt=''></a>)
" . _MD_DATE . " (<a href='viewcat.php?cid=$cid&orderby=dateA'><img src='images/up.gif' border='0' align='middle' alt=''></a><a href='viewcat.php?cid=$cid&orderby=dateD'><img src='images/down.gif' border='0' align='middle' alt=''></a>)
" . _MD_RATING . " (<a href='viewcat.php?cid=$cid&orderby=ratingA'><img src='images/up.gif' border='0' align='middle' alt=''></a><a href=viewcat.php?cid=$cid&orderby=ratingD><img src='images/down.gif' border='0' align='middle' alt=''></a>)
" . _MD_POPULARITY . " (<a href='viewcat.php?cid=$cid&orderby=hitsA'><img src='images/up.gif' border='0' align='middle' alt=''></a><a href='viewcat.php?cid=$cid&orderby=hitsD'><img src='images/down.gif' border='0' align='middle' alt=''></a>)
";

        echo '<br><b><small>';

        printf(_MD_CURSORTBY, $orderbyTrans);

        echo '</small></b><br><br></center>';
    }

    echo "<table width='100%' cellspacing=0 cellpadding=10 border=0>";

    $x = 0;

    while (list($lid, $dtitle, $url, $homepage, $version, $size, $platform, $price, $logourl, $status, $time, $hits, $rating, $votes, $comments, $description) = $xoopsDB->fetchRow($result)) {
        $rating = number_format($rating, 2);

        $dtitle = htmlspecialchars($dtitle, ENT_QUOTES | ENT_HTML5);

        $url = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5);

        $url = urldecode($url);

        $homepage = htmlspecialchars($homepage, ENT_QUOTES | ENT_HTML5);

        $version = htmlspecialchars($version, ENT_QUOTES | ENT_HTML5);

        $size = htmlspecialchars($size, ENT_QUOTES | ENT_HTML5);

        $platform = htmlspecialchars($platform, ENT_QUOTES | ENT_HTML5);

        $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

        $logourl = htmlspecialchars($logourl, ENT_QUOTES | ENT_HTML5);

        # $logourl = urldecode($logourl);

        $datetime = formatTimestamp($time);

        $description = $myts->displayTarea($description, 1);

        include 'include/dlformat.php';

        $x++;
    }

    echo '</table>';

    $orderby = convertorderbyout($orderby);

    //Calculates how many pages exist. Which page one should be on, etc...

    $downloadpages = ceil($numrows / $show);

    //Page Numbering

    if (1 != $downloadpages && 0 != $downloadpages) {
        echo '<br><br>';

        $prev = $min - $show;

        if ($prev >= 0) {
            echo "&nbsp;<a href='viewcat.php?cid=$cid&min=$prev&orderby=$orderby&show=$show'>";

            echo '<b>&lt; ' . _MD_PREVIOUS . ' </b></a>&nbsp;';
        }

        $counter = 1;

        $currentpage = ($max / $show);

        while ($counter <= $downloadpages) {
            $mintemp = ($show * $counter) - $show;

            if ($counter == $currentpage) {
                echo "<b>$counter</b>&nbsp;";
            } else {
                echo "<a href='viewcat.php?cid=$cid&min=$mintemp&orderby=$orderby&show=$show'>$counter</a>&nbsp;";
            }

            $counter++;
        }

        if ($numrows > $max) {
            echo "&nbsp;<a href='viewcat.php?cid=$cid&min=$max&orderby=$orderby&show=$show'>";

            echo '<b> ' . _MD_NEXT . ' &gt;</b></a>';
        }
    }
}
echo "</td></tr></table>\n";
CloseTable();
include 'footer.php';
