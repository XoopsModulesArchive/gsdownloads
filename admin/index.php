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
include 'admin_header.php';
include '../include/functions.php';
include '../cache/config.php';
include '../include/config.php';
include '../ulconf/exten.php';
include '../catconfig.php';
include '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler();
$mytree = new XoopsTree($xoopsDB->prefix('gsdownloads_cat'), 'cid', 'pid');
function gsdownloads()
{
    global $xoopsDB;

    xoops_cp_header();

    OpenTable();

    // Temporarily 'homeless' downloads (to be revised in index.php breakup)

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_broken') . '');

    [$totalbrokendownloads] = $xoopsDB->fetchRow($result);

    if ($totalbrokendownloads > 0) {
        $totalbrokendownloads = "<font color=\"#ff0000\"><b>$totalbrokendownloads</b></font>";
    }

    $result2 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_mod') . '');

    [$totalmodrequests] = $xoopsDB->fetchRow($result2);

    if ($totalmodrequests > 0) {
        $totalmodrequests = "<font color=\"#ff0000\"><b>$totalmodrequests</b></font>";
    }

    $result3 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE status=0');

    [$totalnewdownloads] = $xoopsDB->fetchRow($result3);

    if ($totalnewdownloads > 0) {
        $totalnewdownloads = "<font color=\"#ff0000\"><b>$totalnewdownloads</b></font>";
    }

    echo ' - <a href=index.php?op=gsdownloadsConfigAdmin>' . _MD_GENERALSET . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=booksConfigMenu>' . _MD_MANAGEBOOKS . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=catConfigMenu>' . _MD_MANAGECATEGORIES . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=gsdownloadsCat>' . _MD_MANAGERATECAT . '</a>';

    echo '<br><br>';

    echo ' - <a href=index.php?op=gsdownloadsExtensions>' . _MD_MANAGEEXTENSIONS . '</a>';

    echo '<br><br>';

    // echo " - <a href=index.php?op=gsdownloadsUploadExtensions>"._MD_MANAGEUPLOADEXTENSIONS."</a>";

    // echo "<br><br>";

    echo ' - <a href=index.php?op=listNewDownloads>' . _MD_DLSWAITING . " ($totalnewdownloads)</a>";

    echo '<br><br>';

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE status>0');

    [$numrows] = $xoopsDB->fetchRow($result);

    echo '<br><br><div align="center">';

    printf(_MD_THEREARE, $numrows);

    echo '</div>';

    CloseTable();

    xoops_cp_footer();
}
//#################
//#################
//################# LISTNEWDOWNLOADS
function listNewDownloads()
{
    global $xoopsDB, $myts, $eh, $mytree;

    // List downloads waiting for validation

    $result = $xoopsDB->query('SELECT lid, cid, title, url, homepage, version, size, platform, price, logourl, submitter FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' where status=0 ORDER BY date DESC');

    $numrows = $xoopsDB->getRowsNum($result);

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_DLSWAITING . "&nbsp;($numrows)</h4><br>";

    if ($numrows > 0) {
        while (list($lid, $cid, $title, $url, $homepage, $version, $size, $platform, $price, $logourl, $uid) = $xoopsDB->fetchRow($result)) {
            $result2 = $xoopsDB->query('SELECT description FROM ' . $xoopsDB->prefix('gsdownloads_text') . " WHERE lid=$lid");

            $result3 = $xoopsDB->query('SELECT description FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . " WHERE lid=$lid");

            [$description] = $xoopsDB->fetchRow($result2);

            [$excerpt] = $xoopsDB->fetchRow($result3);

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $url = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5);

            $homepage = htmlspecialchars($homepage, ENT_QUOTES | ENT_HTML5);

            $version = htmlspecialchars($version, ENT_QUOTES | ENT_HTML5);

            $size = htmlspecialchars($size, ENT_QUOTES | ENT_HTML5);

            $platform = htmlspecialchars($platform, ENT_QUOTES | ENT_HTML5);

            $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

            $description = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);

            $excerpt = htmlspecialchars($excerpt, ENT_QUOTES | ENT_HTML5);

            $submitter = XoopsUser::getUnameFromId($uid);

            echo "<form action=\"index.php\" method=post>\n";

            echo '<table width="80%">';

            echo '<tr><td align="right" nowrap>' . _MD_SUBMITTER . "</td><td>\n";

            echo '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $uid . "\">$submitter</a>";

            echo "</td></tr>\n";

            echo '<tr><td align="right" nowrap>' . _MD_FILETITLE . '</td><td>';

            echo "<input type=\"text\" name=\"title\" size=\"50\" maxlength=\"100\" value=\"$title\">";

            echo '</td></tr><tr><td align="right" nowrap>' . _MD_DLURL . '</td><td>';

            echo "<input type=\"text\" name=\"url\" size=\"50\" maxlength=\"250\" value=\"$url\">";

            echo "&nbsp;[&nbsp;<a href=\"$url\">" . _MD_DOWNLOAD . '</a>&nbsp;]';

            echo '</td></tr>';

            echo '<tr><td align="right" nowrap>' . _MD_CATEGORYC . '</td><td>';

            $mytree->makeMySelBox('title', 'title', $cid);

            echo "</td></tr>\n";

            echo '<tr><td align="right" nowrap>' . _MD_PAYPAL . "</td><td>\n";

            echo "<input type=\"text\" name=\"homepage\" size=\"50\" maxlength=\"100\" value=\"$homepage\"></td></tr>\n";

            echo '<tr><td align="right">' . _MD_VERSIONC . "</td><td>\n";

            echo "<input type=\"text\" name=\"version\" size=\"10\" maxlength=\"10\" value=\"$version\"></td></tr>\n";

            echo '<tr><td align="right">' . _MD_FILESIZEC . "</td><td>\n";

            echo "<input type=\"text\" name=\"size\" size=\"10\" maxlength=\"8\" value=\"$size\">" . _MD_BYTES . "</td></tr>\n";

            echo '<tr><td align="right">' . _MD_PLATFORMC . "</td><td>\n";

            echo "<input type=\"text\" name=\"platform\" size=\"45\" maxlength=\"50\" value=\"$platform\"></td></tr>\n";

            echo '<tr><td align="right">' . _MD_PRICEC . "</td><td>\n";

            echo "<input type=\"text\" name=\"price\" size=\"45\" maxlength=\"50\" value=\"free\"></td></tr>\n";

            echo '<tr><td align="right" valign="top" nowrap>' . _MD_DESCRIPTIONC . "</td><td>\n";

            echo "<textarea name=description cols=\"60\" rows=\"5\">$description</textarea>\n";

            echo "</td></tr>\n";

            // echo "<tr><td align=\"right\" valign=\"top\" nowrap>"._MD_EXCERPTC."</td><td>\n";

            // echo "<textarea name=excerpt cols=\"60\" rows=\"5\">$excerpt</textarea>\n";

            // echo "</td></tr>\n";

            echo '<tr><td align="right" nowrap>' . _MD_SHOTIMAGE . "</td><td>\n";

            echo "<input type=\"text\" name=\"logourl\" size=\"50\" maxlength=\"60\"></td></tr>\n";

            echo '<tr><td></td><td>';

            $directory = XOOPS_URL . '/modules/gsdownloads/images/shots/';

            printf(_MD_MUSTBEVALID, $directory);

            echo "</table>\n";

            echo '<br><input type="hidden" name="op" value="approve"></input>';

            echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\"></input>";

            echo '<input type="submit" value="' . _MD_APPROVE . "\"></form>\n";

            echo myTextForm("index.php?op=delNewDownload&lid=$lid", _MD_DELETE);

            echo '<br><br>';
        }
    } else {
        echo _MD_NOSUBMITTED;
    }

    CloseTable();

    xoops_cp_footer();
}
//######################
//######################
//######################END LISTNEWDOWNLOADS
//#################################APPROVE######################################//
function approve()
{
    global $xoopsConfig, $xoopsDB, $_POST, $myts, $eh;

    $lid = $_POST['lid'];

    $title = $_POST['title'];

    $cid = $_POST['cid'];

    if (empty($cid)) {
        $cid = 0;
    }

    $homepage = $_POST['homepage'];

    $version = $_POST['version'];

    $size = $_POST['size'];

    $platform = $_POST['platform'];

    $price = $_POST['price'];

    $description = $_POST['description'];

    $excerpt = $_POST['excerpt'];

    if (($_POST['url']) || ('' != $_POST['url'])) {
        $url = $myts->addSlashes($_POST['url']);
    }

    $logourl = $myts->addSlashes($_POST['logourl']);

    $title = $myts->addSlashes($title);

    $homepage = $myts->addSlashes($homepage);

    $version = $myts->addSlashes($_POST['version']);

    $size = $myts->addSlashes($_POST['size']);

    $platform = $myts->addSlashes($_POST['platform']);

    $price = $myts->addSlashes($_POST['price']);

    $description = $myts->addSlashes($description);

    $excerpt = $myts->addSlashes($excerpt);

    $query = 'UPDATE ' . $xoopsDB->prefix('gsdownloads_downloads') . " SET cid=$cid, title='$title', url='$url', homepage='$homepage', version='$version', size=$size, platform='$platform', price='price', logourl='$logourl', status=1, date=" . time() . ' where lid=' . $lid . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'UPDATE ' . $xoopsDB->prefix('gsdownloads_text') . " SET description='$description' where lid=" . $lid . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'UPDATE ' . $xoopsDB->prefix('gsdownloads_excerpt') . " SET excerpt='$excerpt' where lid=" . $lid . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $result = $xoopsDB->query('SELECT submitter FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

    [$submitter] = $xoopsDB->fetchRow($result);

    $submitter = new XoopsUser($submitter);

    $subject = sprintf(_MD_YOURFILEAT, $xoopsConfig['sitename']);

    $message = sprintf(_MD_HELLO, $submitter->uname());

    $message .= "\n\n" . _MD_WEAPPROVED . "\n\n";

    $siteurl = XOOPS_URL . '/modules/gsdownloads/';

    $message .= sprintf(_MD_VISITAT, $siteurl);

    $message .= "\n\n" . _MD_THANKSSUBMIT . "\n\n" . $xoopsConfig['sitename'] . "\n" . XOOPS_URL . "\n" . $xoopsConfig['adminmail'] . '';

    $xoopsMailer = getMailer();

    $xoopsMailer->useMail();

    $xoopsMailer->setToEmails($submitter->getVar('email'));

    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

    $xoopsMailer->setFromName($xoopsConfig['sitename']);

    $xoopsMailer->setSubject($subject);

    $xoopsMailer->setBody($message);

    $xoopsMailer->send();

    redirect_header('index.php', 1, _MD_NEWDLADDED);
}
//################################END APPROVE#############################//
function booksConfigMenu()
{
    global $xoopsDB, $myts, $eh, $mytree;

    // Add a New Main Category

    xoops_cp_header();

    // Add a New Sub-Category

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_cat') . '');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        OpenTable();

        echo '<form method=post action=index.php>';

        // If there is a category, add a New Download

        OpenTable();

        echo "<form method=post action=index.php>\n";

        echo '<h4>' . _MD_ADDNEWFILE . "</h4><br>\n";

        echo "<table width=\"80%\"><tr>\n";

        echo '<td align="right">' . _MD_FILETITLE . '</td><td>';

        echo '<input type=text name=title size=50 maxlength=100>';

        echo '</td></tr><tr><td align="right" nowrap>' . _MD_DLURL . '</td><td>';

        echo '<input type=text name=url size=50 maxlength=100 value="http://">';

        echo '</td></tr>';

        echo '<tr><td align="right" nowrap>' . _MD_CATEGORYC . '</td><td>';

        $mytree->makeMySelBox('title', 'title');

        echo "</td></tr><tr><td></td><td></td></tr>\n";

        echo '<tr><td align="right" nowrap>' . _MD_PAYPAL . "</td><td>\n";

        echo "<input type=\"text\" name=\"homepage\" size=\"50\" maxlength=\"100\" value=\"$homepage\"></td></tr>\n";

        echo '<tr><td align="right">' . _MD_VERSIONC . "</td><td>\n";

        echo "<input type=\"text\" name=\"version\" size=\"10\" maxlength=\"10\" value=\"$version\"></td></tr>\n";

        echo '<tr><td align="right">' . _MD_FILESIZEC . "</td><td>\n";

        echo "<input type=\"text\" name=\"size\" size=\"10\" maxlength=\"8\" value=\"$size\">" . _MD_BYTES . "</td></tr>\n";

        echo '<tr><td align="right">' . _MD_PLATFORMC . "</td><td>\n";

        echo "<input type=\"text\" name=\"platform\" size=\"45\" maxlength=\"50\" value=\"$platform\"></td></tr>\n";

        echo '<tr><td align="right">' . _MD_PRICEC . "</td><td>\n";

        echo "<input type=\"text\" name=\"price\" size=\"45\" maxlength=\"50\" value=\"free\"></td></tr>\n";

        echo '<tr><td align="right" valign="top" nowrap>' . _MD_DESCRIPTIONC . "</td><td>\n";

        echo "<textarea name=description cols=\"60\" rows=\"5\">$description</textarea>\n";

        echo "</td></tr>\n";

        // echo "<tr><td align=\"right\" valign=\"top\" nowrap>"._MD_EXCERPTC."</td><td>\n";

        // echo "<textarea name=excerpt cols=60 rows=5>"._MD_NONEEXCERPT."</textarea>\n";

        // echo "</td></tr>\n";

        echo '<tr><td align="right"nowrap>' . _MD_SHOTIMAGE . "</td><td>\n";

        echo "<input type=\"text\" name=\"logourl\" size=\"50\" maxlength=\"60\"></td></tr>\n";

        echo '<tr><td align="right"></td><td>';

        $directory = XOOPS_URL . '/modules/gsdownloads/images/shots/';

        printf(_MD_MUSTBEVALID, $directory);

        echo "</td></tr>\n";

        echo "</table>\n<br>";

        echo '<input type="hidden" name="op" value="addDownload"></input>';

        echo '<input type="submit" class="button" value="' . _MD_ADD . "\"></input>\n";

        echo '</form>';

        CloseTable();
    } else {
        OpenTable();

        echo '<br><br><br>';

        echo '<center><font size=4>' . _MD_MUSTADD . " <a href='index.php?op=catConfigMenu'><font size=4>" . _MD_CATADD . '</font></a> ' . _MD_DOWNLOADCAT . '</font></center>';

        echo '<br><br><br><br>';

        CloseTable();
    }

    // Modify Download

    $result2 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . '');

    [$numrows2] = $xoopsDB->fetchRow($result2);

    if ($numrows2 > 0) {
        OpenTable();

        echo "<form method=get action=\"index.php\">\n";

        echo '<h4>' . _MD_MODDL . "</h4><br>\n";

        echo _MD_FILEID . "<input type=text name=lid size=12 maxlength=11>\n";

        echo "<input type=hidden name=fct value=gsdownloads>\n";

        echo "<input type=hidden name=op value=modDownload><br><br>\n";

        echo '<input type=submit value=' . _MD_MODIFY . "></form>\n";

        CloseTable();
    }

    xoops_cp_footer();
}
function catConfigMenu()
{
    global $xoopsDB, $myts, $eh, $mytree;

    // Add a New Main Category

    xoops_cp_header();

    OpenTable();

    echo "<form method=post action=index.php>\n";

    echo '<h4>' . _MD_ADDMAIN . '</h4><br>' . _MD_TITLEC . '<input type=text name=title size=30 maxlength=50><br>';

    echo _MD_IMGURL . '<br><input type="text" name="imgurl" size="100" maxlength="150" value="http://"><br><br>';

    echo "<input type=hidden name=cid value=0>\n";

    echo '<input type=hidden name=op value=addCat>';

    echo '<input type=submit value=' . _MD_ADD . '><br></form>';

    CloseTable();

    echo '<br>';

    // Add a New Sub-Category

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_cat') . '');

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        OpenTable();

        echo '<form method=post action=index.php>';

        echo '<h4>' . _MD_ADDSUB . '</h4><br>' . _MD_TITLEC . '<input type=text name=title size=30 maxlength=50>&nbsp;' . _MD_IN . '&nbsp;';

        $mytree->makeMySelBox('title', 'title');

        # echo "<br>"._MD_IMGURL."<br><input type=\"text\" name=\"imgurl\" size=\"100\" maxlength=\"150\">\n";

        echo '<input type=hidden name=op value=addCat><br><br>';

        echo '<input type=submit value=' . _MD_ADD . '><br></form>';

        CloseTable();

        echo '<br>';

        // Modify Category

        OpenTable();

        echo '<form method=post action=index.php><h4>' . _MD_MODCAT . '</h4><br>';

        echo _MD_CATEGORYC;

        $mytree->makeMySelBox('title', 'title');

        echo "<br><br>\n";

        echo "<input type=hidden name=op value=modCat>\n";

        echo '<input type=submit value=' . _MD_MODIFY . ">\n";

        echo '</form>';

        CloseTable();

        echo '<br>';
    }

    xoops_cp_footer();
}
function modDownload()
{
    global $xoopsDB, $_GET, $myts, $eh, $mytree;

    $lid = $_GET['lid'];

    xoops_cp_header();

    OpenTable();

    $result = $xoopsDB->query('SELECT cid, title, url, homepage, version, size, platform, price, logourl FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid") or $eh::show('0013');

    echo '<h4>' . _MD_MODDL . '</h4><br>';

    [$cid, $title, $url, $homepage, $version, $size, $platform, $price, $logourl] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $url = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5);

    $homepage = htmlspecialchars($homepage, ENT_QUOTES | ENT_HTML5);

    $version = htmlspecialchars($version, ENT_QUOTES | ENT_HTML5);

    $size = htmlspecialchars($size, ENT_QUOTES | ENT_HTML5);

    $platform = htmlspecialchars($platform, ENT_QUOTES | ENT_HTML5);

    $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

    $logourl = htmlspecialchars($logourl, ENT_QUOTES | ENT_HTML5);

    $result2 = $xoopsDB->query('SELECT description FROM ' . $xoopsDB->prefix('gsdownloads_text') . " WHERE lid=$lid");

    [$description] = $xoopsDB->fetchRow($result2);

    $description = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);

    $result3 = $xoopsDB->query('SELECT excerpt FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . " WHERE lid=$lid");

    [$excerpt] = $xoopsDB->fetchRow($result3);

    $excerpt = htmlspecialchars($excerpt, ENT_QUOTES | ENT_HTML5);

    echo '<table>';

    echo '<form method=post action=index.php>';

    echo '<tr><td>' . _MD_FILEID . "</td><td><b>$lid</b></td></tr>";

    echo '<tr><td>' . _MD_FILETITLE . "</td><td><input type=text name=title value=\"$title\" size=50 maxlength=100></input></td></tr>\n";

    echo '</td></tr><tr><td align="right" nowrap>' . _MD_DLURL . '</td><td>';

    echo "<input type=text name=url size=50 maxlength=100 value=\"$url\">";

    echo '</td></tr>';

    echo '<tr><td align="right" nowrap>' . _MD_CATEGORYC . '</td><td>';

    $mytree->makeMySelBox('title', 'title', $cid);

    echo "</td></tr><tr><td></td><td></td></tr>\n";

    echo '<tr><td align="right" nowrap>' . _MD_PAYPAL . "</td><td>\n";

    echo "<input type=\"text\" name=\"homepage\" size=\"50\" maxlength=\"100\" value=\"$homepage\"></td></tr>\n";

    echo '<tr><td align="right">' . _MD_VERSIONC . "</td><td>\n";

    echo "<input type=\"text\" name=\"version\" size=\"10\" maxlength=\"10\" value=\"$version\"></td></tr>\n";

    echo '<tr><td align="right">' . _MD_FILESIZEC . "</td><td>\n";

    echo "<input type=\"text\" name=\"size\" size=\"10\" maxlength=\"8\" value=\"$size\">" . _MD_BYTES . "</td></tr>\n";

    echo '<tr><td align="right">' . _MD_PLATFORMC . "</td><td>\n";

    echo "<input type=\"text\" name=\"platform\" size=\"45\" maxlength=\"50\" value=\"$platform\"></td></tr>\n";

    echo '<tr><td align="right">' . _MD_PRICEC . "</td><td>\n";

    echo "<input type=\"text\" name=\"price\" size=\"45\" maxlength=\"50\" value=\"$price\"></td></tr>\n";

    echo '<tr><td align="right" valign="top" nowrap>' . _MD_DESCRIPTIONC . "</td><td>\n";

    echo "<textarea name=description cols=\"60\" rows=\"5\">$description</textarea>\n";

    echo "</td></tr>\n";

    echo '<tr><td>' . _MD_SHOTIMAGE . "</td><td><input type=text name=logourl value=\"$logourl\" size=\"50\" maxlength=\"60\"></input></td></tr>\n";

    echo '<tr><td></td><td>';

    $directory = XOOPS_URL . '/modules/gsdownloads/images/shots/';

    printf(_MD_MUSTBEVALID, $directory);

    echo "</td></tr>\n";

    echo '</table>';

    echo "<br><br><input type=hidden name=lid value=$lid></input>\n";

    echo '<input type=hidden name=op value=modDownloadS><input type=submit value=' . _MD_SUBMIT . '>';

    echo "</form>\n";

    echo "<table><tr><td>\n";

    echo myTextForm('index.php?op=delDownload&lid=' . $lid, _MD_DELETE);

    echo "</td><td>\n";

    echo myTextForm('index.php?op=booksConfigMenu', _MD_CANCEL);

    echo "</td></tr></table>\n";

    echo '<HR>';

    //Vote Data

    $result5 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . '');

    [$totalvotes] = $xoopsDB->getRowsNum($result5);

    $totalrating = 0;

    while (list($rating5) = $xoopsDB->fetchRow($result5)) {
        $totalrating += $rating5;
    }

    echo "<table valign=top width=100%>\n";

    echo '<tr><td colspan=7><b>';

    //Show Reviews

    $result100 = $xoopsDB->query('SELECT reviewid, reviewuser, review, reviewhostname, reviewtimestamp FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid = $lid AND reviewuser != 0 ORDER BY reviewtimestamp DESC");

    $votes = $xoopsDB->getRowsNum($result100);

    echo '<tr><td colspan=7><br><br><b>';

    printf(_MD_REGUSERREVIEWS, $votes);

    echo "</b><br><br></td></tr>\n";

    if (0 == $votes) {
        echo '<tr><td align="center" colspan="7">' . _MD_NOREGREVIEWS . "<br></td></tr>\n";
    }

    $x = 0;

    $colorswitch = 'dddddd';

    while (list($reviewid, $reviewuser, $review, $reviewhostname, $reviewtimestamp) = $xoopsDB->fetchRow($result100)) {
        $formatted_date = formatTimestamp($reviewtimestamp);

        $review = $myts->displayTarea($review, 1);

        //Individual user information

        $result200 = $xoopsDB->query('SELECT review FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE reviewuser = $reviewuser");

        $reviewuname = XoopsUser::getUnameFromId($reviewuser);

        //echo "$reviewuser";

        echo "<tr bgcolor=\"$colorswitch\"><td colspan=5 align=center valign=top>" . _MD_USER . ": <b>$reviewuname</b> &nbsp;&nbsp;&nbsp;&nbsp;" . _MD_IP . ": <b>$reviewhostname</b> &nbsp;&nbsp;&nbsp;&nbsp; " . _MD_DATE . ": <b>$formatted_date </b></td><td valign=top align=right> <b>" . _MD_DELETE . '</td><td align=center>';

        echo myTextForm("index.php?op=delReview&lid=$lid&rid=$reviewid&user=$reviewuser", 'X');

        echo "</b></td></tr>\n";

        if ('dddddd' == $colorswitch) {
            $colorswitch = 'ffffff';
        } else {
            $colorswitch = 'dddddd';
        }

        echo "<td bgcolor=\"$colorswitch\" colspan=7>$review</td>";

        echo "</tr>\n";

        if ('dddddd' == $colorswitch) {
            $colorswitch = 'ffffff';
        } else {
            $colorswitch = 'dddddd';
        }

        $x++;
    }

    echo "<tr><td colspan=\"6\">&nbsp;<br></td></tr>\n";

    echo "</table>\n";

    CloseTable();

    xoops_cp_footer();
}
function delReview()
{
    global $xoopsDB, $_GET, $eh;

    $rid = $_GET['rid'];

    $lid = $_GET['lid'];

    $user = $_GET['user'];

    //echo "$user";

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE reviewid=$rid";

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . " WHERE ratingid=$rid";

    $xoopsDB->query($query) or $eh::show('0013');

    updaterating($lid);

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . " WHERE lid=$lid AND ratinguser=$user";

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_REVIEWDELETED);
}
function delVote()
{
    global $xoopsDB, $_GET, $eh;

    $rid = $_GET['rid'];

    $lid = $_GET['lid'];

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . " WHERE ratingid=$rid";

    $xoopsDB->query($query) or $eh::show('0013');

    updaterating($lid);

    redirect_header('index.php', 1, _MD_VOTEDELETED);
}
function listBrokenDownloads()
{
    global $xoopsDB, $eh;

    $result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_broken') . ' ORDER BY reportid');

    $totalbrokendownloads = $xoopsDB->getRowsNum($result);

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_BROKENREPORTS . " ($totalbrokendownloads)</h4><br>";

    if (0 == $totalbrokendownloads) {
        echo _MD_NOBROKEN;
    } else {
        echo '<center>' . _MD_IGNOREDESC . '<br>' . _MD_DELETEDESC . '</center><br><br><br>';

        $colorswitch = '#dddddd';

        echo '<table align="center" width="90%">';

        echo '
<tr>
<td><b>' . _MD_FILETITLE . '</b></td>
<td><b>' . _MD_REPORTER . '</b></td>
<td><b>' . _MD_FILESUBMITTER . '</b></td>
<td><b>' . _MD_IGNORE . '</b></td>
<td><b>' . _MD_DELETE . '</b></td>
</tr>';

        while (list($reportid, $lid, $sender, $ip) = $xoopsDB->fetchRow($result)) {
            $result2 = $xoopsDB->query('SELECT title, url, submitter FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

            if (0 != $sender) {
                $result3 = $xoopsDB->query('SELECT uname, email FROM ' . $xoopsDB->prefix('users') . ' WHERE uid=' . $sender . '');

                [$sendername, $email] = $xoopsDB->fetchRow($result3);
            }

            [$title, $url, $owner] = $xoopsDB->fetchRow($result2);

            $result4 = $xoopsDB->query('SELECT uname, email FROM ' . $xoopsDB->prefix('users') . ' WHERE uid=' . $owner . '');

            [$ownername, $owneremail] = $xoopsDB->fetchRow($result4);

            echo "<tr><td bgcolor=$colorswitch><a href=$url>$title</a></td>";

            if ('' == $email) {
                echo "<td bgcolor=$colorswitch>$sendername ($ip)";
            } else {
                echo "<td bgcolor=$colorswitch><a href=mailto:$email>$sendername</a> ($ip)";
            }

            echo '</td>';

            if ('' == $owneremail) {
                echo "<td bgcolor=$colorswitch>$ownername";
            } else {
                echo "<td bgcolor=$colorswitch><a href=mailto:$owneremail>$ownername</a>";
            }

            echo "</td><td bgcolor='$colorswitch' align='center'>\n";

            echo myTextForm("index.php?op=ignoreBrokenDownloads&lid=$lid", 'X');

            echo '</td>';

            echo "<td bgcolor='$colorswitch' align='center'>\n";

            echo myTextForm("index.php?op=delBrokenDownloads&lid=$lid", 'X');

            echo "</td></tr>\n";

            if ('#dddddd' == $colorswitch) {
                $colorswitch = '#ffffff';
            } else {
                $colorswitch = '#dddddd';
            }
        }

        echo '</table>';
    }

    CloseTable();

    xoops_cp_footer();
}
function delBrokenDownloads()
{
    global $xoopsDB, $_GET, $eh;

    $lid = $_GET['lid'];

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_broken') . " WHERE lid=$lid";

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid";

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_FILEDELETED);
}
function ignoreBrokenDownloads()
{
    global $xoopsDB, $_GET, $eh;

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_broken') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_BROKENDELETED);
}
function listModReq()
{
    global $xoopsDB, $myts, $eh, $mytree, $gsdownloads_useshots, $gsdownloads_shotwidth;

    $result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('gsdownloads_mod') . ' ORDER BY requestid');

    $totalmodrequests = $xoopsDB->getRowsNum($result);

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_USERMODREQ . " ($totalmodrequests)</h4><br>";

    if ($totalmodrequests > 0) {
        echo '<table width=95%><tr><td>';

        while (list($requestid, $lid, $cid, $title, $url, $homepage, $version, $size, $platform, $price, $logourl, $description, $excerpt, $modifysubmitter) = $xoopsDB->fetchRow($result)) {
            $result2 = $xoopsDB->query('SELECT cid, title, url, homepage, version, size, platform, price, logourl, submitter FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid");

            [$origcid, $origtitle, $origurl, $orighomepage, $origversion, $origsize, $origplatform, $origprice, $origlogourl, $owner] = $xoopsDB->fetchRow($result2);

            $result2 = $xoopsDB->query('SELECT description FROM ' . $xoopsDB->prefix('gsdownloads_text') . " WHERE lid=$lid");

            $result3 = $xoopsDB->query('SELECT excerpt FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . " WHERE lid=$lid");

            [$origdescription] = $xoopsDB->fetchRow($result2);

            [$origexcerpt] = $xoopsDB->fetchRow($result3);

            $result7 = $xoopsDB->query('SELECT uname, email FROM ' . $xoopsDB->prefix('users') . " WHERE uid=$modifysubmitter");

            $result8 = $xoopsDB->query('SELECT uname, email FROM ' . $xoopsDB->prefix('users') . " WHERE uid=$owner");

            $cidtitle = $mytree->getPathFromId($cid, 'title');

            $origcidtitle = $mytree->getPathFromId($origcid, 'title');

            [$submittername, $submitteremail] = $xoopsDB->fetchRow($result7);

            [$ownername, $owneremail] = $xoopsDB->fetchRow($result8);

            $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $url = htmlspecialchars($url, ENT_QUOTES | ENT_HTML5);

            $homepage = htmlspecialchars($homepage, ENT_QUOTES | ENT_HTML5);

            $version = htmlspecialchars($version, ENT_QUOTES | ENT_HTML5);

            $size = htmlspecialchars($size, ENT_QUOTES | ENT_HTML5);

            $platform = htmlspecialchars($platform, ENT_QUOTES | ENT_HTML5);

            $price = htmlspecialchars($price, ENT_QUOTES | ENT_HTML5);

            // use original image file to prevent users from changing screen shots file

            $origlogourl = htmlspecialchars($origlogourl, ENT_QUOTES | ENT_HTML5);

            $logourl = $origlogourl;

            $description = $myts->displayTarea($description);

            $excerpt = $myts->displayTarea($excerpt);

            $origurl = htmlspecialchars($origurl, ENT_QUOTES | ENT_HTML5);

            $orighomepage = htmlspecialchars($orighomepage, ENT_QUOTES | ENT_HTML5);

            $origversion = htmlspecialchars($origversion, ENT_QUOTES | ENT_HTML5);

            $origsize = htmlspecialchars($origsize, ENT_QUOTES | ENT_HTML5);

            $origplatform = htmlspecialchars($origplatform, ENT_QUOTES | ENT_HTML5);

            $origprice = htmlspecialchars($origprice, ENT_QUOTES | ENT_HTML5);

            $origdescription = $myts->displayTarea($origdescription);

            $origexcerpt = $myts->displayTarea($origexcerpt);

            if ('' == $ownerid) {
                $ownername = 'administration';
            }

            echo '<table border=1 bordercolor=black cellpadding=5 cellspacing=0 align=center width=450><tr><td>
<table width=100% bgcolor=dddddd>
<tr>
<td valign=top width=45%><b>' . _MD_ORIGINAL . '</b></td>
<td rowspan=14 valign=top align=left><br>' . _MD_DESCRIPTIONC . "<br>$origdescription</td>
<td rowspan=14 valign=top align=left><br>" . _MD_EXCERPTC . "<br>$origexcerpt</td>
</tr>
<tr><td valign=top width=45%><small>" . _MD_FILETITLE . ' ' . $origtitle . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_DLURL . ' ' . $origurl . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_CATEGORYC . ' ' . $origcidtitle . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_PAYPAL . ' ' . $orighomepage . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_PRICEC . ' ' . $origprice . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_SHOTIMAGE . '</small> ';

            if ($gsdownloads_useshots && !empty($origlogourl)) {
                echo '<img src="' . XOOPS_URL . '/modules/gsdownloads/images/shots/' . $origlogourl . '" width="' . $gsdownloads_shotwidth . '">';
            } else {
                echo '&nbsp;';
            }

            echo '</td></tr>
</table></td></tr><tr><td>
<table width=100%>
<tr>
<td valign=top width=45%><b>' . _MD_PROPOSED . '</b></td>
<td rowspan=14 valign=top align=left><br>' . _MD_DESCRIPTIONC . "<br>$description</td>
<td rowspan=14 valign=top align=left><br>" . _MD_EXCERPTC . "<br>$excerpt</td>
</tr>
<tr><td valign=top width=45%><small>" . _MD_FILETITLE . ' ' . $title . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_DLURL . ' ' . $url . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_CATEGORYC . ' ' . $cidtitle . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_PAYPAL . ' ' . $homepage . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_PRICEC . ' ' . $price . '</small></td></tr>
<tr><td valign=top width=45%><small>' . _MD_SHOTIMAGE . '</small> ';

            if ($gsdownloads_useshots && !empty($logourl)) {
                echo '<img src="' . XOOPS_URL . '/modules/gsdownloads/images/shots/' . $logourl . '" width="' . $gsdownloads_shotwidth . '">';
            } else {
                echo '&nbsp;';
            }

            echo '</td></tr>
</table></td></tr></table>
<table align=center width=450>
<tr>';

            if ('' == $submitteremail) {
                echo '<td align=left><small>' . _MD_SUBMITTER . " $submittername</small></td>";
            } else {
                echo '<td align=left><small>' . _MD_SUBMITTER . " <a href=mailto:$submitteremail>$submittername</a></small></td>";
            }

            if ('' == $owneremail) {
                echo '<td align=center><small>' . _MD_OWNER . " $ownername</small></td>";
            } else {
                echo '<td align=center><small>' . _MD_OWNER . " <a href=mailto:$owneremail>$ownername</a></small></td>";
            }

            echo "<td align=right><small>\n";

            echo "<table><tr><td>\n";

            echo myTextForm("index.php?op=changeModReq&requestid=$requestid", _MD_APPROVE);

            echo "</td><td>\n";

            echo myTextForm("index.php?op=ignoreModReq&requestid=$requestid", _MD_IGNORE);

            echo "</td></tr></table>\n";

            echo "</small></td></tr>\n";

            echo '</table><br><br>';
        }

        echo '</td></tr></table>';
    } else {
        echo _MD_NOMODREQ;
    }

    CloseTable();

    xoops_cp_footer();
}
function changeModReq()
{
    global $xoopsDB, $_GET, $eh, $myts;

    $requestid = $_GET['requestid'];

    $query = 'SELECT lid, cid, title, url, homepage, version, size, platform, price, logourl, description FROM ' . $xoopsDB->prefix('gsdownloads_mod') . " WHERE requestid=$requestid";

    $result = $xoopsDB->query($query);

    while (list($lid, $cid, $title, $url, $homepage, $version, $size, $platform, $price, $logourl, $description) = $xoopsDB->fetchRow($result)) {
        if (get_magic_quotes_runtime()) {
            $title = stripslashes($title);

            $url = stripslashes($url);

            $homepage = stripslashes($homepage);

            $logourl = stripslashes($logourl);

            $description = stripslashes($description);
        }

        $title = addslashes($title);

        $url = addslashes($url);

        $homepage = addslashes($homepage);

        $logourl = addslashes($logourl);

        $description = addslashes($description);

        $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_downloads') . " SET cid=$cid,title='$title',url='$url',homepage='$homepage',version='$version' ,size=$size ,platform='$platform' , price='$price', logourl='$logourl', status=2, date=" . time() . " WHERE lid=$lid") or $eh::show('0013');

        $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_text') . " SET description='$description' WHERE lid=$lid") or $eh::show('0013');

        $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_mod') . " WHERE requestid=$requestid") or $eh::show('0013');
    }

    redirect_header('index.php', 1, _MD_DBUPDATED);
}
function ignoreModReq()
{
    global $xoopsDB, $_GET, $eh;

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_mod') . ' WHERE requestid=' . $_GET['requestid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_MODREQDELETED);
}
function modDownloadS()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $cid = $_POST['cid'];

    if (($_POST['url']) || ('' != $_POST['url'])) {
        $url = $myts->addSlashes($_POST['url']);
    }

    $logourl = $myts->addSlashes($_POST['logourl']);

    $title = $myts->addSlashes($_POST['title']);

    $homepage = $myts->addSlashes($_POST['homepage']);

    $version = $myts->addSlashes($_POST['version']);

    $size = $myts->addSlashes($_POST['size']);

    $platform = $myts->addSlashes($_POST['platform']);

    $price = $myts->addSlashes($_POST['price']);

    $description = $myts->addSlashes($_POST['description']);

    $excerpt = $myts->addSlashes($_POST['excerpt']);

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_downloads') . " SET cid=$cid, title='$title', url='$url', homepage='$homepage', version='$version', size=$size, platform='$platform', price='$price', logourl='$logourl', status=2, date=" . time() . ' WHERE lid=' . $_POST['lid'] . '') or $eh::show('0013');

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_text') . " SET description='$description' WHERE lid=" . $_POST['lid'] . '') or $eh::show('0013');

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_excerpt') . " SET excerpt='$excerpt' WHERE lid=" . $_POST['lid'] . '') or $eh::show('0013');

    redirect_header('index.php', 1, _MD_DBUPDATED);
}
function delDownload()
{
    global $xoopsDB, $_GET, $eh;

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_text') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_FILEDELETED);
}
function modCat()
{
    global $xoopsDB, $_POST, $myts, $eh, $mytree;

    $cid = $_POST['cid'];

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_MODCAT . '</h4><br>';

    $result = $xoopsDB->query('SELECT pid, title, imgurl FROM ' . $xoopsDB->prefix('gsdownloads_cat') . " WHERE cid=$cid");

    [$pid, $title, $imgurl] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $imgurl = htmlspecialchars($imgurl, ENT_QUOTES | ENT_HTML5);

    echo '<form action=index.php method=post>' . _MD_TITLEC . "<input type=text name=title value=\"$title\" size=51 maxlength=50><br><br>" . _MD_IMGURLMAIN . "<br><input type=text name=imgurl value=\"$imgurl\" size=100 maxlength=150><br>
<br>" . _MD_PARENT . '&nbsp;';

    $mytree->makeMySelBox('title', 'title', $pid, 1, 'pid');

    echo "<input type='hidden' name='cid' value='$cid'>
<input type=hidden name=op value=modCatS><br>
<input type=submit value=\"" . _MD_SAVE . '">
<input type=button value=' . _MD_DELETE . " onClick=\"location='index.php?pid=$pid&cid=$cid&op=delCat'\">";

    echo '&nbsp;<input type=button value=' . _MD_CANCEL . ' onclick="javascript:history.go(-1)">';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}
