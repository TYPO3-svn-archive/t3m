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


/**
 * Hook for srfeuserregister
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 * @todo	hook not implemented yet
 */
class tx_srfeuserregister_hook1 {

	/**
	* Saves delete-reason in fe_users
	*/
	function registrationProcess_beforeSaveDelete($recordArray, &$invokingObj) { // does not happen!
// 		thisisatestcalltoseeifhookgetscalled();

// 		foreach ($recordArray as $key => $val) { // do we have uid of fe_user?
// 			$content .= ' --- '.$key.' : '.$val;
// 		}
// 		$insertFields = array(
// 			'comments' => $content
// 		);
// 		$GLOBALS['TYPO3_DB']->exec_INSERTquery(
// 			'fe_users',
// 			$insertFields
// 		);

		// update fe_users set tx_t3m_deletereason = 'deregistration' where uid = $recordArray['uid']
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/t3m/class.tx_srfeuserregister_hook1.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/t3m/class.tx_srfeuserregister_hook1.php"]);
}


?>