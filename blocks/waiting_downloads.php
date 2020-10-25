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
function b_downloads_waiting_show()
{
    global $xoopsDB, $xoopsUser;

    $block = [];

    $block['title'] = _MB_SYSTEM_gsdownloadsWDLS;

    $block['content'] = '';

    // gsdownloads waiting contents

    if (XoopsModule::moduleExists('gsdownloads')) {
        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . ' WHERE status=0');

        if ($result) {
            [$num] = $xoopsDB->fetchRow($result);

            $block['content'] .= "<strong><big>&middot;</big></strong>&nbsp;<a href='" . XOOPS_URL . "/modules/gsdownloads/admin/index.php?op=listNewDownloads'>" .
_MB_SYSTEM_gsdownloadsWDLS . "</a>: $num<br>\n";
        }

        /*$result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("gsdownloads_broken")."");
        if ( $result ) {
        list($totalbrokenfiles) = $xoopsDB->fetchRow($result);
        $block['content'] .= "<strong><big>&middot;</big></strong>&nbsp;<a href='".XOOPS_URL."/modules/gsdownloads/admin/index.php?op=listBrokenDownloads'>".
        _MB_SYSTEM_gsdownloadsBFLS."</a>: $totalbrokenfiles<br>\n";
        }
        $result = $xoopsDB->query("SELECT COUNT(*) FROM ".$xoopsDB->prefix("gsdownloads_mod")."");
        if ( $result ) {
        list($totalmodrequests) = $xoopsDB->fetchRow($result);
        $block['content'] .= "<strong><big>&middot;</big></strong>&nbsp;<a href='".XOOPS_URL."/modules/gsdownloads/admin/index.php?op=listModReq'>".
        _MB_SYSTEM_gsdownloadsMFLS."</a>: $totalmodrequests<br>\n";
        }*/
    }

    // end of gsdownloads

    return $block;
}
