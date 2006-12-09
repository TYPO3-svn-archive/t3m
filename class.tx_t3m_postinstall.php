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
 * @subpackage tx_t3m
 */
class tx_t3m_postinstall	{

	/**
	* Returns form for postinstall actions and status about the postinstall actions performed
	*
	* @return	string form for postinstall actions and status about the postinstall actions performed
	*/
	function main() {
		if (t3lib_div::_GP('postinstall')) {
			$out .= tx_t3m_postinstall::handlePostVars();
		}
		$out .= tx_t3m_postinstall::form();
		return $out;
	}

	/**
	* Returns form for postinstall actions and buttons for triggering them
	*
	* @return	string form for import stuff or status about the imports and status about the postinstall actions performed
	*/
	function form() {
		$out .= '<form>';
		$out .= '<table>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('createSysfolders').'</td><td><input type="submit" name="createSysfolders" value="'.$GLOBALS['LANG']->getLL('Create').'" /></td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('createFegroups').'</td><td><input type="submit" name="createFegroups" value="'.$GLOBALS['LANG']->getLL('Create').'" /></td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('createBegroupsAndUsers').'</td><td><input type="submit" name="createBegroupsAndUsers" value="'.$GLOBALS['LANG']->getLL('Create').'" /></td></tr>';
// 		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('createDemocontent').'</td><td><input type="submit" name="createDemocontent" value="'.$GLOBALS['LANG']->getLL('Create').'" /></td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('createTargetgroups').'</td><td><input type="submit" name="createTargetgroups" value="'.$GLOBALS['LANG']->getLL('Create').'" /></td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('createAll').'</td><td><input type="submit" name="createAll" value="'.$GLOBALS['LANG']->getLL('Create').'" /></td></tr>';
		$out .= '</table><input type="hidden" name="postinstall" value="1" /></form>';
		return $out;
	}

