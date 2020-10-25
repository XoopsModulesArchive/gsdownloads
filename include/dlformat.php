<?php

include '../cache/config.php';
include 'config.php';
include '../../mainfile.php';
include 'catconfig.php';
if ('dlformatread.php' == basename($GLOBALS['PHP_SELF'])) {
    exit();
}
echo '<tr>';
if ($gsdownloads_useshots) {
    $tablewidth = $gsdownloads_shotwidth + 10;

    echo "<td width='" . $tablewidth . "' align='center'";

    if ($logourl) {
        echo "><a href='" . XOOPS_URL . '/modules/gsdownloads/visit.php?lid=' . $lid . "' target='_blank'><img src='" . XOOPS_URL . '/modules/gsdownloads/images/shots/' . $logourl . "' width='" . $gsdownloads_shotwidth . "' border='0'></a></td><td>";
    } else {
        echo " style='display: none'></td><td colspan='2'>";
    }
} else {
    echo '<td>';
}
$path = $mytree->getPathFromId($cid, 'title');
$path = mb_substr($path, 1);
$path = str_replace('/', " <img src='" . XOOPS_URL . "/modules/gsdownloads/images/arrow.gif' board='0' alt=''> ", $path);
echo "<table width='100%' border='0' cellspacing='1' cellpadding='0' class='bg2'><tr><td>";
echo "<table width='100%' border='0' cellspacing='1' cellpadding='0' class='bg2'><tr><td>";
echo "<table width='100%' border='0' cellspacing='1' cellpadding='4' class='bg2'><tr><td colspan='2'>";
echo '<b>' . _MD_CATEGORYC . '</b>' . $path . '';
echo '</td></tr>';
echo '<tr>';
echo "<td class='bg4' width=50%>" . _MD_DLTITLE . '';
echo '&nbsp;<b>' . $dtitle . '</b>';
newdownloadgraphic($time, $status);
popgraphic($hits);
echo "</td><td class='bg4' align='right'>";
if ('0' != $rating || '0.0' != $rating) {
    if (1 == $votes) {
        $votestring = _MD_ONEVOTE;
    } else {
        $votestring = sprintf(_MD_NUMVOTES, $votes);
    }

    echo '<b>' . _MD_RATINGC . "</b>$rating ($votestring)";
}
echo '&nbsp;';
echo '' . _MD_DLTIMES . " <b>$hits</b> " . _MD_PRICEC . '' . _MD_MONEY . "&nbsp;<b>$price<b></td></tr>";
echo "<tr><td colspan='7' class='bg2'>";
// echo "<img src='".XOOPS_URL."/modules/gsdownloads/images/decs.gif' board='0' width='14' height='14' align='buttom' alt='"._MD_DESCRIPTION."'>&nbsp;:&nbsp; $description";
echo '</td></tr>';
echo '</td></tr>';
echo "</table></table><table width=100% class='bg2'>\n";
##########################################
##This is the total review display code.##
##########################################
echo "<img src='images/redpixel.gif'width=100% height=1 Vspace=0 Hspace=0>\n";
$formatted_date = formatTimestamp($reviewtimestamp);
$review = $myts->displayTarea($review, 1);
$reviewuname = XoopsUser::getUnameFromId($reviewuser);
$resulttot = $xoopsDB->query('SELECT rating FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . " WHERE lid = $lid");
$totalraw = 0;
$totalint = 0;
$totaltotal = 0;
$totbar = 0;
$totalstuff = 0;
$q = 0;
while (list($total) = $xoopsDB->fetchRow($resulttot)) {
    $totalstuff += $total;

    $q++;
}
$totalraw = $totalstuff / $q;
$totalint = (int)($totalstuff / $q) + .5;
if ($totalraw > $totalint) {
    $totaltotal = (int)$totalraw + 1;
} else {
    $totaltotal = (int)$totalraw;
}
//$q=0;
$totbar = ($totaltotal / $gsdownloads_totrate) * 100;
if ($q > 0) {
    echo "<table border=0 width=100% class='bg2'><tr><td width=266 valign=top><table width=266><td align=right><font size=2><b>" . _MD_AVERAGE . "</b></font></td><td width=58><font size=2><b>&nbsp;&nbsp;1</b></font></td><td align=right width=58><font size=2><b>$gsdownloads_totrate&nbsp;&nbsp;</b></font></td></tr><tr><td align=right width=100><font size=2><b>$gsdownloads_totname:</b></font></td><td colspan=2> <img src='images/leftbar1.gif' alt='$gsdownloads_totname: $totaltotal'><img src='images/mainbar1.gif' height=15 width=$totbar alt='$gsdownloads_totname: $totaltotal'><img src='images/rightbar1.gif' alt='$gsdownloads_totname: $totaltotal'></td>";

    for ($e = 1; $e < $gsdownloads_catnum + 1; $e++) {
        $resultcat[$e] = $xoopsDB->query('SELECT rating FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . " WHERE lid=$lid AND ratingcat=$e");

        $x = 0;

        while (list($totcatrate[$e]) = $xoopsDB->fetchRow($resultcat[$e])) {
            $x++;

            $tottotcatrate[$e] += $totcatrate[$e];
        }

        $totalcatrateraw[$e] = $tottotcatrate[$e] / $x;

        $totalcatrateint[$e] = (int)($tottotcatrate[$e] / $x) + .5;

        if ($totalcatrateraw[$e] > $totalcatrateint[$e]) {
            $catrate[$e] = (int)$totalcatrateraw[$e] + 1;
        } else {
            $catrate[$e] = (int)$totalcatrateraw[$e];
        }

        //echo "$catrate[$e]";

        $catbar[$e] = ($catrate[$e] / $gsdownloads_maxrate) * 100;

        $tottotcatrate[$e] = 0;
    }

    for ($e = 1; $e < $gsdownloads_catnum + 1; $e++) {
        echo "<tr><td align=right width=100><font size=2>$gsdownloads_catname[$e]:</font></td><td colspan=2><img src='images/leftbar.gif' alt='$gsdownloads_catname[$e]: $catrate[$e]'><img src='images/mainbar.gif' height=16 width=$catbar[$e] alt='$gsdownloads_catname[$e]: $catrate[$e]'><img src='images/rightbar.gif' alt='$gsdownloads_catname[$e]: $catrate[$e]'></td><td><font size=2></font></td></tr>";

        $catrate[$e] = 0;

        $catbar[$e] = 0;
    }

    echo '</table>';

    echo '</td><td valign=top><table border=0 width=100%><tr><td align=right width=50%>' . _MD_DATE . ": <b>$datetime </b></td></tr><tr><td colspan=2><b>" . _MD_DESCRIPTION . "</b>&nbsp;:&nbsp; $description</td></tr></table>";

    echo '</td></tr>';

    echo '</table>';

//echo "</table>";
##############################################
##This is the end of the total display code.##
##############################################
} else {
    echo "<table border=0 width=100% class='bg2'><tr><td width=266 valign=top><table width=266><td align=right><font size=2><b></b></font></td><td width=58><font size=2><b></b></font></td><td align=right width=58><font size=2><b></b></font></td></tr><tr><td align=right width=100><font size=2><b></b></font></td><td colspan=2></td>";

    /* for ($e=1;$e<$gsdownloads_catnum+1;$e++){
    $resultcat[$e]=$xoopsDB->query("SELECT rating FROM ".$xoopsDB->prefix("gsdownloads_votecat")." WHERE lid=$lid AND ratingcat=$e");
    $x=0;
    while(list($totcatrate[$e])=$xoopsDB->fetchRow($resultcat[$e]))
    {
    $x++;
    $tottotcatrate[$e]+=$totcatrate[$e];
    }
    $totalcatrateraw[$e]=$tottotcatrate[$e]/$x;
    $totalcatrateint[$e]=intval($tottotcatrate[$e]/$x)+.5;
    if ($totalcatrateraw[$e]>$totalcatrateint[$e])
    {
    $catrate[$e]=intval($totalcatrateraw[$e])+1;
    }
    else
    {
    $catrate[$e]=intval($totalcatrateraw[$e]);
    }
    //echo "$catrate[$e]";
    $catbar[$e]=($catrate[$e]/$gsdownloads_maxrate)*100;
    } */

    /*for ($e=1;$e<$gsdownloads_catnum+1;$e++){
    //echo "<tr><td align=right width=100><font size=2>$gsdownloads_catname[$e]:</font></td><td colspan=2><img src='images/leftbar.gif' alt='$gsdownloads_catname[$e]: $catrate[$e]'><img src='images/mainbar.gif' height=16 width=$catbar[$e] alt='$gsdownloads_catname[$e]: $catrate[$e]'><img src='images/rightbar.gif' alt='$gsdownloads_catname[$e]: $catrate[$e]'></td><td><font size=2></font></td></tr>";
    }*/

    echo '</table>';

    echo '</td><td valign=top><table border=0 width=100%><tr><td align=right width=50%>' . _MD_DATE . ": <b>$datetime </b></td></tr><tr><td colspan=2><b>" . _MD_DESCRIPTION . "</b>&nbsp;:&nbsp; $description</td></tr></table>";

    echo '</td></tr>';

    echo '</table>';

    //echo "</table>";
}
$q = 0;
echo "<tr><td colspan='2' class='bg4' align='center'>";
//voting & comments stats
if (0 != $comments) {
    if (1 == $comments) {
        $poststring = _MD_ONEPOST;
    } else {
        $poststring = sprintf(_MD_NUMPOSTS, $comments);
    }

    echo '<b>' . _MD_COMMENTSC . "</b>$poststring";
}
//The Link Engine
echo "<table width=100%><tr><td class='bg1' colspan='2' align='center'><b>";
//This statement ties the book links to PayPal or to a hard download link
if (_MD_FREE == $price) {
    echo "<table align=center width='100%'>\n";

    echo "<tr>\n";

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        echo "<td align=middle><img src='" . $gsdownloads_eximage[$x] . "' width='16' height='16' border='0'align='absmiddle'>&nbsp;<b>" . $gsdownloads_extitle[$x] . "</b></td>\n";
    }

    echo "</tr>\n";

    echo "<tr>\n";

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        echo "<td align=middle><a href='" . XOOPS_URL . '/modules/gsdownloads/dl' . $x . ".php?lid=$lid' target='_blank'><img src='" . $gsdownloads_exdlimage[$x] . "' border='0' align='absmiddle' alt='" . _MD_DOWNLOADNOW . "'></a></b></td>\n";
    }

    echo "</tr>\n";

    echo "</table>\n";

    echo "<br>\n";
} else {
    //This section ties the link to PayPal if the customer has to pay for the file.

    echo "<table align=center width='100%'>\n";

    echo "<tr>\n";

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        echo "<td align=middle><img src='" . $gsdownloads_eximage[$x] . "' width='16' height='16' border='0' align='absmiddle'>&nbsp;<b>" . $gsdownloads_extitle[$x] . "</b></td>\n";
    }

    echo "</tr>\n";

    echo "<tr>\n";

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        echo "<td align=middle>\n";

        echo "<form action=https://www.paypal.com/cgi-bin/webscr method=post>\n";

        echo "<input type=hidden name=cmd value=_xclick>\n";

        echo "<input type=hidden name=business value='$homepage'>\n";

        echo "<input type=hidden name=item_name value='$dtitle, " . $gsdownloads_extitle[$x] . " format'>\n";

        echo "<input type=hidden name=item_number value=$lid>\n";

        echo "<input type=hidden name=amount value='$price'>\n";

        echo "<input type=hidden name=no_shipping value=1>\n";

        echo "<input type=hidden name=return value='" . XOOPS_URL . '/modules/gsdownloads/dl' . $x . ".php?lid=$lid'>\n";

        echo "<input type=hidden name=cancel_return value='" . XOOPS_URL . "'>\n";

        echo "<input type=hidden name=no_note value=1>\n";

        echo '<input type=image src=' . $gsdownloads_exdlbuyimage[$x] . " border=0 name=submit alt='" . _MD_BUYPAYPAL . "'>\n";

        echo "</form>\n";

        echo "</td>\n";
    }

    echo "</td>\n";

    echo "</tr>\n";

    echo "</table>\n";
}
echo '</td></tr></table>';
//THIS IS THE END OF THE LINK CODE
echo "<table width=100% border=1><tr><td colspan=2 align='center'><b>" . _MD_VERSIONC . "</b> $version &nbsp;&nbsp; <b>" . _MD_FILESIZEC . '</b> ' . PrettySize($size) . '&nbsp;&nbsp; <b>' . _MD_PLATFORMC . "</b>$platform &nbsp;&nbsp;</td></tr></table>";
//echo "<a href='javascript:history.go(-1)'>"._MD_BACKSTEP."</a>";
// echo " | <a href='".XOOPS_URL."/modules/gsdownloads/excerptfile.php?lid=".$lid."'>"._MD_READEXCERPT."</a>";
//echo " | <a href='".XOOPS_URL."/modules/gsdownloads/ratefile.php?lid=".$lid."'>"._MD_RATETHISFILE."</a>";
$result300 = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid=$lid");
[$reviewuserDB] = $xoopsDB->fetchRow($result300);
$result350 = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . " WHERE lid=$lid");
[$editorialuserDB] = $xoopsDB->fetchRow($result350);
if ($reviewuserDB || $editorialuserDB) {
    echo "<a href='" . XOOPS_URL . '/modules/gsdownloads/detailfile.php?lid=' . $lid . "'>" . _MD_DETAILS . '</a> | ';
}
// echo "<a href='".XOOPS_URL."/modules/gsdownloads/detailfile.php?lid=".$lid."'>"._MD_DETAILS."</a> | ";
// echo "<a href='".XOOPS_URL."/modules/gsdownloads/ratefile.php?lid=".$lid."'>"._MD_RATETHISFILE."</a>";
echo "<a target='_top' href='mailto:?subject=" . rawurlencode(sprintf(_MD_INTFILEAT, $xoopsConfig['sitename'])) . '&body=' . rawurlencode(sprintf(_MD_INTFILEFOUND, $xoopsConfig['sitename']) . ': ' . XOOPS_URL . '/modules/gsdownloads/singlefile.php?lid=' . $lid) . "'>" . _MD_TELLAFRIEND . '</a>';
if ($xoopsUser) {
    $submitter = $xoopsUser->uid();

    $result240 = $xoopsDB->query('SELECT reviewuser FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid=$lid AND reviewuser=$submitter");

    [$reviewuserDB] = $xoopsDB->fetchRow($result240);

    if ($reviewuserDB) {
        echo " | <a href='" . XOOPS_URL . '/modules/gsdownloads/reviewbook.php?lid=' . $lid . "'>" . _MD_REVIEWEDIT . '</a>';
    } else {
        echo " | <a href='" . XOOPS_URL . '/modules/gsdownloads/reviewbook.php?lid=' . $lid . "'>" . _MD_VSCOMMENTS . '</a>';
    }
}
echo " | <a target='_top' href='mailto:" . ($xoopsConfig['adminmail']) . '?subject=' . rawurlencode(sprintf(_MD_MAILBROKEN1, $dtitle)) . '&body=' . rawurlencode(sprintf(_MD_BROKENLINK) . ': ' . XOOPS_URL . '/modules/gsdownloads/singlefile.php?lid=' . $lid) . "'>" . _MD_REPORTBROKEN . '</a>';
global $xoopsUser;
if ($xoopsUser) {
    $submitter = $xoopsUser->uid();

    if ($xoopsUser->isAdmin($xoopsModule->mid())) {
        $result3000 = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . " WHERE lid=$lid AND editorialuser=$submitter");

        [$editorialuserDB] = $xoopsDB->fetchRow($result3000);

        if ($editorialuserDB) {
            echo " | <a href='" . XOOPS_URL . '/modules/gsdownloads/editorialbook.php?lid=' . $lid . "'>" . _MD_EDITORIALEDIT . '</a>';
        } else {
            echo " | <a href='" . XOOPS_URL . '/modules/gsdownloads/editorialbook.php?lid=' . $lid . "'>" . _MD_EDITORIAL . '</a>';
        }

        echo " | <a href='" . XOOPS_URL . '/modules/gsdownloads/admin/index.php?lid=' . $lid . "&fct=gsdownloads&op=modDownload'>" . _MD_EDIT . '</a>';
    }
}
echo '</td></tr></table>';
