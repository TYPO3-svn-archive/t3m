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
 * Directmail target1 for t3m
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
			$tmpusers = tx_t3m_main::getTargetgroupUsers($targetgroup);
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
		//check bounce config
		$bounceConfig = tx_t3m_bounce::getBounceRules();
		//get bounces from table fe_users
		$bouncereason = get_defined_constants();

		if ($bouncereason == 'TCDIRECTMAIL_SOFTBOUNCE') {
			$previousSoftBounces = tx_t3m_bounce::getPreviousSoftBounces($uid);
			$softBounces = $previousSoftBounces + 1;
			$GLOBALS['TYPO3_DB']->sql_query('UPDATE fe_users SET tx_t3m_softbounces = '.$softBounces.' WHERE uid = '.intval($uid));
			//put him into special 'softbounce' group
			if ($previousSoftBounces >= $bounceConfig['max_softbounces']) { //do action!
				//disable user
				$GLOBALS['TYPO3_DB']->sql_query('UPDATE fe_users SET disable = 1 WHERE uid = '.intval($uid));
			}
		}
		elseif ($bouncereason == 'TCDIRECTMAIL_HARDBOUNCE') {
			$previousHardBounces = tx_t3m_bounce::getPreviousHardBounces($uid);
			$hardBounces = $previousHardBounces + 1;
			$GLOBALS['TYPO3_DB']->sql_query('UPDATE fe_users SET tx_t3m_hardbounces = '.$hardBounces.' WHERE uid = '.intval($uid));
			//put him into special 'hardbounce' group
			if ($previousHardBounces >= $bounceConfig['max_hardbounces']) { //do action!
				//disable user
				$GLOBALS['TYPO3_DB']->sql_query('UPDATE fe_users SET disable = 1 WHERE uid = '.intval($uid));
			}
		} else {
			// no bouncereason given
			// put him into 'bounce' group
		}
	}

}

?>