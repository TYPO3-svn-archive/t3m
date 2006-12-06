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
 * This function is for doing some initialization stuff
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package TYPO3
 * @subpackage t3m
 */
class tx_t3m_settings	{

	/**
	* Returns an evaluation  if spam checking is configured correct
	*
	* @return	string	a status if spam checking is configured correct
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
	* @return	string	status about the copy task
	*/
	function checkSystemSettings() {
		//lynx, fetchmail, cronjobs
	}

	/**
	* Returns an evaluation for 3rdparty module setting values
	*
	* @return	string	an evaluation for 3rdparty module setting values
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
	* @return	string	setting value
	*/
	function checkOwnSettings()	{
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
			'sr_feuser_register_Sysfolder',
			'tcdirectmail_Sysfolder',
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
	* @return	string	setting value
	*/
	function getExternalSetting($setting)	{
		$out = $this->rootTS[$setting]['value'];
		return $out;
	}


	/**
	* Returns a link for editing all extension settings
	*
	* @return	string	a link for editing all extension settings
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
	* @return	string	a link for editing all extension settings
	*/
	function editOwnExtensionConfig()	{
		$out = '<img src="'.$GLOBALS['BACK_PATH'].'gfx/i/sys_action.gif" />&nbsp;';
		$out .= '<a href="'.$GLOBALS['BACK_PATH'].'mod/tools/em/index.php?CMD[showExt]='.$this->extKey.'&SET[singleDetails]=info">'.$GLOBALS['LANG']->getLL('ExtensionSettings').'</a>';
		return $out;
	}


	/**
	* Main function for creation task beusers
	*
	* @return	string	status about the creation task
	*/
	function changeExtensionSettings() {
		$out = 'Changing extensions settings...<br/>';
	// read in new values from created stuff:
		$this->myConf['T3M_Sysfolder'] = $this->t3mFolder;
		$this->myConf['sr_feuser_register_Sysfolder'] = $this->srfeuserregisterFolder;
		$this->myConf['tcdirectmail_Sysfolder'] = $this->tcdirectmailFolder;
		$this->myConf['groupTest'] = $this->testGroup;
		$this->myConf['targetTest'] = $this->testTarget;
	// get it as a serialized array:
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m'] = serialize($this->myConf);
		$newConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m']);
		$newConfigString = '$TYPO3_CONF_VARS[\'EXT\'][\'extConf\'][\'t3m\'] = \''.serialize($this->myConf).'\';';
		$newConfigStringConfigOnly = serialize($this->myConf);
	// write the config into the conf file:
// 		$filename = t3lib_extMgm::extPath($this->extKey).'ext_localconf.php'; // not used anymore because it creates a conflict with EM!!
		$filename = constant('PATH_typo3conf').'localconf.php'; // sensible stuff, i know!
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));

	// first do a simple check if we have a config
		$needle = '$TYPO3_CONF_VARS[\'EXT\'][\'extConf\'][\'t3m\']';
		if (!strstr($contents,$needle)) { // if its the first time and nothing is found just append the config
// 			tx_t3m_postinstall::createLocalExtensionSettings();
 			$newContents = str_replace('?>',$newConfigString."\n?>",$contents);
			$out .= 'Config not found! appending!';
		} else { // config was found and has to be altered
	// do a search and replace:
			$pattern = '/(\$TYPO3_CONF_VARS\[\'EXT\'\]\[\'extConf\'\]\[\'t3m\'\] = \')(.*)(\';)(.*)/'; // $2 will be old config
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
*/		$out.='<br/>Please clear cache!!!</p>';

		return $out;
	}

}

?>