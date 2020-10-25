<?php

include '/cache/config.php';
$modversion['name'] = _MI_gsdownloads_NAME;
$modversion['version'] = 2.0;
$modversion['description'] = _MI_gsdownloads_DESC;
$modversion['credits'] = 'Modified by the GiantSpider (http://www.giantspider.biz)<br>
based on myDownloads module modified by the wanderer
<br>( http://www.mpn-tw.com/ ) <br>Based on MyLinks by Kazumi Ono<br>( http://www.mywebaddons.com/ )<br>The XOOPS Project';
$modversion['author'] = 'The GiantSpider<br>( http://www.giantspider.biz )';
$modversion['help'] = 'gsdownloads.html';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 1;
$modversion['image'] = 'images/mydl_slogo.gif';
$modversion['dirname'] = 'gsdownloads';
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'gsdownloads_cat';
$modversion['tables'][1] = 'gsdownloads_downloads';
$modversion['tables'][2] = 'gsdownloads_text';
$modversion['tables'][3] = 'gsdownloads_excerpt';
$modversion['tables'][4] = 'gsdownloads_votedata';
$modversion['tables'][5] = 'gsdownloads_votecat';
$modversion['tables'][6] = 'gsdownloads_reviews';
$modversion['tables'][7] = 'gsdownloads_editorials';
// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';
// Blocks
$modversion['blocks'][1]['file'] = 'gsdownloads_top.php';
$modversion['blocks'][1]['name'] = _MI_gsdownloads_BNAME1;
$modversion['blocks'][1]['description'] = 'Shows recently added donwload files';
$modversion['blocks'][1]['show_func'] = 'b_gsdownloads_top_show';
$modversion['blocks'][1]['edit_func'] = 'b_gsdownloads_top_edit';
$modversion['blocks'][1]['options'] = 'date|10';
$modversion['blocks'][2]['file'] = 'gsdownloads_top.php';
$modversion['blocks'][2]['name'] = _MI_gsdownloads_BNAME2;
$modversion['blocks'][2]['description'] = 'Shows most downloaded files';
$modversion['blocks'][2]['show_func'] = 'b_gsdownloads_top_show';
$modversion['blocks'][2]['edit_func'] = 'b_gsdownloads_top_edit';
$modversion['blocks'][2]['options'] = 'hits|10';
$modversion['blocks'][3]['file'] = 'waiting_downloads.php';
$modversion['blocks'][3]['name'] = _MI_gsdownloads_BNAME3;
$modversion['blocks'][3]['description'] = 'Shows books waiting for approval';
$modversion['blocks'][3]['show_func'] = 'b_downloads_waiting_show';
$modversion['blocks'][3]['edit_func'] = '';
$modversion['blocks'][3]['options'] = '';
// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_gsdownloads_SMNAME1;
$modversion['sub'][1]['url'] = 'topten.php?hit=1';
$modversion['sub'][2]['name'] = _MI_gsdownloads_SMNAME2;
$modversion['sub'][2]['url'] = 'topten.php?rate=1';
$modversion['sub'][3]['name'] = _MI_gsdownloads_SMNAME3;
$modversion['sub'][3]['url'] = 'submit.php';
$modversion['sub'][4]['name'] = _MI_gsdownloads_SMNAME4;
$modversion['sub'][4]['url'] = 'randomfile.php';
// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'gsdownloads_search';
