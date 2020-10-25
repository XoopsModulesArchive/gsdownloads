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
$q = 'SELECT cid, title, imgurl FROM ' . $xoopsDB->prefix('gsdownloads_cat') . ' WHERE pid = 0 ORDER BY title';
$result = $xoopsDB->query($q) || die('');
if ('gsdownloads' == $xoopsConfig['startpage']) {
    $xoopsOption['show_rblock'] = 1;

    require XOOPS_ROOT_PATH . '/header.php';

    make_cblock();

// echo "<br>";
} else {
    $xoopsOption['show_rblock'] = 1;

    require XOOPS_ROOT_PATH . '/header.php';
}
OpenTable();
$mainlink = 0;
mainheader($mainlink);
echo "<center>\n";
echo "<table border=\"0\" cellspacing=\"5\" cellpadding=\"0\" width=\"90%\"><tr>\n";
$count = 0;
while ($myrow = $xoopsDB->fetchArray($result)) {
    $title = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

    echo '<td valign="top" align="right">';

    if ($myrow['imgurl'] && 'http://' != $myrow['imgurl']) {
        $imgurl = htmlspecialchars($myrow['imgurl'], ENT_QUOTES | ENT_HTML5);

        echo '<a href="' . XOOPS_URL . '/modules/gsdownloads/viewcat.php?cid=' . $myrow['cid'] . '"><img src="' . $imgurl . '" height="50" border="0"></a>';
    } else {
        echo '';
    }

    $totaldownload = getTotalItems($myrow['cid'], 1);

    echo '</td><td valign="top" width="40%"><a href="' . XOOPS_URL . '/modules/gsdownloads/viewcat.php?cid=' . $myrow['cid'] . "\"><b>$title</b></a>&nbsp;($totaldownload)<br>";

    // get child category objects

    $arr = [];

    $arr = $mytree->getFirstChild($myrow['cid'], 'title');

    $space = 0;

    $chcount = 0;

    foreach ($arr as $ele) {
        $chtitle = htmlspecialchars($ele['title'], ENT_QUOTES | ENT_HTML5);

        if ($chcount > 5) {
            echo '...';

            break;
        }

        if ($space > 0) {
            echo ', ';
        }

        echo '<a href="' . XOOPS_URL . '/modules/gsdownloads/viewcat.php?cid=' . $ele['cid'] . '">' . $chtitle . '</a>';

        $space++;

        $chcount++;
    }

    if ($count < 1) {
        echo '</td>';
    }

    $count++;

    if (2 == $count) {
        echo '</td></tr><tr>';

        $count = 0;
    }
}
echo '</td></tr></table>';
[$numrows] = $xoopsDB->fetchRow($xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE status>0'));
echo '<br><br>';
printf(_MD_THEREARE, $numrows);
echo '</center>';
CloseTable();
//echo "<br>";
//OpenTable();
//echo "<div align=\"center\"><big><b>"._MD_LATESTLIST."</b></big><br><br>";
//showNew($mytree);
//echo "</div>";
//CloseTable();
require XOOPS_ROOT_PATH . '/modules/gsdownloads/footer.php';
// Shows the Latest Listings on the front page
//function showNew($mytree){
// global $myts, $xoopsDB, $xoopsConfig, $xoopsModule;
// global $gsdownloads_shotwidth, $gsdownloads_newdownloads, $gsdownloads_useshots;
// $result = $xoopsDB->query("SELECT d.lid, d.cid, d.title, d.url, d.homepage, d.version, d.size, d.platform, d.price, d.logourl, d.status, d.date, d.hits, d.rating, d.votes, d.comments, t.description FROM ".$xoopsDB->prefix("gsdownloads_downloads")." d, ".$xoopsDB->prefix("gsdownloads_text")." t WHERE d.status>0 AND d.lid=t.lid ORDER BY date DESC",$gsdownloads_newdownloads,0);
// echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"10\" border=\"0\"><tr><td width=\"110\" align=\"center\">";
// $x=0;
// while(list($lid, $cid, $dtitle, $url, $homepage, $version, $size, $platform, $price, $logourl, $status, $time, $hits, $rating, $votes, $comments, $description)=$xoopsDB->fetchRow($result)) {
// $rating = number_format($rating, 2);
// $dtitle = htmlspecialchars($dtitle);
// $url = htmlspecialchars($url);
// $homepage = htmlspecialchars($homepage);
// $version = htmlspecialchars($version);
// $size = htmlspecialchars($size);
// $platform = htmlspecialchars($platform);
// $price = htmlspecialchars($price);
// $logourl = htmlspecialchars($logourl);
# $logourl = urldecode($logourl);
// $datetime = formatTimestamp($time,"s");
// $description = $myts->displayTarea($description,0); //no html
// require XOOPS_ROOT_PATH."/modules/gsdownloads/include/dlformat.php";
$x++;
// }
// echo "</table>";
//}
