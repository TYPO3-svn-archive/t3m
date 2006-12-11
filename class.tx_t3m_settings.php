<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Stefan Koch <t3m@stefkoch.de>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Check and set some settings of our and other extensions
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_settings	{

	/**
	 * Returns an evaluation  if spam checking is configured correct
	 *
	 * @return	string		a status if spam checking is configured correct
	 */
	function checkForSpamProgram()	{
		$spamstring = exec('echo \' test \' | '.$this->myConf['spam_checker_script']);
// 		$spamstring = exec('echo \' test \' | thisisnotaprogram');
		switch ($spamstring) {
			case '0/0':
				$out = '<img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_fatalerror.gif">&nbsp;Daemon spamd not responding correctly!';
			break;
			case '':
				$out = '<img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_fatalerror.gif">&nbsp;Client spamc not responding correctly!';
			break;
			default :
				$out = '<img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_ok2.gif">&nbsp;Client and daemon working ok.';
			break;
		}
		$out .= '&nbsp;<b>('.$this->myConf['spam_checker_script'].')</b></br>';
		return $out;
	}



	/**
	 * Main function for copying example files
	 *
	 * @return	string		status about the copy task
	 */
	function checkSystemSettings() {
		//lynx, fetchmail, cronjobs
	}

	/**
	 * Returns an evaluation for 3rdparty module setting values
	 *
	 * @return	string		an evaluation for 3rdparty module setting values
	 */
	function checkExternalSettings()	{
		$out = '<img src="'.$GLOBALS['BACK_PATH'].'gfx/wizard_tsconfig_s.gif"> Checking relevant root-TypoScript values:';
		$settingsToCheck = array(
			'plugin.tx_srfeuserregister_pi1.userGroupUponRegistration',
			'plugin.tx_srfeuserregister_pi1.userGroupAfterConfirmation',
			'plugin.tx_srfeuserregister_pi1.pid',
			'plugin.tx_srfeuserregister_pi1.loginPID',
			'plugin.tx_srfeuserregister_pi1.registerPID',
			'plugin.tx_srfeuserregister_pi1.editPID',
			'plugin.tx_srfeuserregister_pi1.confirmPID',
// 			'invalidtestvalue'
			);
		foreach($settingsToCheck as $value) {
			if ($this->rootTS[$value]['value']) {
				$out .= '<br /><img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_ok2.gif" /> &nbsp;'.$value.':&nbsp;'.$this->rootTS[$value]['value'].'&nbsp;'.$GLOBALS['LANG']->getLL('OK');
			} else	{
				$out .= '<br /><img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_fatalerror.gif" /> &nbsp;'.$value.':&nbsp;'.$this->rootTS[$value]['value'].'&nbsp;'.$GLOBALS['LANG']->getLL('notOK');
			}
		}
		return $out;
	}


	/**
	 * Returns an evaluation for module setting values
	 *
	 * @return	string		setting value
	 */
	function checkOwnSettings()	{
		// load settings
		$this->myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']["EXT"]["extConf"][$this->extKey]);
		$out = 'Checking our most important extensions settings:';
		$settingsToCheck = array(
			'groupPending',
			'groupRegistered',
// 			'groupDeregistered',
			'groupBlocked',
			'groupSoftbounces',
			'groupHardbounces',
			'groupTest',
			'targetTest',
			'T3M_Sysfolder',
			'registrations_Sysfolder',
			'userarea_Sysfolder',
			'mailings_Sysfolder',
			'campaigns_Sysfolder',
			'categories_Sysfolder',
			'salutations_Sysfolder',
			'targetgroups_Sysfolder',
			'static_countries_uid',
			'static_country_zones_uid',
			'spam_checker_script',
			'sender_name',
			'sender_email'
// 			'invalidtestvalue'
			);

		foreach($settingsToCheck as $value) {
			if ($this->myConf[$value]) {
				if (($this->myConf[$value]) == 'NULL') {
					$out .= '<br /><img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_note.gif">&nbsp;'.$value.':&nbsp;'.$this->myConf[$value].':&nbsp;'.$GLOBALS['LANG']->getLL('Warning');
				} else {
					$out .= '<br /><img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_ok2.gif">&nbsp;'.$value.':&nbsp;'.$this->myConf[$value].':&nbsp;'.$GLOBALS['LANG']->getLL('OK');
				}
			} else	{
				$out .= '<br /><img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_fatalerror.gif">&nbsp;'.$value.':&nbsp;'.$this->myConf[$value].':&nbsp;'.$GLOBALS['LANG']->getLL('notOK');
			}
		}
		return $out;
	}

	/**
	 * Returns single module settings
	 *
	 * @param	[type]		$setting: ...
	 * @return	string		setting value
	 */
	function getExternalSetting($setting)	{
		$out = $this->rootTS[$setting]['value'];
		return $out;
	}

	/**
	 * Returns a link for editing all extension settings
	 *
	 * @return	string		a link for editing all extension settings
	 */
	function editExternalExtensionConfig()	{
		$out = '';
		$extensions = array(
			'rs_userimp',
			'sr_feuser_register',
			'static_info_tables',
			'tcdirectmail'
			);
		foreach($extensions as $extension) {
			$out .= '<img src="'.$GLOBALS['BACK_PATH'].'gfx/i/sys_action.gif" />&nbsp;<a href="'.$GLOBALS['BACK_PATH'].'mod/tools/em/index.php?CMD[showExt]='.$extension.'&SET[singleDetails]=info">'.$extension.'\'s&nbsp;'.$GLOBALS['LANG']->getLL('ExtensionSettings').'</a>&nbsp;<br />';
		}
		return $out;
	}

	/**
	 * Returns a link for editing all extension settings
	 *
	 * @return	string		a link for editing all extension settings
	 */
	function editOwnExtensionConfig()	{
		$out = '<img src="'.$GLOBALS['BACK_PATH'].'gfx/i/sys_action.gif" />&nbsp;';
		$out .= '<a href="'.$GLOBALS['BACK_PATH'].'mod/tools/em/index.php?CMD[showExt]='.$this->extKey.'&SET[singleDetails]=info">'.$GLOBALS['LANG']->getLL('ExtensionSettings').'</a>';
		return $out;
	}

	/**
	 * Main function for changing an extensions setting in localconf file, BTW should work for all other extensions (similar t3lib_function please!)
	 *
	 * @param	string		$extension: extension key
	 * @param	array		$settings: key-value array of settings
	 * @return	string		status about the creation task
	 */
	function changeExtensionSettings($extKey, $settings) {

		// read old values
		if (!($extKey)) {
			$extKey = $this->extKey; //t3m
		}
		$this->myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extKey]);

		$out = 'Changing '.$extKey.'\'s extensions settings...<br/>';

		// read in new values from created stuff:
		if (!($settings)) {
			$out = 'No settings found to change..<br/>';
			return $out;
		}

		foreach ($settings as $key => $val) {
			$this->myConf[$key] = $val;
		}

		// get it as a serialized array:
		//$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m'] = serialize($this->myConf);
		// put it back into the serialized array
		//$newConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m']);

		// create a string for the config file
		$newConfigString = '$TYPO3_CONF_VARS[\'EXT\'][\'extConf\'][\''.$extKey.'\'] = \''.serialize($this->myConf).'\';';
		$newConfigStringConfigOnly = serialize($this->myConf);

		// write the config into the conf file:
