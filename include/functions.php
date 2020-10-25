<?php

function mainheader($mainlink = 1)
{
    echo '<p><div align="center">';

    echo '<a href="' . XOOPS_URL . '/modules/gsdownloads/index.php"><img src="' . XOOPS_URL . '/modules/gsdownloads/images/logo-en.gif" border="0" alt""></a>';

    echo '</p>';
}
function newdownloadgraphic($time, $status)
{
    $count = 7;

    $startdate = (time() - (86400 * $count));

    if ($startdate < $time) {
        if (1 == $status) {
            echo '&nbsp;<img src="' . XOOPS_URL . '/modules/gsdownloads/images/newred.gif" alt="' . _MD_NEWTHISWEEK . '">';
        } elseif (2 == $status) {
            echo '&nbsp;<img src="' . XOOPS_URL . '/modules/gsdownloads/images/update.gif" alt="' . _MD_UPTHISWEEK . '">';
        }
    }
}
function popgraphic($hits)
{
    global $gsdownloads_popular;

    if ($hits >= $gsdownloads_popular) {
        echo '&nbsp;<img src ="' . XOOPS_URL . '/modules/gsdownloads/images/pop.gif" alt="' . _MD_POPULAR . '">';
    }
}
//Reusable Link Sorting Functions
function convertorderbyin($orderby)
{
    if ('titleA' == $orderby) {
        $orderby = 'title ASC';
    }

    if ('dateA' == $orderby) {
        $orderby = 'date ASC';
    }

    if ('hitsA' == $orderby) {
        $orderby = 'hits ASC';
    }

    if ('ratingA' == $orderby) {
        $orderby = 'rating ASC';
    }

    if ('titleD' == $orderby) {
        $orderby = 'title DESC';
    }

    if ('dateD' == $orderby) {
        $orderby = 'date DESC';
    }

    if ('hitsD' == $orderby) {
        $orderby = 'hits DESC';
    }

    if ('ratingD' == $orderby) {
        $orderby = 'rating DESC';
    }

    return $orderby;
}
function convertorderbytrans($orderby)
{
    if ('hits ASC' == $orderby) {
        $orderbyTrans = _MD_POPULARITYLTOM;
    }

    if ('hits DESC' == $orderby) {
        $orderbyTrans = _MD_POPULARITYMTOL;
    }

    if ('title ASC' == $orderby) {
        $orderbyTrans = _MD_TITLEATOZ;
    }

    if ('title DESC' == $orderby) {
        $orderbyTrans = _MD_TITLEZTOA;
    }

    if ('date ASC' == $orderby) {
        $orderbyTrans = _MD_DATEOLD;
    }

    if ('date DESC' == $orderby) {
        $orderbyTrans = _MD_DATENEW;
    }

    if ('rating ASC' == $orderby) {
        $orderbyTrans = _MD_RATINGLTOH;
    }

    if ('rating DESC' == $orderby) {
        $orderbyTrans = _MD_RATINGHTOL;
    }

    return $orderbyTrans;
}
function convertorderbyout($orderby)
{
    if ('title ASC' == $orderby) {
        $orderby = 'titleA';
    }

    if ('date ASC' == $orderby) {
        $orderby = 'dateA';
    }

    if ('hits ASC' == $orderby) {
        $orderby = 'hitsA';
    }

    if ('rating ASC' == $orderby) {
        $orderby = 'ratingA';
    }

    if ('title DESC' == $orderby) {
        $orderby = 'titleD';
    }

    if ('date DESC' == $orderby) {
        $orderby = 'dateD';
    }

    if ('hits DESC' == $orderby) {
        $orderby = 'hitsD';
    }

    if ('rating DESC' == $orderby) {
        $orderby = 'ratingD';
    }

    return $orderby;
}
function PrettySize($size)
{
    $mb = 1024 * 1024;

    if ($size > $mb) {
        $mysize = sprintf('%01.2f', $size / $mb) . ' MB';
    } elseif ($size >= 1024) {
        $mysize = sprintf('%01.2f', $size / 1024) . ' KB';
    } else {
        $mysize = sprintf(_MD_NUMBYTES, $size);
    }

    return $mysize;
}
//updates rating data in itemtable for a given item
function updaterating($sel_id)
{
    global $xoopsDB;

    $query = 'select rating FROM ' . $xoopsDB->prefix('gsdownloads_votedata') . ' WHERE lid = ' . $sel_id . '';

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    $totalrating = 0;

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    $finalrating = $totalrating / $votesDB;

    $finalrating = number_format($finalrating, 4);

    $query = 'UPDATE ' . $xoopsDB->prefix('gsdownloads_downloads') . " SET rating=$finalrating, votes=$votesDB WHERE lid = $sel_id";

    $xoopsDB->query($query);
}
//returns the total number of items in items table that are accociated with a given table $table id
function getTotalItems($sel_id, $status = '')
{
    global $xoopsDB, $mytree;

    $count = 0;

    $arr = [];

    $query = 'select count(*) from ' . $xoopsDB->prefix('gsdownloads_downloads') . ' where cid=' . $sel_id . '';

    if ('' != $status) {
        $query .= " and status>=$status";
    }

    $result = $xoopsDB->query($query);

    [$thing] = $xoopsDB->fetchRow($result);

    $count = $thing;

    $arr = $mytree->getAllChildId($sel_id);

    $size = count($arr);

    for ($i = 0; $i < $size; $i++) {
        $query2 = 'select count(*) from ' . $xoopsDB->prefix('gsdownloads_downloads') . ' where cid=' . $arr[$i] . '';

        if ('' != $status) {
            $query2 .= " and status>=$status";
        }

        $result2 = $xoopsDB->query($query2);

        [$thing] = $xoopsDB->fetchRow($result2);

        $count += $thing;
    }

    return $count;
}
