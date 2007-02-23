<?php

require_once (t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_target_sql.php');
class tx_tcdirectmail_target_fegroups extends tx_tcdirectmail_target_sql {
   var $tableName = 'fe_users';

   function init () {
       $groups = explode(',',$this->fields['fegroups']);
       $groups[] = -1;
       $groups = array_filter($groups);


       $this->data = $GLOBALS['TYPO3_DB']->sql_query(
         "SELECT DISTINCT fe_users.uid,name,address,telephone,fax,email,username,fe_users.title,zip,city,country,www,company,fe_groups.title as group_title
          FROM fe_groups, fe_users
          WHERE fe_groups.uid IN (".implode(',',$groups).")
          AND FIND_IN_SET(fe_groups.uid, fe_users.usergroup)
          AND email != ''
          AND fe_groups.deleted = 0
          AND fe_groups.hidden = 0
          AND fe_users.disable = 0
          AND fe_users.deleted = 0");
   }


   	/**
	 * Main function for disabling receivers when bounces occur
	 *
	 * @return	void		nothing to be returned
	 */
	function disableReceiver($uid, $authCode, $bouncereason) { // uid must be a fe_user uid here
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

			//put him into special 'softbounce' group if we have one and if he is not yet in it ("srfeuserregister-style")
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

			//put him into special 'hardbounce' group if we have one and if he is not yet in it ("srfeuserregister-style")
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