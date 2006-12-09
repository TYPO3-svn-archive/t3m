#!/usr/bin/php
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Daniel Schledermann <daniel@typoconsult.dk>
*  Modified by Stefan Koch <t3m@stefkoch.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

// print("Tcdirectmail script sending at ".date("Y-m-d_H:i:s")."\n");

if (isset($_SERVER['argv'][1])) {
	if ($_SERVER['argv'][1] == '--test') { // ? (isset($_SERVER['argv'][1]) && ($_SERVER['argv'][1] == '--test'))
		print "Ok\n";
		exit(0);
	}
}

define("PATH_typo3conf", dirname(dirname(dirname(__FILE__)))."/");
define("PATH_site", dirname(PATH_typo3conf)."/");
define("PATH_typo3", PATH_site."typo3/");       // Typo-configuraton path
define("PATH_t3lib", PATH_site."t3lib/");
define('TYPO3_MODE','BE');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('max_execution_time',0);
define('TYPO3_cliMode', TRUE);
require_once(PATH_t3lib.'class.t3lib_div.php');
require_once(PATH_t3lib.'class.t3lib_extmgm.php');
require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(PATH_t3lib.'config_default.php');
require_once(PATH_t3lib.'stddb/tables.php');
include (PATH_t3lib.'stddb/load_ext_tables.php');
if (!defined ("TYPO3_db"))      die ("The configuration file was not included.");
require_once(PATH_t3lib.'class.t3lib_db.php');          // The database library

/* For debugging */
class tx_tcdirectmail_noisydb extends t3lib_db {
    function sql_query($sql) {
//	print ("$sql\n\n");
	return parent::sql_query($sql);
    }
}
$TYPO3_DB = t3lib_div::makeInstance('tx_tcdirectmail_noisydb');
$TYPO3_DB->sql_pconnect (TYPO3_db_host, TYPO3_db_username, TYPO3_db_password);
$TYPO3_DB->sql_select_db (TYPO3_db);

require_once(t3lib_extMgm::extPath('tcdirectmail')."class.tx_tcdirectmail_tools.php");



/***************** Send script ********************/

/* List pages NOT to send */
$rs = $TYPO3_DB->sql_query("SELECT pid FROM tx_tcdirectmail_lock
                               WHERE stoptime = 0");
$pids[] = -1;
while (list($pid) = $TYPO3_DB->sql_fetch_row($rs)) {
	$pids[] = $pid;
}
$pids = implode(',',$pids);

/* Get a ready-to-send page */
$rs = $TYPO3_DB->sql_query("SELECT *
                                FROM pages
                                WHERE tx_tcdirectmail_senttime <= UNIX_TIMESTAMP()
                                AND tx_tcdirectmail_senttime <> 0
                                AND doktype = 189
				AND uid NOT IN ($pids)
				AND NOT deleted
				AND NOT hidden
				LIMIT 1");


if ($page = $TYPO3_DB->sql_fetch_assoc($rs)) {
    /* Lock the page */
    $begintime = time();
    $TYPO3_DB->exec_INSERTquery('tx_tcdirectmail_lock', array('pid' => $page['uid'], 'begintime' => $begintime, 'stoptime' => 0));
    $lockid = $TYPO3_DB->sql_insert_id();

    tx_tcdirectmail_tools::mailForReal($page, $begintime);

    /* Unlock the page */
    tx_tcdirectmail_tools::setSceduleAfterSending ($page);
    $TYPO3_DB->exec_UPDATEquery('tx_tcdirectmail_lock', "uid = $lockid", array('stoptime' => time()));
}

/* Get all test pages */
$rs = $TYPO3_DB->sql_query("SELECT *
                                FROM pages
                                WHERE tx_tcdirectmail_dotestsend = 1
				AND NOT deleted
				AND NOT hidden
                                AND doktype = 189");

while ($page = $TYPO3_DB->sql_fetch_assoc($rs)) {
    tx_tcdirectmail_tools::mailForTest($page);
    $TYPO3_DB->sql_query("UPDATE pages SET tx_tcdirectmail_dotestsend = 0 WHERE uid = $page[uid]");
}

?>
