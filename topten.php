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
require XOOPS_ROOT_PATH . '/header.php';
//generates top 10 charts by rating and hits for each main category
OpenTable();
mainheader();
if ($rate) {
    $sort = _MD_RATING;

    $sortDB = 'rating';
} else {
    $sort = _MD_HITS;

    $sortDB = 'hits';
}
$arr = [];
$result = $xoopsDB->query('SELECT cid, title FROM ' . $xoopsDB->prefix('gsdownloads_cat') . ' WHERE pid=0');
while (list($cid, $ctitle) = $xoopsDB->fetchRow($result)) {
    $boxtitle = '<big>';

    $boxtitle .= sprintf(_MD_TOP10, $ctitle);

    $boxtitle .= ' (' . $sort . ')</big>';

    $thing = "<table width='100%' border='0'><tr><td width='7%' class='bg3'><b>" . _MD_RANK . "</b></td><td width='28%' class='bg3'><b>" . _MD_TITLE . "</b></td><td width='40%' class='bg3'><b>" . _MD_CATEGORY . "</b></td><td width='8%' class='bg3' align='center'><b>" . _MD_HITS . "</b></td><td width='9%' class='bg3' align='center'><b>" . _MD_RATING . "</b></td><td width='8%' class='bg3' align='right'><b>" . _MD_VOTE . '</b></td></tr>';

    $query = 'SELECT lid, cid, title, hits, rating, votes FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE status>0 AND (cid=$cid";

    // get all child cat ids for a given cat id

    $arr = $mytree->getAllChildId($cid);

    $size = count($arr);

    for ($i = 0; $i < $size; $i++) {
        $query .= ' OR cid=' . $arr[$i] . '';
    }

    $query .= ') ORDER BY ' . $sortDB . ' DESC';

    $result2 = $xoopsDB->query($query, 10, 0);

    $rank = 1;

    while (list($did, $dcid, $dtitle, $hits, $rating, $votes) = $xoopsDB->fetchRow($result2)) {
        $rating = number_format($rating, 2);

        if ($hit) {
            $hits = "<span class='fg2'>$hits</span>";
        } elseif ($rate) {
            $rating = "<span class='fg2'>$rating</span>";
        }

        $catpath = $mytree->getPathFromId($dcid, 'title');

        $catpath = mb_substr($catpath, 1);

        $catpath = str_replace('/', " <span class='fg2'>&raquo;&raquo;</span> ", $catpath);

        $thing .= "<tr><td>$rank</td>";

        $thing .= "<td><a href='singlefile.php?lid=$did'>$dtitle</a></td>";

        $thing .= "<td>$catpath</td>";

        $thing .= "<td align='center'>$hits</td>";

        $thing .= "<td align='center'>$rating</td><td align='right'>$votes</td></tr>";

        $rank++;
    }

    $thing .= '</table>';

    themecenterposts($boxtitle, $thing);

    echo '<br>';
}
CloseTable();
include 'footer.php';