	/**
	* Returns true if actions took place ok.
	*
	* @return	boolean true if actions took place ok.
	* @todo		implement some checks (return value evaluation)
	*/
	function handlePostVars() {
		$out = $GLOBALS['LANG']->getLL('performingPostinstalls');

		//getting old config
		$settings = $this->myConf;

		t3lib_div::debug($settings);
		if (t3lib_div::_GP('createSysfolders')) {
			$out .= tx_t3m_postinstall::createSysfolders();
		}
		if (t3lib_div::_GP('createFegroups')) {
			$out .= tx_t3m_postinstall::createFegroups(); // check srfeuserregister for 'pending' 'subscriptions', others: 'tests','deregistrations', 'blocked, 'softbounces', 'hardbounces'
		// 	$out .= tx_t3m_postinstall::createFeUsers(); //none so far
		}
		if (t3lib_div::_GP('createBeUsers')) {
			$out .= tx_t3m_postinstall::createBeGroups();
			$out .= tx_t3m_postinstall::createBeUsers(); //"admin, controller, author"
		}
		if (t3lib_div::_GP('createTargetgroups')) {
			$out .= tx_t3m_postinstall::createTargetgroups();
		}
		if (t3lib_div::_GP('createAll')) {
			$out = tx_t3m_postinstall::createSysfolders();
			$out .= tx_t3m_postinstall::createFegroups();
			$out .= tx_t3m_postinstall::createBeGroups();
			$out .= tx_t3m_postinstall::createTargetgroups();
		}

		//now $settings has new values
		//write settings
		$out .= tx_t3m_settings::changeExtensionSettings($settings);

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

	//create our T3M_Sysfolder
		$insertArray = array('pid' => 0,'title' => 'T3M','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['T3M_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'T3M Sysfolder (ID:'.$settings['T3M_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create registrations_Sysfolder (srfeuserregister - where to store user data, registration form and confirmation page)
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'registrations','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['registrations_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Registrations Sysfolder (ID: '.$settings['registrations_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create userarea_Sysfolder (srfeuserregister - where to store login page and pages users see after login data)
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'userarea','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['userarea_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Userarea Sysfolder (ID: '.$settings['userarea_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create receivers_Sysfolder (tcdirectmail - targets/receivers)
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'receivers','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['receivers_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Receivers Sysfolder (ID: '.$settings['receivers_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create mailings_Sysfolder (tcdirectmail - mailings)
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'mailings','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['mailings_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Mailings Sysfolder (ID: '.$settings['mailings_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create campaigns_Sysfolder
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'campaigns','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['campaigns_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Campaigns Sysfolder (ID: '.$settings['campaigns_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create salutations_Sysfolder
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'salutations','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['salutations_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Salutations Sysfolder (ID: '.$settings['salutations_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create categories_Sysfolder
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'categories','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['categories_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Categories Sysfolder (ID: '.$settings['categories_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

	//create targetgroups_Sysfolder
		$insertArray = array('pid' => $settings['T3M_Sysfolder'],'title' => 'targetgroups','doktype' => 254,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages', $insertArray);
		$settings['targetgroups_Sysfolder'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Targetgroups Sysfolder (ID: '.$settings['targetgroups_Sysfolder'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		return $out;
	}


	/**
	* Main function for creation task targetgroups
	*
	* @return	string	status about the creation task
	*/
	function createFegroups() {
	//create test-usergroup in srfeuserregister sysfolder
		$insertArray = array('pid' => $settings['registrations_Sysfolder'],'title' => 'TestGroup','hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_groups', $insertArray);
		$settings['testGroup'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'Test frontend group (ID: '.$settings['testGroup'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		return $out;
	}

	/**
	* Main function for creation task targetgroups
	*
	* @return	string	status about the creation task
	*/
	function createTargetgroups() {
	//create test-targetgroup in tcdirectmail sysfolder with uid= $GLOBALS['TYPO3_DB']->sql_insert_id() AND type=fe_group AND tx_t3m_target = $testGroup;
		$insertArray = array('pid' => $settings['receivers_Sysfolder'],'title' => 'Testreceivers','targettype' => 'tx_tcdirectmail_target_fegroups','fegroups' => $testGroup,'hidden' => 0);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_tcdirectmail_targets', $insertArray);
		$settings['testReceivers'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Test receiverlist (ID: '.$settings['testReceivers'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		return $out;
	}

	/**
	* Main function for creation task begroups
	*
	* @return	string	status about the creation task
	* @todo:	give correct rights to groups (serialized array)
	*/
	function createBeGroups() {
	//create be-usergroups in root-page

		$non_exclude_fields = 'pages:doktype,pages:hidden,pages:nav_hide,pages:subtitle,pages:target,pages:author_email,pages:tx_tcdirectmail_senttime,pages:tx_tcdirectmail_repeat,pages:tx_tcdirectmail_plainconvert,pages:tx_tcdirectmail_testreceivers,pages:tx_tcdirectmail_real_target,pages:tx_tcdirectmail_test_target,pages:tx_tcdirectmail_sendername,pages:tx_tcdirectmail_senderemail,pages:tx_tcdirectmail_spy,pages:tx_tcdirectmail_register_clicks,tt_content:header_layout,tt_content:subheader,tt_content:text_properties,tt_content:splash_layout,fe_users:name,fe_users:address,fe_users:telephone,fe_users:fax,fe_users:email,fe_users:title,fe_users:zip,fe_users:city,fe_users:www,fe_users:company,fe_users:disable,fe_users:starttime,fe_users:endtime,fe_users:module_sys_dmail_html,fe_users:tx_commerce_tt_address_id,tx_tcdirectmail_targets:plain_only,tx_tcdirectmail_targets:beusers,tx_tcdirectmail_targets:fegroups,tx_tcdirectmail_targets:fepages,tx_tcdirectmail_targets:ttaddress,tx_tcdirectmail_targets:rawsql,tx_tcdirectmail_targets:csvseparator,tx_tcdirectmail_targets:csvfields,tx_tcdirectmail_targets:csvvalues,tx_tcdirectmail_targets:csvfilename,tx_tcdirectmail_targets:csvurl,tx_tcdirectmail_targets:targettype,tx_tcdirectmail_targets:htmlfile,tx_tcdirectmail_targets:htmlfetchtype,tx_tcdirectmail_targets:confirmed_receivers,tx_tcdirectmail_targets:tx_t3m_target';
		$pagetypes_select = '1,2,3,189,4,5,6,7,199,254,255';
		$tables_modify = 'pages,tt_content,fe_users,fe_groups,tt_address,tx_tcdirectmail_targets,tx_tcdirectmail_bounceaccount,tx_t3m_campaigns,tx_t3m_targetgroups,tx_t3m_directmails,tx_t3m_categories,tx_t3m_salutations';
		$groupModsAll = 'web_layout,web_view,web_list,web_txtcdirectmailM1,web_info,web_perm,web_func,tools_txrsuserimpM1,tools_txtcdirectmailM2,txt3mM0';

		$groupMods = $groupModsAll.'txt3mM0_txt3mM1,txt3mM0_txt3mM2,txt3mM0_txt3mM3,txt3mM0_txt3mM4,txt3mM0_txt3mM5'; // can do all
		$insertArray = array('pid' => 0,'hidden' => 0,'inc_access_lists' => 1, 'workspace_perms' => 7,'db_mountpoints' => $settings['T3M_Sysfolder'],
				     'title' => 'T3M Admins','groupMods' => $groupMods,
				     'non_exclude_fields' => $non_exclude_fields,'pagetypes_select' => $pagetypes_select,'tables_modify' => $tables_modify);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups', $insertArray);
		$settings['beGroupAdmins'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'backend group Admins (ID: '.$settings['beGroupAdmins'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		$groupMods = $groupModsAll.'txt3mM0_txt3mM4'; // can view stats
		$insertArray = array('pid' => 0,'hidden' => 0,'inc_access_lists' => 1, 'workspace_perms' => 7,'db_mountpoints' => $settings['T3M_Sysfolder'],
				     'title' => 'T3M Controllers','groupMods' => $groupMods,
				     'non_exclude_fields' => $non_exclude_fields,'pagetypes_select'=> $pagetypes_select,'tables_modify' => $tables_modify );
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups', $insertArray);
		$settings['beGroupControllers'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'backend group Controllers (ID: '.$settings['beGroupControllers'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		$groupMods = $groupModsAll.'txt3mM0_txt3mM2,txt3mM0_txt3mM3'; // can create and send
		$insertArray = array('pid' => 0,'hidden' => 0,'inc_access_lists' => 1, 'workspace_perms' => 7,'db_mountpoints' => $settings['T3M_Sysfolder'],
				     'title' => 'T3M Authors','groupMods' => $groupMods,
				     'non_exclude_fields' => $non_exclude_fields,'pagetypes_select'=> $pagetypes_select,'tables_modify' => $tables_modify);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_groups', $insertArray);
		$settings['beGroupAuthors'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'backend group Authors (ID: '.$settings['beGroupAuthors'].') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

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

//  $uc = 'a:19:{s:14:"interfaceSetup";s:0:"";s:10:"moduleData";a:4:{s:12:"alt_menu.php";a:0:{}s:7:"txt3mM0";a:1:{s:8:"function";s:8:"settings";}s:19:"tools_txrsuserimpM1";a:1:{s:8:"function";s:1:"1";}s:16:"xMOD_alt_doc.php";a:0:{}}s:19:"thumbnailsByDefault";i:0;s:14:"emailMeAtLogin";i:0;s:13:"condensedMode";i:0;s:10:"noMenuMode";i:0;s:17:"startInTaskCenter";i:0;s:18:"hideSubmoduleIcons";i:0;s:8:"helpText";i:1;s:8:"titleLen";i:30;s:17:"edit_wideDocument";s:1:"0";s:18:"edit_showFieldHelp";s:4:"icon";s:8:"edit_RTE";s:1:"1";s:20:"edit_docModuleUpload";s:1:"1";s:15:"disableCMlayers";i:0;s:13:"navFrameWidth";s:0:"";s:17:"navFrameResizable";i:0;s:4:"lang";s:0:"";s:15:"moduleSessionID";a:3:{s:7:"txt3mM0";s:32:"f6544cc0303257b1954069fe826b6a03";s:19:"tools_txrsuserimpM1";s:32:"f6544cc0303257b1954069fe826b6a03";s:16:"xMOD_alt_doc.php";s:32:"f6544cc0303257b1954069fe826b6a03";}}';


		//admin
		$userMods = 'tools_txrsuserimpM1,txt3mM0,txt3mM0_txt3mM1,txt3mM0_txt3mM2,txt3mM0_txt3mM3,txt3mM0_txt3mM4,txt3mM0_txt3mM5'; // can do all
		$user = 't3madmin';
		$insertArray = array('pid' => 0,'username' => $user,'password' => md5($user),'disable' => 0,'usergroup' => $settings['beGroupAdmins'], 'admin' => 0, 'userMods'  => $userMods);
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);
		$settings['beUserAdmin'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out = 'Backend user '.$user.' (ID: '.$settings['beUserAdmin'].' and password: '.$user.') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		//controller
		$userMods = 'txt3mM0,txt3mM0_txt3mM4'; // can view stats
		$user = 't3mcontroller';
		$insertArray = array('pid' => 0,'username' => $user,'password' => md5($user),'disable' => 0,'usergroup' => $settings['beGroupControllers'], 'admin' => 0,'lang' => 'de', 'userMods'  => $userMods); //german!
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);
		$settings['beUserController'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Backend user '.$user.' (ID: '.$settings['beUserController'].' and password: '.$user.') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		//author
		$userMods = 'txt3mM0,txt3mM0_txt3mM2,txt3mM0_txt3mM3'; // can create and send
		$user = 't3mautor';
		$insertArray = array('pid' => 0,'username' => $user,'password' => md5($user),'disable' => 0,'usergroup' => $settings['beGroupAuthors'], 'admin' => 0,'lang' => 'de', 'userMods'  => $userMods); //german!
		$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('be_users', $insertArray);
		$settings['beUserAuthor'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$out .= 'Backend user '.$user.' (ID: '.$settings['beUserAuthor'].' and password: '.$user.') '.$GLOBALS['LANG']->getLL('Created').'! <br/>';

		return $out;
	}
}

?>