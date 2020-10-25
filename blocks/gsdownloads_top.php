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
/******************************************************************************
 * Function: b_gsdownloads_top_show
 * Input : $options[0] = date for the most recent downloads
 * hits for the most popular downloads
 * $block['content'] = The optional above content
 * $options[1] = How many downloads are displayed
 * Output : Returns the most recent or most popular downloads
 *****************************************************************************
 * @param $options
 * @return array
 */
function b_gsdownloads_top_show($options)
{
    global $xoopsDB;

    $block = [];

    $myts = MyTextSanitizer::getInstance();

    //$order = date for most recent reviews

    //$order = hits for most popular reviews

    $result = $xoopsDB->query('SELECT lid, title, date, hits FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE status>0 ORDER BY ' . $options[0] . ' DESC', $options[1], 0);

    $block['content'] = '<small>';

    while ($myrow = $xoopsDB->fetchArray($result)) {
        $title = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);

        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($title) >= 19) {
                $title = mb_substr($title, 0, 18) . '...';
            }
        }

        $block['content'] .= '&nbsp;&nbsp;<strong><big>&middot;</big></strong>&nbsp;<a href="' . XOOPS_URL . '/modules/gsdownloads/singlefile.php?lid=' . $myrow['lid'] . "\">$title</a> ";

        if ('date' == $options[0]) {
            $block['content'] .= '(' . formatTimestamp($myrow['date'], 's') . ')<br>';

            $block['title'] = _MB_gsdownloads_TITLE1;
        } elseif ('hits' == $options[0]) {
            $block['content'] .= '(' . $myrow['hits'] . ')<br>';

            $block['title'] = _MB_gsdownloads_TITLE2;
        }
    }

    $block['content'] .= '</small>';

    return $block;
}
function b_gsdownloads_top_edit($options)
{
    $form = '' . _MB_gsdownloads_DISP . '&nbsp;';

    $form .= '<input type="hidden" name="options[]" value="';

    if ('date' == $options[0]) {
        $form .= 'date"';
    } else {
        $form .= 'hits"';
    }

    $form .= '>';

    $form .= '<input type="text" name="options[]" value="' . $options[1] . '">&nbsp;' . _MB_gsdownloads_FILES . '';

    return $form;
}