function modCatS()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $cid = $_POST['cid'];

    $sid = $_POST['pid'];

    $title = $myts->addSlashes($_POST['title']);

    if (($_POST['imgurl']) || ('' != $_POST['imgurl'])) {
        $imgurl = $myts->addSlashes($_POST['imgurl']);
    }

    $xoopsDB->query('UPDATE ' . $xoopsDB->prefix('gsdownloads_cat') . " SET title='$title', imgurl='$imgurl', pid='$sid' where cid=$cid") or $eh::show('0013');

    redirect_header('index.php', 1, _MD_DBUPDATED);
}
function delCat()
{
    global $xoopsDB, $_GET, $eh, $mytree;

    $cid = $_GET['cid'];

    if ($_GET['ok']) {
        $ok = $_GET['ok'];
    }

    if (1 == $ok) {
        //get all subcategories under the specified category

        $arr = $mytree->getAllChildId($cid);

        for ($i = 0, $iMax = count($arr); $i < $iMax; $i++) {
            //get all downloads in each subcategory

            $result = $xoopsDB->query('SELECT lid FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE cid=' . $arr[$i] . '') or $eh::show('0013');

            //now for each download, delete the text data and vote data associated with the download

            while (list($lid) = $xoopsDB->fetchRow($result)) {
                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_text') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

                $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . ' WHERE lid=' . $lid . '') or $eh::show('0013');
            }

            //all downloads for each subcategory is deleted, now delete the subcategory data

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_cat') . ' WHERE cid=' . $arr[$i] . '') or $eh::show('0013');
        }

        //all subcategory and associated data are deleted, now delete category data and its associated data

        $result = $xoopsDB->query('SELECT lid FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE cid=' . $cid . '') or $eh::show('0013');

        while (list($lid) = $xoopsDB->fetchRow($result)) {
            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid") or $eh::show('0013');

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_text') . " WHERE lid=$lid") or $eh::show('0013');

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . " WHERE lid=$lid") or $eh::show('0013');

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . " WHERE lid=$lid") or $eh::show('0013');

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . " WHERE lid=$lid") or $eh::show('0013');

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . ' WHERE lid=' . $lid . '') or $eh::show('0013');

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_votecat') . ' WHERE lid=' . $lid . '') or $eh::show('0013');
        }

        $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('gsdownloads_cat') . " WHERE cid=$cid") or $eh::show('0013');

        redirect_header('index.php', 1, _MD_CATDELETED);

        exit();
    }

    xoops_cp_header();

    OpenTable();

    echo '<center>';

    echo '<h4><font color="#ff0000">';

    echo _MD_WARNING . '</font></h4><br>';

    echo "<table><tr><td>\n";

    echo myTextForm("index.php?op=delCat&cid=$cid&ok=1", _MD_YES);

    echo "</td><td>\n";

    echo myTextForm('index.php', _MD_NO);

    echo "</td></tr></table>\n";

    CloseTable();

    xoops_cp_footer();
}
function delNewDownload()
{
    global $xoopsDB, $_GET, $eh;

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_text') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_editorials') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_excerpt') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    $query = 'DELETE FROM ' . $xoopsDB->prefix('gsdownloads_reviews') . ' WHERE lid=' . $_GET['lid'] . '';

    $xoopsDB->query($query) or $eh::show('0013');

    redirect_header('index.php', 1, _MD_FILEDELETED);
}
function addCat()
{
    global $xoopsDB, $_POST, $myts, $eh;

    $pid = $_POST['cid'];

    $title = $_POST['title'];

    if (($_POST['imgurl']) || ('' != $_POST['imgurl'])) {
        $imgurl = $myts->addSlashes($_POST['imgurl']);
    }

    $title = $myts->addSlashes($title);

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_cat') . '_cid_seq');

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_cat') . " (cid, pid, title, imgurl) VALUES ($newid, $pid, '$title', '$imgurl')") or $eh::show('0013');

    redirect_header('index.php', 1, _MD_NEWCATADDED);
}
function addDownload()
{
    global $xoopsDB, $xoopsUser, $_POST, $myts, $eh;

    if (($_POST['url']) || ('' != $_POST['url'])) {
        $url = $myts->addSlashes($_POST['url']);
    }

    $logourl = $myts->addSlashes($_POST['logourl']);

    $title = $myts->addSlashes($_POST['title']);

    $homepage = $myts->addSlashes($_POST['homepage']);

    $version = $myts->addSlashes($_POST['version']);

    $size = $myts->addSlashes($_POST['size']);

    $platform = $myts->addSlashes($_POST['platform']);

    $price = $myts->addSlashes($_POST['price']);

    $description = $myts->addSlashes($_POST['description']);

    $excerpt = $myts->addSlashes($_POST['excerpt']);

    $submitter = $xoopsUser->uid();

    $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE url='$url'");

    [$numrows] = $xoopsDB->fetchRow($result);

    $error = 0;

    $errormsg = '';

    if ($numrows > 0) {
        $errormsg .= '<h4><font color="#ff0000">';

        $errormsg .= _MD_ERROREXIST . '</font></h4><br>';

        $error = 1;
    }

    // Check if Title exist

    if ('' == $title) {
        $errormsg .= '<h4><font color="#ff0000">';

        $errormsg .= _MD_ERRORTITLE . '</font></h4><br>';

        $error = 1;
    }

    if (empty($size) || !is_numeric($size)) {
        $size = 0;
    }

    // Check if Description exist

    if ('' == $description) {
        $errormsg .= '<h4><font color="#ff0000">';

        $errormsg .= _MD_ERRORDESC . '</font></h4><br>';

        $error = 1;
    }

    /* // Check if Excerpt exist
    if ($excerpt=="") {
    $errormsg .= "<h4><font color=\"#ff0000\">";
    $errormsg .= _MD_ERROREXCERPT."</font></h4><br>";
    $error =1;
    } */

    if (1 == $error) {
        xoops_cp_header();

        echo $errormsg;

        xoops_cp_footer();

        exit();
    }

    if (!empty($_POST['cid'])) {
        $cid = $_POST['cid'];
    } else {
        $cid = 0;
    }

    $newid = $xoopsDB->genId($xoopsDB->prefix('gsdownloads_downloads') . '_lid_seq');

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_downloads') . " (lid, cid, title, url, homepage, version, size, platform, price, logourl, submitter, status, date, hits, rating, votes, comments) VALUES ($newid, $cid, '$title', '$url', '$homepage', '$version', $size, '$platform', '$price', '$logourl', $submitter, 1, " . time() . ', 0, 0, 0, 0)') or $eh::show('0013');

    if (0 == $newid) {
        $newid = $xoopsDB->getInsertId();
    }

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_text') . " (lid, description) VALUES ($newid, '$description')") or $eh::show('0013');

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('gsdownloads_excerpt') . " (lid, excerpt) VALUES ($newid, '$excerpt')") or $eh::show('0013');

    redirect_header('index.php', 1, _MD_NEWDLADDED);
}
function gsdownloadsConfigAdmin()
{
    global $gsdownloads_perpage, $gsdownloads_reviewsperpage, $gsdownloads_popular, $gsdownloads_newdownloads, $gsdownloads_top, $gsdownloads_sresults, $gsdownloads_anonadddownloadlock, $gsdownloads_useshots, $gsdownloads_allow_bbcode, $gsdownloads_extensions, $gsdownloads_uploadextensions, $gsdownloads_allow_smilies, $gsdownloads_shotwidth, $gsdownloads_uphome, $gsdownloads_sizemax, $gsdownloads_totrate, $gsdownloads_catnum, $gsdownloads_maxrate, $gsdownloads_totname, $gsdownloads_features;

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_GENERALSET . '</h4><br>';

    echo '<form action="index.php" method="post">';

    echo '<table width=100% border=0>';

    echo ' <tr><td nowrap>
' . _MD_DLSPERPAGE . "</td><td width=100%>
<select name=xgsdownloads_perpage>
<option value=$gsdownloads_perpage selected>$gsdownloads_perpage</option>
<option value=10>10</option>
<option value=15>15</option>
<option value=20>20</option>
<option value=25>25</option>
<option value=30>30</option>
<option value=50>50</option>
</select>
</td></tr>";

    echo ' <tr><td nowrap>
' . _MD_DLSREVIEWSPERPAGE . "</td><td width=100%>
<select name=xgsdownloads_reviewsperpage>
<option value=$gsdownloads_perpage selected>$gsdownloads_reviewsperpage</option>
<option value=10>10</option>
<option value=15>15</option>
<option value=20>20</option>
<option value=25>25</option>
<option value=30>30</option>
<option value=50>50</option>
</select>
</td></tr>";

    echo '<tr><td nowrap>
' . _MD_HITSPOP . "</td><td>
<select name=xgsdownloads_popular>
<option value=$gsdownloads_popular selected>$gsdownloads_popular</option>
<option value=10>10</option>
<option value=20>20</option>
<option value=50>50</option>
<option value=100>100</option>
<option value=500>500</option>
<option value=1000>1000</option>
</select>
</td></tr>";

    echo '<tr><td nowrap>
' . _MD_DLSNEW . "</td><td>
<select name=xgsdownloads_newdownloads>
<option value=$gsdownloads_newdownloads selected>$gsdownloads_newdownloads</option>
<option value=10>10</option>
<option value=15>15</option>
<option value=20>20</option>
<option value=25>25</option>
<option value=30>30</option>
<option value=50>50</option>
</select>";

    echo '</td></tr>';

    echo '<tr><td nowrap>
' . _MD_EXTENSIONNUMBER . "</td><td>
<select name=xgsdownloads_extensions>
<option value=$gsdownloads_extensions selected>$gsdownloads_extensions</option>
<option value=1>1</option>
<option value=2>2</option>
<option value=3>3</option>
<option value=4>4</option>
<option value=5>5</option>
<option value=6>6</option>
</select>";

    echo '</td></tr>';

    echo '<tr><td nowrap>
' . _MD_CATNAME . "</td><td>
<select name=xgsdownloads_catnum>
<option value=$gsdownloads_catnum selected>$gsdownloads_catnum</option>
<option value=1>1</option>
<option value=2>2</option>
<option value=3>3</option>
<option value=4>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>
<option value=8>8</option>
<option value=9>9</option>
<option value=10>10</option>
</select>";

    echo '</td></tr>';

    echo '<tr><td nowrap>
' . _MD_MAXRATE . "</td><td>
<select name=xgsdownloads_maxrate>
<option value=$gsdownloads_maxrate selected>$gsdownloads_maxrate</option>
<option value=1>1</option>
<option value=2>2</option>
<option value=3>3</option>
<option value=4>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>
<option value=8>8</option>
<option value=9>9</option>
<option value=10>10</option>
<option value=11>11</option>
<option value=12>12</option>
<option value=13>13</option>
<option value=14>14</option>
<option value=15>15</option>
<option value=16>16</option>
<option value=17>17</option>
<option value=18>18</option>
<option value=19>19</option>
<option value=20>20</option>
</select>";

    echo '</td></tr>';

    echo '<tr><td nowrap>
' . _MD_TOTRATE . "</td><td>
<select name=xgsdownloads_totrate>
<option value=$gsdownloads_totrate selected>$gsdownloads_totrate</option>
<option value=1>1</option>
<option value=2>2</option>
<option value=3>3</option>
<option value=4>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>
<option value=8>8</option>
<option value=9>9</option>
<option value=10>10</option>
<option value=11>11</option>
<option value=12>12</option>
<option value=13>13</option>
<option value=14>14</option>
<option value=15>15</option>
<option value=16>16</option>
<option value=17>17</option>
<option value=18>18</option>
<option value=19>19</option>
<option value=20>20</option>
</select>";

    echo '</td></tr>';

    echo '<tr><td nowrap>' . _MD_SIZEMAX . ' </td><td>';

    echo "<INPUT TYPE=\"text\" size=\"85\" NAME=\"xgsdownloads_sizemax\" VALUE=\"$gsdownloads_sizemax\"></INPUT>";

    echo '</td></tr>';

    echo '<tr><td nowrap>' . _MD_TOTNAME . ' </td><td>';

    echo "<INPUT TYPE=\"text\" size=\"85\" NAME=\"xgsdownloads_totname\" VALUE=\"$gsdownloads_totname\"></INPUT>";

    echo '</td></tr>';

    echo '<tr><td nowrap>' . _MD_FEATURES . ' </td><td>';

    echo "<INPUT TYPE=\"text\" size=\"85\" NAME=\"xgsdownloads_features\" VALUE=\"$gsdownloads_features\"></INPUT>";

    echo '</td></tr>';

    /* echo "<tr><td nowrap>
    "._MD_UPLOADEXTENSIONNUMBER."</td><td>
    <select name=xgsdownloads_uploadextensions>
    <option value=$gsdownloads_uploadextensions selected>$gsdownloads_uploadextensions</option>
    <option value=1>1</option>
    <option value=2>2</option>
    <option value=3>3</option>
    <option value=4>4</option>
    <option value=5>5</option>
    <option value=6>6</option>
    </select>";
    echo "</td></tr>"; */

    echo '<tr><td nowrap>' . _MD_UPHOME . ' </td><td>';

    echo "<INPUT TYPE=\"text\" size=\"85\" NAME=\"xgsdownloads_uphome\" VALUE=\"$gsdownloads_uphome\"></INPUT>";

    echo '</td></tr>';

    echo '<tr><td nowrap>' . _MD_USESHOTS . ' </td><td>';

    if (1 == $gsdownloads_useshots) {
        echo '<INPUT TYPE="RADIO" NAME="xgsdownloads_useshots" VALUE="1" CHECKED>&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

        echo '<INPUT TYPE="RADIO" NAME="xgsdownloads_useshots" VALUE="0" >&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
    } else {
        echo '<INPUT TYPE="RADIO" NAME="xgsdownloads_useshots" VALUE="1">&nbsp;' . _MD_YES . '&nbsp;</INPUT>';

        echo '<INPUT TYPE="RADIO" NAME="xgsdownloads_useshots" VALUE="0" CHECKED>&nbsp;' . _MD_NO . '&nbsp;</INPUT>';
    }

    echo '</td></tr>';

    echo '<tr><td nowrap>' . _MD_IMGWIDTH . ' </td><td>';

    if ('' != $gsdownloads_shotwidth) {
        echo "<INPUT TYPE=\"text\" size=\"10\" NAME=\"xgsdownloads_shotwidth\" VALUE=\"$gsdownloads_shotwidth\"></INPUT>";
    } else {
        echo '<INPUT TYPE="text" size="10" NAME="xgsdownloads_shotwidth" VALUE="140"></INPUT>';
    }

    echo '</td></tr>';

    echo '<tr><td>&nbsp;</td></tr>';

    echo '</table>';

    echo '<input type="hidden" name="op" value="gsdownloadsConfigChange">';

    echo '<input type="submit" value="' . _MD_SAVE . '">';

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}
function gsdownloadsConfigChange()
{
    global $_POST;

    $xgsdownloads_popular = $_POST['xgsdownloads_popular'];

    $xgsdownloads_newdownloads = $_POST['xgsdownloads_newdownloads'];

    $xgsdownloads_perpage = $_POST['xgsdownloads_perpage'];

    $xgsdownloads_reviewsperpage = $_POST['xgsdownloads_reviewsperpage'];

    $xgsdownloads_extensions = $_POST['xgsdownloads_extensions'];

    $xgsdownloads_sizemax = $_POST['xgsdownloads_sizemax'];

    $xgsdownloads_catnum = $_POST['xgsdownloads_catnum'];

    $xgsdownloads_maxrate = $_POST['xgsdownloads_maxrate'];

    $xgsdownloads_totrate = $_POST['xgsdownloads_totrate'];

    $xgsdownloads_totname = $_POST['xgsdownloads_totname'];

    $xgsdownloads_features = $_POST['xgsdownloads_features'];

    // $xgsdownloads_uploadextensions = $_POST['xgsdownloads_uploadextensions'];

    $xgsdownloads_useshots = $_POST['xgsdownloads_useshots'];

    $xgsdownloads_shotwidth = $_POST['xgsdownloads_shotwidth'];

    $xgsdownloads_uphome = $_POST['xgsdownloads_uphome'];

    $filename = XOOPS_ROOT_PATH . '/modules/gsdownloads/cache/config.php';

    $file = fopen($filename, 'wb');

    $content = '';

    $content .= "<?php\n";

    $content .= "\n";

    $content .= "###############################################################################\n";

    $content .= "# gsdownloads v0.3.0  #\n";

    $content .= "#   #\n";

    $content .= "# \$gsdownloads_popular: The number of hits required for a download to be a popular site. Default = 20 #\n";

    $content .= "# \$gsdownloads_newdownloads: The number of downloads that appear on the front page as latest listings. Default = 10 #\n";

    $content .= "# \$gsdownloads_perpage: The number of downloads that appear for each page. Default = 10 #\n";

    $content .= "# \$gsdownloads_useshots: Use screenshots? Default = 1 (Yes) #\n";

    $content .= "# \$gsdownloads_shotwidth: Screenshot Image Width (Default = 140) #\n";

    $content .= "# \$gsdownloads_extensions: Number of extensions (Default = 1) #\n";

    $content .= "###############################################################################\n";

    $content .= "\n";

    $content .= "\$gsdownloads_popular = $xgsdownloads_popular;\n";

    $content .= "\$gsdownloads_newdownloads = $xgsdownloads_newdownloads;\n";

    $content .= "\$gsdownloads_perpage = $xgsdownloads_perpage;\n";

    $content .= "\$gsdownloads_reviewsperpage = $xgsdownloads_reviewsperpage;\n";

    $content .= "\$gsdownloads_extensions = $xgsdownloads_extensions;\n";

    $content .= "\$gsdownloads_sizemax = $xgsdownloads_sizemax;\n";

    $content .= "\$gsdownloads_catnum = $xgsdownloads_catnum;\n";

    $content .= "\$gsdownloads_maxrate = $xgsdownloads_maxrate;\n";

    $content .= "\$gsdownloads_totrate = $xgsdownloads_totrate;\n";

    $content .= "\$gsdownloads_totname = \"$xgsdownloads_totname\";\n";

    $content .= "\$gsdownloads_features = \"$xgsdownloads_features\";\n";

    // $content .= "\$gsdownloads_uploadextensions = $xgsdownloads_uploadextensions;\n";

    $content .= "\$gsdownloads_useshots = $xgsdownloads_useshots;\n";

    $content .= "\$gsdownloads_shotwidth = $xgsdownloads_shotwidth;\n";

    $content .= "\$gsdownloads_uphome = \"$xgsdownloads_uphome\";\n";

    $content .= "\n";

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    redirect_header('index.php', 1, _MD_CONFUPDATED);
}
########################ExtensionAdmin############################################
function gsdownloadsExtensions()
{
    global $gsdownloads_extensions, $gsdownloads_extitle, $gsdownloads_exname, $gsdownloads_eximage, $gsdownloads_exdlimage, $gsdownloads_exdlbuyimage;

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_MANAGEEXTENSIONS . '</h4><br>';

    echo '' . _MD_EXTENSIONHERE . '<br>';

    echo '' . _MD_NOEXTENSIONS1 . '<a href="index.php?op=gsdownloadsConfigAdmin">' . _MD_NOEXTENSIONS2 . '</a> ' . _MD_NOEXTENSIONS3 . '<br>';

    echo '' . _MD_EXTENSIONORDER . '<br>';

    echo '<form action="index.php" method="post">';

    echo '<table width=50% border=0>';

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        echo '<tr><td nowrap align=right><b>' . _MD_EXTENSIONTITLE . ':' . $x . '</b> </td><td>';

        echo "<INPUT TYPE=\"text\" size=\"25\" NAME='xgsdownloads_extitle" . $x . "' VALUE=\"$gsdownloads_extitle[$x]\"></INPUT>";

        echo '</td></tr>';

        echo '<tr><td nowrap align=right>' . _MD_EXTENSIONNAME . ' </td><td>';

        echo "<INPUT TYPE=\"text\" size=\"10\" NAME='xgsdownloads_exname" . $x . "' VALUE=\"$gsdownloads_exname[$x]\"></INPUT>";

        echo '</td></tr>';

        echo '<tr><td nowrap align=right>' . _MD_EXTENSIONIMAGE . ' </td><td>';

        echo "<INPUT TYPE=\"text\" size=\"85\" NAME='xgsdownloads_eximage" . $x . "' VALUE=\"$gsdownloads_eximage[$x]\"></INPUT>";

        echo '</td></tr>';

        echo '<tr><td nowrap align=right>' . _MD_EXTENSIONDLIMAGE . ' </td><td>';

        echo "<INPUT TYPE=\"text\" size=\"85\" NAME='xgsdownloads_exdlimage" . $x . "' VALUE=\"$gsdownloads_exdlimage[$x]\"></INPUT>";

        echo '</td></tr>';

        // echo "<tr><td nowrap align=right>" . _MD_EXTENSIONDLBUYIMAGE . " </td><td>";
// echo "<INPUT TYPE=\"text\" size=\"85\" NAME='xgsdownloads_exdlbuyimage".$x."' VALUE=\"$gsdownloads_exdlbuyimage[$x]\"></INPUT>";
// echo "</td></tr>";
    }

    echo '<tr><td>&nbsp;</td></tr>';

    echo '</table>';

    echo '<input type="hidden" name="op" value="gsdownloads_ExtensionChange">';

    echo '<input type="submit" value="' . _MD_SAVE . '">';

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}
function gsdownloads_ExtensionChange()
{
    global $_POST;

    global $gsdownloads_extensions;

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        $xgsdownloads_extitle[$x] = $_POST['xgsdownloads_extitle' . $x . ''];

        $xgsdownloads_exname[$x] = $_POST['xgsdownloads_exname' . $x . ''];

        $xgsdownloads_eximage[$x] = $_POST['xgsdownloads_eximage' . $x . ''];

        $xgsdownloads_exdlimage[$x] = $_POST['xgsdownloads_exdlimage' . $x . ''];

        $xgsdownloads_exdlbuyimage[$x] = $_POST['xgsdownloads_exdlbuyimage' . $x . ''];
    }

    $filename = XOOPS_ROOT_PATH . '/modules/gsdownloads/include/config.php';

    $file = fopen($filename, 'wb');

    $content = '';

    $content .= "<?php\n";

    $content .= "\n";

    $content .= "###############################################################################\n";

    $content .= "# gsdownloads v0.3.0  #\n";

    $content .= "#   #\n";

    $content .= "# Stores Extensions#\n";

    $content .= "###############################################################################\n";

    $content .= "\n";

    for ($x = 1; $x < $gsdownloads_extensions + 1; $x++) {
        $content .= "\$gsdownloads_extitle[$x] = \"$xgsdownloads_extitle[$x]\";\n";

        $content .= "\$gsdownloads_exname[$x] = \"$xgsdownloads_exname[$x]\";\n";

        $content .= "\$gsdownloads_eximage[$x] = \"$xgsdownloads_eximage[$x]\";\n";

        $content .= "\$gsdownloads_exdlimage[$x] = \"$xgsdownloads_exdlimage[$x]\";\n";

        // $content .= "\$gsdownloads_exdlbuyimage[$x] = \"$xgsdownloads_exdlbuyimage[$x]\";\n";

        $content .= "\n";
    }

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    redirect_header('index.php', 1, _MD_CONFUPDATED);
}
########################ExtensionAdmin############################################
function gsdownloadsUploadExtensions()
{
    global $gsdownloads_uploadextensions, $ext;

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_MANAGEUPLOADEXTENSIONS . '</h4><br>';

    echo '' . _MD_EXTENSIONHERE . '<br>';

    echo '' . _MD_NOEXTENSIONS1 . '<a href="index.php?op=gsdownloadsConfigAdmin">' . _MD_NOEXTENSIONS2 . '</a> ' . _MD_NOEXTENSIONS3 . '<br>';

    echo '<form action="index.php" method="post">';

    echo '<table width=25% border=0>';

    for ($x = 1; $x < $gsdownloads_uploadextensions + 1; $x++) {
        echo '<tr><td nowrap align=right>' . _MD_UPLOADEXTENSIONNAME . " <b>$x</b></td><td>";

        echo "<INPUT TYPE=\"text\" size=\"10\" NAME='xext" . $x . "' VALUE=\"$ext[$x]\"></INPUT>";
    }

    echo '<tr><td>&nbsp;</td></tr>';

    echo '</table>';

    echo '<input type="hidden" name="op" value="gsdownloads_UploadExtensionChange">';

    echo '<input type="submit" value="' . _MD_SAVE . '">';

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}
function gsdownloads_UploadExtensionChange()
{
    global $_POST;

    global $gsdownloads_uploadextensions;

    for ($x = 1; $x < $gsdownloads_uploadextensions + 1; $x++) {
        $xext[$x] = $_POST['xext' . $x . ''];
    }

    $filename = XOOPS_ROOT_PATH . '/modules/gsdownloads/ulconf/exten.php';

    $file = fopen($filename, 'wb');

    $content = '';

    $content .= "<?php\n";

    $content .= "\n";

    $content .= "###############################################################################\n";

    $content .= "# gsdownloads v0.3.0  #\n";

    $content .= "#   #\n";

    $content .= "# Stores upload extensions#\n";

    $content .= "###############################################################################\n";

    $content .= "\n";

    for ($x = 1; $x < $gsdownloads_uploadextensions + 1; $x++) {
        $content .= "\$ext[$x] = \"$xext[$x]\";\n";

        $content .= "\n";
    }

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    redirect_header('index.php', 1, _MD_CONFUPDATED);
}
########################ExtensionAdmin############################################
function gsdownloadsCat()
{
    global $gsdownloads_catname, $gsdownloads_catnum, $gsdownloads_catweight;

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _MD_MANAGECAT . '</h4><br>';

    echo '<b><font color=red>' . _MD_CATORDER . '</font></b><br><br>';

    echo '<b><font color=red>' . _MD_CATORDER1 . '</font></b><br><br>';

    echo '<b><font color=red>' . _MD_CATORDER2 . '</font></b><br><br>';

    echo '<b><font color=red>' . _MD_CATORDER3 . '</font></b><br><br>';

    // echo ""._MD_EXTENSIONHERE."<br>";

    // echo ""._MD_NOEXTENSIONS1."<a href=\"index.php?op=gsdownloadsConfigAdmin\">"._MD_NOEXTENSIONS2."</a> "._MD_NOEXTENSIONS3."<br>";

    echo '<form action="index.php" method="post">';

    echo '<table width=25% border=0>';

    echo '<tr><td></td><td></td><td align=center></td><td></td></tr>';

    for ($x = 1; $x < $gsdownloads_catnum + 1; $x++) {
        echo '<tr><td nowrap align=right>' . _MD_CATNAME . " <b>$x</b></td><td>";

        echo "<INPUT TYPE=\"text\" size=\"25\" NAME='xgsdownloads_catname" . $x . "' VALUE=\"$gsdownloads_catname[$x]\"></INPUT></td><td>";

        // echo "<INPUT TYPE=\"text\" size=\"10\" NAME='xgsdownloads_catweight".$x."' VALUE=\"$gsdownloads_catweight[$x]\"></INPUT></td><td align=center>";

        // echo myTextForm("index.php?op=delVote&lid=$lid&rid=$ratingid" , "X");

        echo '</td>';
    }

    echo '<tr><td>&nbsp;</td></tr>';

    echo '</table>';

    echo '<input type="hidden" name="op" value="gsdownloads_CatChange">';

    echo '<input type="submit" value="' . _MD_SAVE . '">';

    echo '&nbsp;<input type="button" value="' . _MD_CANCEL . '" onclick="javascript:history.go(-1)">';

    echo '</form>';

    /* echo "<table width=25% border=0>";
    echo "<tr><td></td><td align=center>Delete</td></tr>";
    for ($x=1;$x<$gsdownloads_catnum+1;$x++)
    {
    echo "<tr><td align=right>$gsdownloads_catname[$x]</td><td align=center>";
    echo myTextForm("index.php?op=delVote&lid=$lid&rid=$ratingid" , "X");
    echo "</td></tr>";
    }
    echo "</table>"; */

    CloseTable();

    xoops_cp_footer();
}
function gsdownloads_CatChange()
{
    global $_POST;

    global $gsdownloads_catnum;

    for ($x = 1; $x < $gsdownloads_catnum + 1; $x++) {
        $xgsdownloads_catname[$x] = $_POST['xgsdownloads_catname' . $x . ''];

        $xgsdownloads_catweight[$x] = $_POST['xgsdownloads_catweight' . $x . ''];
    }

    $filename = XOOPS_ROOT_PATH . '/modules/gsdownloads/catconfig.php';

    $file = fopen($filename, 'wb');

    $content = '';

    $content .= "<?php\n";

    $content .= "\n";

    $content .= "###############################################################################\n";

    $content .= "# gsdownloads v0.3.0  #\n";

    $content .= "#   #\n";

    $content .= "# Stores upload extensions#\n";

    $content .= "###############################################################################\n";

    $content .= "\n";

    for ($x = 1; $x < $gsdownloads_catnum + 1; $x++) {
        $content .= "\$gsdownloads_catname[$x] = \"$xgsdownloads_catname[$x]\";\n";

        $content .= "\$gsdownloads_catweight[$x] = \"$xgsdownloads_catweight[$x]\";\n";

        $content .= "\n";
    }

    $content .= "?>\n";

    fwrite($file, $content);

    fclose($file);

    redirect_header('index.php', 1, _MD_CONFUPDATED);
}
switch ($op) {
default:
gsdownloads();
break;
case 'delNewDownload':
delNewDownload();
break;
case 'catConfigMenu':
catConfigMenu();
break;
case 'gsdownloadsCat':
gsdownloadsCat();
break;
case 'gsdownloads_CatChange':
gsdownloads_CatChange();
break;
case 'gsdownloads_ExtensionChange':
gsdownloads_ExtensionChange();
break;
case 'gsdownloads_UploadExtensionChange':
gsdownloads_UploadExtensionChange();
break;
case 'approve':
approve();
break;
case 'addCat':
addCat();
break;
case 'addSubCat':
addSubCat();
break;
case 'addDownload':
addDownload();
break;
case 'listBrokenDownloads':
listBrokenDownloads();
break;
case 'delBrokenDownloads':
delBrokenDownloads();
break;
case 'ignoreBrokenDownloads':
ignoreBrokenDownloads();
break;
case 'listModReq':
listModReq();
break;
case 'changeModReq':
changeModReq();
break;
case 'ignoreModReq':
ignoreModReq();
break;
case 'delCat':
delCat();
break;
case 'modCat':
modCat();
break;
case 'modCatS':
modCatS();
break;
case 'modDownload':
modDownload();
break;
case 'modDownloadS':
modDownloadS();
break;
case 'delDownload':
delDownload();
break;
case 'delVote':
delVote();
break;
case 'delReview':
delReview();
break;
case 'delComment':
delComment($bid, $rid);
break;
case 'gsdownloadsConfigAdmin':
gsdownloadsConfigAdmin();
break;
case 'gsdownloadsConfigChange':
if (xoopsfwrite()) {
    gsdownloadsConfigChange();
}
break;
case 'booksConfigMenu':
booksConfigMenu();
break;
case 'listNewDownloads':
listNewDownloads();
break;
case 'gsdownloadsExtensions':
gsdownloadsExtensions();
break;
case 'gsdownloadsUploadExtensions':
gsdownloadsUploadExtensions();
break;
}
