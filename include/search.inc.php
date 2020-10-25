<?php

function gsdownloads_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;

    $sql = 'SELECT d.lid,d.title,d.submitter,d.date,t.description FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' d LEFT JOIN ' . $xoopsDB->prefix('gsdownloads_text') . ' t ON t.lid=d.lid WHERE status>0';

    if (0 != $userid) {
        $sql .= ' AND d.submitter=' . $userid . ' ';
    }

    // because count() returns 1 even if a supplied variable

    // is not an array, we must check if $querryarray is really an array

    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((d.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";

        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";

            $sql .= "(d.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
        }

        $sql .= ') ';
    }

    $sql .= 'ORDER BY d.date DESC';

    $result = $xoopsDB->query($sql, $limit, $offset);

    $ret = [];

    $i = 0;

    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = 'images/size.gif';

        $ret[$i]['link'] = 'singlefile.php?lid=' . $myrow['lid'] . '';

        $ret[$i]['title'] = $myrow['title'];

        $ret[$i]['time'] = $myrow['date'];

        $ret[$i]['uid'] = $myrow['submitter'];

        $i++;
    }

    return $ret;
}
