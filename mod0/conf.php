<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/t3m/mod0/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']='txt3mM0';

	## WOP:[module][1][admin_only]: If the flag was set the value is "admin", otherwise "user,group"
$MCONF['access']='user,group';
$MCONF['script']='index.php';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:t3m/mod0/locallang_mod.xml';

$MLANG["default"]["labels"]["tablabel"] = "T3M Direct Mail";
$MLANG["default"]["labels"]["tabdescr"] = "TYPO3 Macher E-Mail Marketing Lösung, basierend auf Direct Mail";


?>