// 		$filename = t3lib_extMgm::extPath($this->extKey).'ext_localconf.php'; // not used anymore because it creates a conflict with EM, EM does not read the values!!
		$filename = constant('PATH_typo3conf').'localconf.php'; // sensible stuff!
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));

		// first do a simple check if we have a config
		$needle = '$TYPO3_CONF_VARS[\'EXT\'][\'extConf\'][\''.$extKey.'\']';
		if (!strstr($contents,$needle)) { // if its the first time and nothing is found just append the config
// 			tx_t3m_postinstall::createLocalExtensionSettings();
 			$newContents = str_replace('?>',$newConfigString."\n?>",$contents);
			$out .= 'Config not found! appending!';
		} else { // config was found and has to be altered
	// do a search and replace:
			$pattern = '/(\$TYPO3_CONF_VARS\[\'EXT\'\]\[\'extConf\'\]\[\''.$extKey.'\'\] = \')(.*)(\';)(.*)/'; // $2 will be old config
			$subject = $contents;

			preg_match($pattern,substr($subject,strlen($pattern)), $matches); // find our config
// 			t3lib_div::debug($matches);

 			$newContents = str_replace($matches[2],$newConfigStringConfigOnly,$contents); // and replace it with new
 			$out .= 'Config found! replace!';

// 			$newContents = str_replace($matches[0],'',$contents); // or delete it
// 			$out .= 'found! remove!';
		}
		fclose($handle);

	// write new config file:
		$handle = fopen($filename, "w");
		fwrite($handle,$newContents);
		fclose($handle);

