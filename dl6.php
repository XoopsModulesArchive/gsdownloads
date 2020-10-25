<?php

include '../../mainfile.php';
include '/include/config.php';
$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
$lid = (int)$_GET['lid'];
$xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('gsdownloads_downloads') . " SET hits=hits+1 WHERE lid=$lid AND status>0");
$result = $xoopsDB->query('SELECT url FROM ' . $xoopsDB->prefix('gsdownloads_downloads') . " WHERE lid=$lid AND status>0");
[$url] = $xoopsDB->fetchRow($result);
header("Location: $url" . $gsdownloads_exname[6] . '');
echo '<html><head><meta http-equiv="Refresh" content="0; URL=' . htmlspecialchars($url, ENT_QUOTES | ENT_HTML5) . '"></meta></head><body></body></html>';
exit();
