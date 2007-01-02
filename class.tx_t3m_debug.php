<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Stefan Koch <t3m@stefkoch.de>
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

require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_target_array.php');
require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_main.php');
require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_bounce.php');

/**
 * Debug class
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_debug {


	/**
	 * Returns some system infos
	 *
	 * @return	string		some interesting stuff
	 */
	function testSomeStuff()	{

// 		for ($i = 0; $i < 20; $i++) {
			$out[] = tx_t3m_stats::countEmailUsers(91);
// 		}

// 		$var = tx_t3m_main::getTimestampFromAge(36);
// 		$var2 = tx_t3m_main::getTimestampFromAge(37);
		//$GLOBALS['BE_USER'];$GLOBALS['TYPO3_DB'], t3lib_extMgm, $BE_USER, 't3lib_BEfunc', $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m']; $GLOBALS['_EXTKEY'], $GLOBALS['MCONF'], $GLOBALS['EM_CONF']['t3m'], $GLOBALS['ICON_PATH']$this->myConf

// 		$class = $GLOBALS['TYPO3_DB'];  //t3lib_extMgmt3lib_BEfunc't3lib_iconWorks';
//
//  		$out[0] = get_declared_classes();
// 		$out[1] = get_class_methods($class);
// 		$out[2] = get_defined_constants();

// 		$GLOBALS['TYPO3_DB']->exec_SELECTquery( //from pages: "is_siteroot=1" is not needed (not always the case), a roottemplate should be there however
// 			'*',
// 			'be_groups',
// 			'uid=10'
// 		);
// 		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
// 		$out[3] = $GLOBALS['TYPO3_DB']->sql_affected_rows();

// 		$out = t3lib_div::stdAuthCode(4);
// 		$out = tx_t3m_main::getFeGroupUsers(2);
 		t3lib_div::debug($out);
//  		t3lib_div::debug($GLOBALS['TCA']); //['tt_content']['ctrl']['typeicons']);$GLOBALS['TBE_STYLES'




				$filename = t3lib_extMgm::extPath('t3m').'res/pbimagegraph/plot_step2.txt';
				$handle = fopen($filename, "r");
				$contents = fread($handle, filesize($filename));
				$ts = t3lib_div::makeInstance("t3lib_tsparser");
				$ts->parse($contents); // now we should have it in $ts->setup
				$plot_step2 = $ts->setup['lib.']['pbimagegraph.'];
				fclose($handle);
				$plot_step2['10.']['20.']['10.']['10.']['dataset.']['10.']['name'] = 'sssDeleted';
				$out = ' <br />'.tx_pbimagegraph_ts::make($plot_step2);



		return $out;
	}

}

?>