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
class tx_t3m_postinstall	{

	/**
	* Returns initial stuff form or status about the imports
	*
	* @return	string form for import stuff or status about the imports
	*/
	function postinstallActions() {
		if (t3lib_div::_GP('createStuff')) {
			$content .= tx_t3m_postinstall::main();
		} else {
			$content .= '<form>';
			$content .= '<p><input type="submit" name="createStuff" value="'.$GLOBALS['LANG']->getLL('createStuff').'" /></p>';
			$content .= '</form>';
		}
		return $content;
	}

	/**
	* Main function for the postinstall actions (INSERT into db and CONFIGURE extension)
	*
	* @return	string	status about the postinstall actions performed
	*/
	function main()	{
// 		$out = tx_t3m_postinstall::create();
// 		$out .= tx_t3m_postinstall::copyFiles();
// 		$out .= tx_t3m_settings::checkSystemSettings();
// 		$out .= tx_t3m_settings::changeExtensionSettings();
		return $out;
	}

	/**
	* Main function for creation tasks
	*
	* @return	string	status about the creation tasks
	*/
	function create() {
// 		$out = tx_t3m_postinstall::createSysfolders();
// 		$out .= tx_t3m_postinstall::createBeGroups(); //"admin, controller, author"
// 		$out .= tx_t3m_postinstall::createBeUsers(); //"admin, controller, author"
// 		$out .= tx_t3m_postinstall::createFeGroups(); // check srfeuserregister for 'pending' 'subscriptions', others: 'tests','deregistrations', 'blocked, 'softbounces', 'hardbounces'
// 		$out .= tx_t3m_postinstall::createFeUsers(); //not important
// 		$out .= tx_t3m_postinstall::createTargetgroups();

		return $out;
	}

	/**
	* Main function for copying example files
	*
	* @return	string	status about the copy task
	*/
	function copyFiles() {
		// cp /typo3conf/ext/t3m/static/t3m-example-cvs.txt /uploads/tx_tcdirectmail/
	}


