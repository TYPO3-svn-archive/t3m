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
// require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_main.php');
require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_addresses.php');
require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_bounce.php');

/**
 * TCDirectmail target1 for t3m
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_target1 extends tx_tcdirectmail_target_array {

	/**
	 * Main function for writes content to $this->data
	 *
	 * @return	void		nothing to be returned
	 */
	function init() {
		//multiple targetgroups enabled;
		$targetgroups = explode(',',$this->fields['tx_t3m_target']); // (uids from our targetgroup table)
		$i = 0;
		foreach ($targetgroups as $targetgroup) {
			$tmpusers = tx_t3m_addresses::getTargetgroupUsers($targetgroup);
			foreach ($tmpusers as $tmpuser) {
				if(!(in_array($tmpuser,$sentusers))) { // is not already in array, so no duplicate here
					if ($tmpuser['email']) { // do email sanity check here?
						$this->data[$i]['uid'] = $tmpuser['uid'];
						$this->data[$i]['email'] = $tmpuser['email'];
						$this->data[$i]['plain_only'] = $tmpuser['plain_only'];
					}
					$sentusers[] = $tmpuser; // add him to recognize duplicates
					$i++;
				}
			}
		}
	}

	/**
	 * Main function for disabling receivers when bounces occur
	 *
	 * @return	void		nothing to be returned
	 */
	function disableReceiver($uid, $authCode, $bouncereason) { // uid is a fe_user uid!!
		//get extension config
		$myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m']);

		//get bounce config or set hardcoded if not there
		if (!($myConf['max_hardbounce'])) {
			$myConf['max_hardbounce'] = 2;
		}
		if (!($myConf['max_softbounce'])) {
			$myConf['max_softbounce'] = 3;
		}

		//get bounce data from table fe_users
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_t3m_softbounces,tx_t3m_hardbounces,usergroup',
			'fe_users',
			'uid='.intval($uid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$previousSoftBounces = $row['tx_t3m_softbounces'];
		$previousHardBounces = $row['tx_t3m_hardbounces'];
		$userGroups = explode(',',$row['usergroup']);

		if ($bouncereason == 'TCDIRECTMAIL_SOFTBOUNCE') {

			//count bounces
			$softBounces = $previousSoftBounces + 1;
			$fields_values['tx_t3m_softbounces'] = $softBounces;

			//put him into special 'softbounce' group if he is not yet in it
			if ($myConf['groupSoftbounces']) { //we have a group to collect the users
				if (!(in_array($myConf['groupSoftbounces'],$userGroups))) {
					$fields_values['usergroup'] = $row['usergroup'].','.$myConf['groupSoftbounces'];
				}
			}

			//disable if max bounces reached
			if ($previousSoftBounces >= $myConf['max_softbounces']) {
				$out .= 'Max softbounces reached ('.$myConf['max_softbounces'].'). Disabling user. '.intval($uid);
				//disable user
				$fields_values['disable'] = 1;
			}

		} elseif ($bouncereason == 'TCDIRECTMAIL_HARDBOUNCE') {

			//count bounces
			$hardBounces = $previousHardBounces + 1;
			$fields_values['tx_t3m_hardbounces'] = $hardBounces;

			//put him into special 'hardbounce' group if he is not yet in it
			if ($myConf['groupHardbounces']) { //we have a group to collect the users
				if (!(in_array($myConf['groupHardbounces'],$userGroups))) {
					$fields_values['usergroup'] = $row['usergroup'].','.$myConf['groupHardbounces'];
				}
			}

			//disable if max bounces reached
			if ($previousHardBounces >= $myConf['max_hardbounces']) {
				$out .= 'Max hardbounces reached ('.$myConf['max_hardbounces'].'). Disabling user. '.intval($uid);
				//disable user
				$fields_values['disable'] = 1;
			}

		} else {
			// no bouncereason given
		}

		$fields_values['tstamp'] = time();

		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'fe_users',
			'uid='.intval($uid),
			$fields_values
		);

		return $out;
	}

}

?>