/*		$out.=$filename.'<p>changed from: '.htmlspecialchars($contents).'</p>';
		$out.='<p>to:<br/>'.htmlspecialchars($newContents);
*/		$out.='<br/>Please clear typo3conf cache!!!</p>';

		return $out;
	}

	/**
	* Returns true if actions took place ok.
	*
	* @return	boolean true if actions took place ok.
	* @todo		implement some checks (return value evaluation)
	*/
	function handlePostVars() {

		require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_tools.php');
		require_once(t3lib_extMgm::extPath('tcdirectmail').'mod1/index.php');

		if (t3lib_div::_GP('check_valid_mail')) {
			// $_REQUEST['id'] is there
			$out .= tx_tcdirectmail_module1::checkMailValidity();
		}
		if (t3lib_div::_GP('clear_invalid')) {
			// $_REQUEST['id'] and $_REQUEST['clear_invalid'] are there
			$out .= tx_tcdirectmail_module1::doMaintenance();
		}
		if (t3lib_div::_GP('disable_invalid_user')) {
			$out .= tx_t3m_settings::disableUser(t3lib_div::_GP('user_id')); //or what action? edit user?
		}

		return $out;
	}


	/**
	 * Return table for clearing invalig logs and checking validity on mails
	 *
	 * @return	table for clearing log data and checking validity on mails
	 * @todo	better integration into tcdirectmail (function calls?)
	 */
	function maintenance() {
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('maintenanceUsers').'</h3>';
		$users = tx_t3m_settings::getInvalidUsers(); //@todo: apart from not having email, what else? bounces?
		$out .= tx_t3m_main::tableForFeusers($users);
		t3lib_div::debug($users);

		$out = '<h3>'.$GLOBALS['LANG']->getLL('maintenanceMails').'@todo...</h3>';
/*		$mails = tx_t3m_main::getUnsentMails(); //@todo: change to pages for which warnings are found
		$out .= tx_t3m_settings::tableForMailMaintenance($mails);*/

		$out .= '<h3>'.$GLOBALS['LANG']->getLL('maintenanceLogdata').'@todo...</h3>';
// 		$mails = tx_t3m_main::getSentMails(); //@todo: change to pages for which invalid log data is found
// 		$out .= tx_t3m_settings::tableForLogdataMaintenance($mails);

		return $out;
	}

	/**
	 * Function for clearing invalig logs
	 *
	 * @param	array		$pids: mail ids
	 * @return	string		table for clearing invalig logs and check validity
	 */
	function tableForMailMaintenance($pids) {
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('maintenanceMails').'</td></tr>';
			foreach($pids as $pid) {
				$out .= '<tr><td>'.$pid['title'].'</td>
					<td><form><input type="submit" name="check_validity" value="'.$GLOBALS['LANG']->getLL('maintenanceMails').'" />
					<input type="hidden" name="id" value="'.$pid['uid'].'" /></form></td></tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}


	/**
	 * Function for clearing invalig logs
	 *
	 * @param	array		$pids: mail ids
	 * @return	string		table for clearing invalig logs and check validity
	 */
	function tableForLogdataMaintenance($pids) {
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('maintenanceLogdata').'</td></tr>';
			foreach($pids as $pid) {
				$out .= '<tr><td>'.$pid['title'].'</td>
					<td><form><input type="submit" name="clear_invalid" value="'.$GLOBALS['LANG']->getLL('maintenanceLogdata').'" />
					<input type="hidden" name="id" value="'.$pid['uid'].'" /></form></td></tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}

	/**
	 * Function for clearing invalig logs
	 *
	 * @param	array		$pids: mail ids
	 * @return	array		invalid users
	 */
	function getInvalidUsers() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,username,gender,email,date_of_birth,tx_t3m_categories',
			'fe_users',
			'deleted=0' //' AND disable=0'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			if (!($row['email'])) {
				$out[] = $row;
			}
		}
		return $out;
	}

	/**
	 * Function for clearing invalig logs
	 *
	 * @param	array		$pids: array with uids of pages
	 * @return	boolean		if clearing invalig logs went ok
	 */
	function clearInvalidLogdata($pid) { //rewrite of tcdirectmail?
		if ($pid) { //
		} else { // clear all log data
		}
	}

}

?>