	/**
	* Main function for creation task sysfolders
	*
	* @return	string	status about the creation task
	*/
	function createSysfolders() {

	//create our t3m sysfolder
		$insertArray = array('pid' => 0,'title' => 'T3M','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$this->t3mFolder = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'T3M Sysfolder with ID: '.$this->t3mFolder.' created! <br/>';

	//create srfeuserregister sysfolder
		$insertArray = array('pid' => $this->t3mFolder,'title' => 'srfeuserregister','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$this->srfeuserregisterFolder = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'srfeuserregister Sysfolder with ID: '.$this->srfeuserregisterFolder.' created! <br/>';

	//create tcdirectmail sysfolder
		$insertArray = array('pid' => $this->t3mFolder,'title' => 'tcdirectmail','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$this->tcdirectmailFolder = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'tcdirectmail Sysfolder with ID: '.$this->tcdirectmailFolder.' created! <br/>';

		return $out;
	}

	/**
	* Main function for creation task targetgroups
	*
	* @return	string	status about the creation task
	*/
	function createTargetgroups() {
	//create test-usergroup in srfeuserregister sysfolder
		$insertArray = array('pid' => $this->srfeuserregisterFolder,'title' => 'TestGroup','hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_groups', $insertArray);
		$this->testGroup = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'testgroup with ID: '.$this->testGroup.' created! <br/>';

	//create test-targetgroup in tcdirectmail sysfolder with uid= $GLOBALS['TYPO3_DB']->sql_insert_id() AND type=fe_group AND tx_t3m_target = $testGroup;
		$insertArray = array('pid' => $this->tcdirectmailFolder,'title' => 'TestTarget','targettype' => 'tx_tcdirectmail_target_fegroups','fegroups' => $testGroup,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_tcdirectmail_targets', $insertArray);
		$this->testTarget = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'test-targetgroup with ID: '.$this->testTarget.' created! <br/>';

		return $out;
	}

	/**
	* Main function for creation task begroups
	*
	* @return	string	status about the creation task
	*/
	function createBeGroups() {
	//create be-usergroups in root-page
		$groupMods = 'tools_txrsuserimpM1,txt3mM0,txt3mM0_txt3mM1,txt3mM0_txt3mM2,txt3mM0_txt3mM3,txt3mM0_txt3mM4,txt3mM0_txt3mM5'; // can do all
		$insertArray = array('pid' => 0,'title' => 'T3M Admins','hidden' => 0,'groupMods' => $groupMods);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups', $insertArray);
		$this->beGroupAdmins = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'backend group Admins with ID: '.$this->beGroupAdmins.' created! <br/>';

		$groupMods = 'txt3mM0,txt3mM0_txt3mM4'; // can view stats
		$insertArray = array('pid' => 0,'title' => 'T3M Controllers','hidden' => 0,'groupMods' => $groupMods);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups', $insertArray);
		$this->beGroupControllers = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'backend group Controllers with ID: '.$this->beGroupControllers.' created! <br/>';

		$groupMods = 'txt3mM0,txt3mM0_txt3mM2,txt3mM0_txt3mM3'; // can create and send
		$insertArray = array('pid' => 0,'title' => 'T3M Authors','hidden' => 0,'groupMods' => $groupMods);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups', $insertArray);
		$this->beGroupAuthors = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'backend group Authors with ID: '.$this->beGroupAuthors.' created! <br/>';




		// @todo: give rights to groups:
		//  $uc = 'a:19:{s:14:"interfaceSetup";s:0:"";s:10:"moduleData";a:4:{s:12:"alt_menu.php";a:0:{}s:7:"txt3mM0";a:1:{s:8:"function";s:8:"settings";}s:19:"tools_txrsuserimpM1";a:1:{s:8:"function";s:1:"1";}s:16:"xMOD_alt_doc.php";a:0:{}}s:19:"thumbnailsByDefault";i:0;s:14:"emailMeAtLogin";i:0;s:13:"condensedMode";i:0;s:10:"noMenuMode";i:0;s:17:"startInTaskCenter";i:0;s:18:"hideSubmoduleIcons";i:0;s:8:"helpText";i:1;s:8:"titleLen";i:30;s:17:"edit_wideDocument";s:1:"0";s:18:"edit_showFieldHelp";s:4:"icon";s:8:"edit_RTE";s:1:"1";s:20:"edit_docModuleUpload";s:1:"1";s:15:"disableCMlayers";i:0;s:13:"navFrameWidth";s:0:"";s:17:"navFrameResizable";i:0;s:4:"lang";s:0:"";s:15:"moduleSessionID";a:3:{s:7:"txt3mM0";s:32:"f6544cc0303257b1954069fe826b6a03";s:19:"tools_txrsuserimpM1";s:32:"f6544cc0303257b1954069fe826b6a03";s:16:"xMOD_alt_doc.php";s:32:"f6544cc0303257b1954069fe826b6a03";}}';

		return $out;
	}

	/**
	* Main function for creation task beusers
	*
	* @return	string	status about the creation task
	*/
	function createBeUsers() {
		//create be-users in root-page
		// creating a superadmin with 'admin' => 1 would be too 'intrusive' i guess

// 		$this->beGroupAdmins = 6;
// 		$this->beGroupControllers = 7;
// 		$this->beGroupAuthors = 8;

		//admin
		$userMods = 'tools_txrsuserimpM1,txt3mM0,txt3mM0_txt3mM1,txt3mM0_txt3mM2,txt3mM0_txt3mM3,txt3mM0_txt3mM4,txt3mM0_txt3mM5'; // can do all
		$user = 't3madmin';
		$insertArray = array('pid' => 0,'username' => $user,'password' => md5($user),'disable' => 0,'usergroup' => $this->beGroupAdmins, 'admin' => 0, 'userMods'  => $userMods);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);
		$this->beUserAdmin = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'backend user '.$user.' with ID: '.$this->beUserAdmin.' and password: '.$user.' created! <br/>';

		//controller
		$userMods = 'txt3mM0,txt3mM0_txt3mM4'; // can view stats
		$user = 't3mcontroller';
		$insertArray = array('pid' => 0,'username' => $user,'password' => md5($user),'disable' => 0,'usergroup' => $this->beGroupControllers, 'admin' => 0,'lang' => 'de', 'userMods'  => $userMods); //german!
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);
		$this->beUserController = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'backend user '.$user.' with ID: '.$this->beUserController.' and password: '.$user.' created! <br/>';

		//author
		$userMods = 'txt3mM0,txt3mM0_txt3mM2,txt3mM0_txt3mM3'; // can create and send
		$user = 't3mautor';
		$insertArray = array('pid' => 0,'username' => $user,'password' => md5($user),'disable' => 0,'usergroup' => $this->beGroupAuthors, 'admin' => 0,'lang' => 'de', 'userMods'  => $userMods); //german!
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);
		$this->beUserAuthor = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'backend user '.$user.' with ID: '.$this->beUserAuthor.' and password: '.$user.' created! <br/>';

		return $out;

	}


}

?>