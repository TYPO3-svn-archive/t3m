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
 * Class for handling addresses
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_addresses {

	/**
	* php4 constructor
	*
	* @return	string	given name of the object (no purpose right now))
	*/
// 	function tx_t3m_addresses($name)	{
// 		tx_t3m_addresses::__construct($name);
// 		return true;
// 	}
	/**
	* php5 constructor
	*
	* @return	string	given name of the object (no purpose right now))
	*/
// 	function __construct($name)	{
// 		$this->name = strip_tags($name);
// 		return $this->name;
// 	}


	/**
	* Integrates csv import (and EXT:rs_userimp extension for superadmins)
	*
	* @return	string	content for csv import (and from from EXT:rs_userimp for superadmins)
	*/
// 	function import()	{
// 	}

	/**
	 * Integrates csv import
	 *
	 * @return	string		content for csv import
	 * @todo	 Test and implement loading of a cvs via url
	 */
	function importReceivers() {

		//csv file
		$columnsOnly = '';
		$defVals = '&defVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvfile&defVals[tx_tcdirectmail_targets][title]='.$GLOBALS['LANG']->getLL('csvfile').'&defVals[tx_tcdirectmail_targets][csvfields]=name,email&defVals[tx_tcdirectmail_targets][csvseparator]=,&defVals[tx_tcdirectmail_targets][csvfilename]=t3m-example-cvs.txt';
		$overrideVals = '&overrideVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvfile';
		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['receivers_Sysfolder'].']=new'.$defVals.$columnsOnly;
		$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('csvfile').'</a><br/>';

		//csv url did not work for me so i do not include it here
// 		$columnsOnly = '';
// 		$defVals = '&defVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvurl&defVals[tx_tcdirectmail_targets][title]='.$GLOBALS['LANG']->getLL('csvurl');
// 		$overrideVals = '&overrideVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvurl';
// 		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['tcdirectmail_Sysfolder'].']=new'.$defVals.$columnsOnly;
// 		$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('csvurl').'</a><br/>';

		//csv list
		$columnsOnly = '';
		$defVals = '&defVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvlist&defVals[tx_tcdirectmail_targets][title]='.$GLOBALS['LANG']->getLL('csvlist').'&defVals[tx_tcdirectmail_targets][csvfields]=name,email&defVals[tx_tcdirectmail_targets][csvvalues]=foo,bar@localhost&defVals[tx_tcdirectmail_targets][csvseparator]=,';
		$overrideVals = '&overrideVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvlist';
		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['receivers_Sysfolder'].']=new'.$defVals.$columnsOnly;
		$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('csvlist').'</a><br/>';

		return $out;
	}

	/**
	 * Integrates rs_userimp extension for superadmins
	 *
	 * @return	string		content for csv import from EXT:rs_userimp for superadmins
	 * @todo	Integrate the extension better so it works for non-admin beusers (security?)
	 * @todo 	Create a profile-file for import for Oultook, Firefox adress exports (A very simple demo cvs i already included)
	 */
	function importFeusers() {
		$out.= '<a href="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('rs_userimp').'mod1/index.php?SET[function]=1" title="CSV Import"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('rs_userimp').'mod1/moduleicon.gif', '').' />&nbsp;CSV User Import Tool</a>';

		// <a href="#" onclick="top.goToModule(\'tools_txrsuserimpM1\');this.blur();return false;" title="CSV Import Tool">

		return $out;

	}

	/**
	 * Returns a string with the sr_feuser_register's sysfolder where users are saved
	 *
	 * @return	string		table with the sr_feuser_register sysfolder where users are saved
	 */
	function getSubscriptionSysfolder()	{
		$out = tx_t3m_addresses::tableForSysfolder($this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value']);
		return $out;
	}

	/**
	 * Returns a table with the sr_feuser_register profile creation page
	 *
	 * @return	array		table with the sr_feuser_register profile creation page
	 */
	function subscriptionPage()	{
		//find via
		//1.pages: where $plugin.tx_srfeuserregister_pi1.registerPID is set via subscription Typoscript ['plugin.tx_srfeuserregister_pi1.registerPID']['value'] or
 		//2.tt_content: where list-type = sr_feuser_register_pi1 and select_key = CREATE
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.registerPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");
			$first = $tmpl->ext_prevPageWithTemplate($uid,$this->perms_clause);
			$ts = tx_t3m_settings::getTSConstants($first['uid']);
			$out .= $GLOBALS['LANG']->getLL('subscriptionpageFormfields').': <a href="'.$GLOBALS['BACK_PATH'].'sysext/tstemplate/ts/index.php?id='.$first['uid'].'&e[constants]=1">'.$this->iconImgEdit.'</a><br/><b>'.$ts['plugin.tx_srfeuserregister_pi1.formFields']['value'].'</b>';
			$out .= tx_t3m_mailings::page($uid);
		}
		return $out;
	}

	/**
	* Returns a string with the sr_feuser_register's templateFile
	*
	* @return	string	table with the sr_feuser_register profile creation page
	*/
	function subscriptionPageTemplate()	{
		if (!($this->rootTS['plugin.tx_srfeuserregister_pi1.file.templateFile']['value'])) {
			$file = 'EXT:sr_feuser_register/pi1/tx_srfeuserregister_pi1_css_tmpl.html'; // standard config file path
		} else {
			$file = $this->rootTS['plugin.tx_srfeuserregister_pi1.file.templateFile']['value'];
		}
		$out = tx_t3m_addresses::tableForFile($file);
		return $out;
	}

	/**
	 * Returns a table with the sr_feuser_register profile creation confirmation page
	 *
	 * @return	array		table with the sr_feuser_register profile creation confirmation page
	 */
	function subscriptionConfirmationPage()	{
		//find via
		//1. pages: where $plugin.tx_srfeuserregister_pi1.confirmPID is set via subscription Typoscript OR
		//2. tt_content: where list-type = sr_feuser_register_pi1 and select_key = '' (according to manual)
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.confirmPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$out = tx_t3m_mailings::page($uid);
		}
		return $out;
	}

	/**
	 * Returns a table with the sr_feuser_register profile creation confirmation page
	 *
	 * @return	array		table with the sr_feuser_register profile creation confirmation page
	 */
	function subscriptionEditPage()	{
		//find via
 		//1. pages: where $plugin.tx_srfeuserregister_pi1.confirmPID is set via subscription Typoscript OR
 		//2: tt_content: where list-type = sr_feuser_register_pi1 and select_key = 'EDIT' (according to manual)
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.editPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");
			$first = $tmpl->ext_prevPageWithTemplate($uid,$this->perms_clause);
			$ts = tx_t3m_settings::getTSConstants($first['uid']);
			$out .= 'Form fields: <a href="'.$GLOBALS['BACK_PATH'].'sysext/tstemplate/ts/index.php?id='.$first['uid'].'&e[constants]=1">'.$this->iconImgEdit.'</a><br/><b>'.$ts['plugin.tx_srfeuserregister_pi1.formFields']['value'].'</b>';
			$out .= tx_t3m_mailings::page($uid);
		}
		return $out;
	}

	/**
	 * Returns a table with the feuser login page
	 *
	 * @return	array		table with the feuser login page
	 */
	function loginPage()	{
		//find via
 		//1. pages: where $plugin.tx_srfeuserregister_pi1.confirmPID is set via subscription Typoscript OR
 		//2: tt_content: where CType = login and select_key = 'EDIT' (according to manual)
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.loginPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$out = tx_t3m_mailings::page($uid);
		}
		return $out;
	}

	/**
	 * Returns a group and an edit button
	 *
	 * @param	int		$gid: frontend group uid
	 * @return	string		a table for a group and an edit button
	 */
	function tableForFeGroup($gid)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td></tr>';
		if (!$gid) {
			$out = $GLOBALS['LANG']->getLL('errorNoGroup');
		} else {
			$out .= '<tr><td>'.tx_t3m_addresses::getGroupName($gid).'</td>';
			$out .= '<td>'.tx_t3m_addresses::editGroup($gid).'</td>';
			$out .= '<td>'.tx_t3m_stats::countUsers($gid).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns array with uids of the fe_users of a fe_group
	 *
	 * @param	int		$gid: frontend group uid
	 * @return	array		with uids of the fe_users
	 */
	function getFeGroupUsers($gid)	{
		// get all fe_users
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,username,usergroup,email',
			'fe_users',
			'deleted=0' //' AND disable=0'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$userGroups[$row['uid']] = explode(',',$row['usergroup']);
			// check if the user is in that group and if so add him to the array
			if (in_array(intval($gid), $userGroups[$row['uid']])) {
				$userArray[] = $row;
			}
		}
// 		t3lib_div::debug($userArray);
		return $userArray;
	}

	/**
	 * Returns array with uids of the be_users of a be_group
	 *
	 * @param	int		$gid: backend group uid
	 * @return	array		with uids of the be_users
	 */
	function getBeGroupUsers($gid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,username,usergroup,email',
			'be_users',
			'deleted=0' //' AND disable=0'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$userArrayAll[$row['uid']] = explode(',',$row['usergroup']);

			// check if the user is in that group and if so add him to the array
			if (in_array(intval($gid), $userArrayAll[$row['uid']])) {
				$userArray[] = $row;
			}
		}
		return $userArray;
	}


	/**
	 * Returns a name (title) for a group
	 *
	 * @param	int		$gid: backend group uid
	 * @return	string		a name (title) for a group (e.g. the group name sr_feuser_register uses for pending users)
	 */
	function getGroupName($gid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'title',
			'fe_groups',
			'uid="'.intval($gid).'"'
			);
		if ((!$gid) || (!$res)) {
			$out = $GLOBALS['LANG']->getLL('errorNoGroup');
		} else {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$out = $row['title'];
		}
		return $out;
	}


	/**
	 * Returns a name (title) for a targetgroup
	 *
	 * @param	int		$gid: targetgroup uid
	 * @return	string		a name (title) for a group (e.g. the group name sr_feuser_register uses for pending users)
	 */
	function getTargetGroupName($gid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'name',
			'tx_t3m_targetgroups',
			'uid='.intval($gid)
			);
		if ((!$gid) || (!$res)) {
			$out = $GLOBALS['LANG']->getLL('errorNoGroup');
		} else {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$out = $row['name'];
		}
		return $out;
	}


	/**
	 * Returns a name (title) for a Receivergroup
	 *
	 * @param	int		$gid: Receivergroup uid
	 * @return	string		a name (title) for a group (e.g. the group name sr_feuser_register uses for pending users)
	 */
	function getReceivergroupName($gid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'title',
			'tx_tcdirectmail_targets',
			'uid='.intval($gid)
			);
		if ((!$gid) || (!$res)) {
			$out = $GLOBALS['LANG']->getLL('errorNoGroup');
		} else {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$out = $row['title'];
		}
		return $out;
	}

	/**
	 * Returns stats for users
	 *
	 * @return	string		a table with stats for users
	 * @todo	Move actions to the more global "handler" function
	 */
	function getGroups()	{
		global $LANG;
// 		$out = '<h3><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />';
		if (!$_REQUEST['group']) { // should go into 'handlepostvars', i know
			$gid = $this->rootTS['plugin.tx_srfeuserregister_pi1.userGroupUponRegistration']['value'];
			$_REQUEST['group'] = 'pending'; // setting this so that the selector shows right value, could be done there too.. :-/
		} else {
			switch($_REQUEST['group'])	{
				case pending:
					$gid = $this->rootTS['plugin.tx_srfeuserregister_pi1.userGroupUponRegistration']['value'];
				break;
				case subscriptions:
					$gid = $this->rootTS['plugin.tx_srfeuserregister_pi1.userGroupAfterConfirmation']['value'];
				break;
				case tests:
					$gid = $this->myConf['groupTest'];
				break;
				case deregistrations:
					$gid = $this->myConf['groupDeregistrations'];
				break;
				case blocked:
					$gid = $this->myConf['groupBlocked'];
				break;
				case softbounces:
					$gid = $this->myConf['groupSoftbounces'];
				break;
				case hardbounces:
					$gid = $this->myConf['groupHardbounces'];
				break;
			}
		}
// 		$out.='</h3>';
// 		$out.=$LANG->getLL($_REQUEST['group']);
		$out.=tx_t3m_addresses::formGroupSelector();
		$out.='<br /><h3>'.$LANG->getLL('Group').'</h3>';
		$out.=tx_t3m_addresses::tableForFeGroup($gid);
		$out.='<br /><h3>'.$LANG->getLL('User').'</h3>';
		$out.=tx_t3m_addresses::tableForFeGroupUsers($gid);
		if ($_REQUEST['group'] == 'blocked') { // quick hack to shwo disabled users too.
			$out.='<br /><h3>'.$LANG->getLL('disabledUsers').'</h3>';
			$out.=tx_t3m_addresses::getDisabledUsers();
		}

		return $out;
	}

	/**
	 * Returns group selector
	 *
	 * @return	string		group selector
	 */
	function formGroupSelector()	{
                $out .= '<select onchange="document.location=\'index.php?group=\' + options[selectedIndex].value">';  //&SET[function]=group is set anyway
		$groups = Array (
			'pending' => $GLOBALS['LANG']->getLL('pending'),
			'subscriptions' => $GLOBALS['LANG']->getLL('subscriptions'),
			'tests' => $GLOBALS['LANG']->getLL('testGroup'),
// 			'deregistrations' => $GLOBALS['LANG']->getLL('deregistrations'), // i would need to change sr_feuser_register not to delete but to keep users
			'blocked' => $GLOBALS['LANG']->getLL('blocked'),
			'softbounces' => $GLOBALS['LANG']->getLL('softbounces'),
			'hardbounces' => $GLOBALS['LANG']->getLL('hardbounces'),
		);
// 		ksort ($groups);
		foreach ($groups as $key => $val) {
			if ($_REQUEST['group'] == $key) {
				$out .= '<option selected value="'.$key.'">'.$val.'</option>';
			} else {
				$out .= '<option value="'.$key.'">'.$val.'</option>';
			}
		}
                $out .= '</select>';
		return $out;
	}


	/**
	 * Returns targetgroup selector
	 *
	 * @param	int		$uid: targetgroup id
	 * @return	string		targetgroup selector link
	 */
	function formTargetgroupSelector($uid)	{
		$out = '<a href="#" onclick="document.location=\'index.php?targetgroup=\' + '.intval($uid).'">'.tx_t3m_addresses::getTargetgroupName(intval($uid)).'</a>';
		return $out;
	}

	/**
	 * Returns receivergroup selector
	 *
	 * @param	int		$uid: targetgroup id
	 * @return	string		receivergroup selector link
	 */
	function formReceivergroupSelector($uid)	{
		$out = '<a href="#" onclick="document.location=\'index.php?receivergroup=\' + '.intval($uid).'">'.tx_t3m_addresses::getReceivergroupName(intval($uid)).'</a>';
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function getSalutations()	{
		$table = 'tx_'.$this->extKey.'_salutations';
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.1').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.2').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.0').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td></tr>'; // maybe use tx_t3m_salutations.single_female
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,single_female,single_male,plural',
			$table,
			'deleted=0 AND hidden=0',
			'',
			''
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.$row['name'].'</td>
				<td>'.$row['single_female'].'</td>
				<td>'.$row['single_male'].'</td>
				<td>'.$row['plural'].'</td>
				<td>'.tx_t3m_addresses::editSalutation($row['uid']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}


	/**
	 * Returns a link for editing a group
	 *
	 * @param	int		$uid: fegroup uid
	 * @return	string		a link for editing a group (e.g. the group sr_feuser_register uses for pending users)
	 */
	function editGroup($uid)	{
		$params = '&edit[fe_groups]['.intval($uid).']=edit';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a>';
		return $out;
	}


	/**
	 * Creates a link for editing a fe_user
	 *
	 * @param	int		$uid: user uid
	 * @return	string		form for editing a fe_user
	 */
	function editUser($uid)	{
		$columnsOnly = $this->columnsOnlyFeuser;
		$defVals = $this->defValsFeuser;
		$params = '&edit[fe_users]['.intval($uid).']=edit'.$defVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a>';
		return $out;
	}


	/**
	* Creates a link for editing a fe_user
	*
	* @return	string	form for editing a fe_user
	*/
// 	function deleteUser($uid)	{
// 		$out = '<a href="'.$GLOBALS['BACK_PATH'].'tce_db.php?cmd[fe_users]['.intval($uid).'][delete]=1">
// 			<img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users__h.gif" title="'.$GLOBALS['LANG']->getLL('Delete').'"/></a>';
// 		return $out;
// 	}
	/**
	* Creates a link for editing a fe_user
	*
	* @return	string	form for editing a fe_user
	*/
// 	function deleteUser($uid)	{
// 		$out = '<a href="#" onclick="return deleteRecord(\'fe_users\',\'15\',\'stefkoch\');">
// 			<img src="'.$GLOBALS['BACK_PATH'].'gfx/delete_record.gif" title="'.$GLOBALS['LANG']->getLL('Delete').'"/></a>';
// 		return $out;
// 	}



	/**
	 * Returns a link for editing a targetgroup
	 *
	 * @param	int		$uid: targetgroup uid
	 * @return	string		a link for editing a group (e.g. the group sr_feuser_register uses for pending users)
	 */
	function editTargetgroup($uid)	{
		$table = 'tx_'.$this->extKey.'_targetgroups';
		$columnsOnly = '&columnsOnly=name,gender,age_from,age_to,zip,country,categories_uid,description';
		$params = '&edit['.$table.']['.intval($uid).']=edit'.$columnsOnly;
		$out ='<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'" title="'.$GLOBALS['LANG']->getLL('Edit').'">'.$this->iconImgEdit.'</a>';
		return $out;
	}

	/**
	 * Returns links for exports of users and groups
	 *
	 * @return	string		links for exports of users and groups
	 * @todo	Create individual CSV-file exports for targetgroups
	 */
	function export()	{
		$out .='<a href="'.$GLOBALS['BACK_PATH'].'db_list.php?id='.$this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value'].'&table=fe_users&sortField=username&csv=1&returnUrl=http://'.t3lib_div::getIndpEnv('TYPO3_HOST_ONLY').t3lib_div::getIndpEnv('SCRIPT_NAME').'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/fileicons/csv.gif" title="'.$GLOBALS['LANG']->getLL('Export').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('export').'</a>';
		return $out;
	}

	/**
	* Shows the receiver groups
	*
	* @return	string	table with receiver groups
	*/
// 	function getGroups()	{
// 		return true;
// 	}

	/**
	 * Shows the receiver group definitions, a table with gender(fe_users:gender), age range(fe_users:date_of_birth), fe_users:zone (e.g. bundesland, state), fe_users:zip (plz), selectorbox for fe_groups, big selectorbox for fe_users
	 *
	 * @return	string		table with receiver group definitions
	 * @todo	Add zone support and keep it consistent with zip-codes (tricky!) as well as reduce the possible selection corresponding to a selected country-code (tricky, too)!)
	 */
	function getTargetgroupDefinitions()	{
		$out = '<table class="typo3-dblist">';

		$columns = 'name,gender,age_range,country,zip,categories,Edit,countUsers'; //zone,salutation,
		$columnsArray = explode(',',$columns);

		$out .= '<tr class="c-headLineTable">';
		foreach($columnsArray as $value) {
			$out .= '<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL($value).'</td>';
		}
		$out .= '<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('createReceiverlist').'</td></tr>';

		$resTargetgroups = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,gender,age_from,age_to,country,zip,categories_uid,calculated_receivers', //,zone,salutations_uid
			'tx_'.$this->extKey.'_targetgroups',
			'deleted=0 AND hidden=0',
			'',
			'calculated_receivers DESC'
			);
		while($rowTargetgroup = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTargetgroups))	{
			$out .= '<tr>';
			$rowTargetgroup['name'] = tx_t3m_addresses::formTargetgroupSelector($rowTargetgroup['uid']);

			$rowTargetgroup['gender'] = $GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$rowTargetgroup['gender']);

			if ($rowTargetgroup['age_to'] == intval(0)) {
				$rowTargetgroup['age_range'] = '';
			} else {
				$rowTargetgroup['age_range'] = $rowTargetgroup['age_from'].'-'.$rowTargetgroup['age_to'];
			}

			$resCountry = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'cn_iso_2',
				'static_countries',
				'uid = '.$rowTargetgroup['country']
				);
			$rowCountry = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCountry);
			$rowTargetgroup['country'] = $rowCountry['cn_iso_2'];

// 			$resZone = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
// 				'zn_code',
// 				'static_country_zones',
// 				'uid = '.$rowTargetgroup['zone']
// 				);
// 			$rowZone = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resZone);
// 			$rowTargetgroup['zone'] = $rowZone['zn_code'];

// 			$resSalutation = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
// 				'name',
// 				'tx_'.$this->extKey.'_salutations',
// 				'uid = '.$rowTargetgroup['salutations_uid']
// 				);
// 			$rowSalutation = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resSalutation);
// 			$rowTargetgroup['salutation'] = $rowSalutation['name'];


			$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,name',
				'tx_'.$this->extKey.'_categories',
				'uid = '.implode(' OR uid = ',explode(',',$rowTargetgroup['categories_uid']))
				);
			while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
				$rowTargetgroup['categories'] .= $rowCategories['name'].'<br />';
			}
// 			$rowTargetgroups['categories'] = $rowTargetgroupsWhere; //$rowTargetgroups['categories_uid'];

			$rowTargetgroup['Edit'] = tx_t3m_addresses::editTargetgroup($rowTargetgroup['uid']);

			$rowTargetgroup['countUsers'] = $rowTargetgroup['calculated_receivers'];

// 			$rowTargetgroup['userids'] = implode('<br/>',tx_t3m_addresses::getTargetgroupUsers($rowTargetgroup['uid']));

			foreach($columnsArray as $value) {
				$out .= '<td>'.$rowTargetgroup[$value].'</td>';
			}


// 			$out .= '<td>';
// 			// fetch all categories assigned to this targetgroup
// 			$resTargetgroupsCategoriesMM = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
// 				'targetgroups_uid,categories_uid',
// 				'tx_'.$this->extKey.'_targetgroups_categories_mm',
// 				'targetgroups_uid = '.$rowTargetgroups['uid'],
// 				'',
// 				''
// 				);
// 			while($rowTargetgroupsCategoriesMM = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTargetgroupsCategoriesMM))	{
// 				$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
// 					'uid, name',
// 					'tx_'.$this->extKey.'_categories',
// 					'uid = '.$rowTargetgroupsCategoriesMM['categories_uid'],
// 					'',
// 					''
// 					);
// 				while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
// 					$out .= $rowCategories['name'].'&nbsp;';
// 				}
// 			}
// 			$out .= '</td>';
// 			$out .= '<td>2do: Editlink</td>';
// 			$out .= '<td>2do: Usercount</td>';
// 			$out .= '<td>2do: Deletelink</td>';

			$out .= '<td>'.tx_t3m_addresses::createReceiverlistForTargetgroup($rowTargetgroup['uid']).'</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Shows the users
	 *
	 * @param	int		uid of targetgroup
	 * @return	array		list of receivers
	 */
	function getTargetgroupUsers($uid) {
		$myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m']); //need this for calls from outside t3m-module
		$resTargetgroups = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,gender,age_from,age_to,country,zone,zip,salutations_uid,categories_uid',
			'tx_t3m_targetgroups',
			'uid='.intval($uid).' AND deleted=0 AND hidden=0'
			);
		while($rowTargetgroup = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTargetgroups))	{
			$userArray = NULL;
			// check if values are set in targetgroup and if set make it a requirement
// 			$where = '';
			if (($rowTargetgroup['gender'] == 0 ) || ($rowTargetgroup['gender'] == 1 )) {
				$where[] = 'gender = '.$rowTargetgroup['gender'];
			}
			if ($rowTargetgroup['country'] != 0 ) {
				$resCountry = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'cn_iso_2',
				'static_countries',
				'uid = '.$rowTargetgroup['country']
				);
				$rowCountry = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCountry);
				$where[] = '(static_info_country = \''.$rowCountry['cn_iso_2'].'\' OR tx_t3m_country = \''.$rowCountry['cn_iso_2'].'\' OR country = \''.$rowCountry['cn_iso_2'].'\')';

			}
			if ($rowTargetgroup['zone'] != 0 ) {
				$resZone = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'zn_code',
				'static_country_zones',
				'uid = '.$rowTargetgroup['zone']
				);
				$rowZone = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resZone);
				$where[] = 'zone = \''.$rowZone['zn_code'].'\'';
			}
			if ($rowTargetgroup['age_from'] != 0 ) {
				$where[] = 'date_of_birth > '.tx_t3m_addresses::getTimestampFromAge($rowTargetgroup['age_from']);
			}
			if ($rowTargetgroup['age_to'] != 0 ) {
				$where[] = 'date_of_birth < '.tx_t3m_addresses::getTimestampFromAge($rowTargetgroup['age_to']);
			}
			if ($rowTargetgroup['zip'] != 0 ) {
				$where[] = 'zip LIKE \''.$rowTargetgroup['zip'].'%\'';
			}
			$whereString = implode(' AND ',$where).' AND deleted=0 AND disable=0';

			$where = '';
// 			t3lib_div::debug($whereString);
			$resFeUsers = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,email,tx_t3m_categories,module_sys_dmail_html,usergroup',
				'fe_users',
				$whereString
			);
			$i = 0;
			while($rowFeUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resFeUsers))	{
				if (in_array(intval($myConf['groupBlocked']),explode(',',$rowFeUser['usergroup']))) { // do not add if user is in robinson (blocked) group
				} else {
					if ($rowTargetgroup['categories_uid'] != '' ) { //user has chosen at least one category
						$categoriesTargetgroup = explode(',',$rowTargetgroup['categories_uid']);
	// 					t3lib_div::debug($categoriesTargetgroup);
						$categoriesUser = explode(',',$rowFeUser['tx_t3m_categories']);
	// 					t3lib_div::debug($categoriesUser);
						foreach($categoriesUser as $category) {
							if (in_array($category, $categoriesTargetgroup)) {
								$userArray[$i]['uid'] = $rowFeUser['uid'];
								$userArray[$i]['email'] = $rowFeUser['email'];
								$userArray[$i]['plain_only'] = !$rowFeUser['module_sys_dmail_html'];
							}
						}
					} else { //user has chosen no category
						$userArray[$i]['uid'] = $rowFeUser['uid'];
						$userArray[$i]['email'] = $rowFeUser['email'];
						$userArray[$i]['plain_only'] = !$rowFeUser['module_sys_dmail_html'];
					}
					$i++;
				}
			}
		}
		return $userArray;
	}


	/**
	 * Get users for a receivergroup
	 *
	 * @param	int		uid of the receivergroup
	 * @return	array		user array
	 * @todo	Integrate original tcdirectmail classes in order to stay compatible rather than work on DB directly
	 */
	function getReceivers($uid) {
		if ($uid) { // update single targetgroup
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_tcdirectmail_targets',
				'uid ='.$uid
				);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			switch ($row['targettype']) {
				case 'tx_t3m_target1':
					$targetgroups = explode(',',$row['tx_t3m_target']);
					foreach ($targetgroups as $targetgroup) {
						$tmpusers = tx_t3m_addresses::getTargetgroupUsers($targetgroup);
						foreach ($tmpusers as $tmpuser) {
							if(!(in_array($tmpuser,$users))) { //is not already in array, so no dupe here
								$users[] = $tmpuser;
							}
						}
					}
					$out = $users;
				break;
				case 'tx_tcdirectmail_target_fegroups':
					$groups = explode(',',$row['fegroups']);
					foreach ($groups as $group) {
// 						$users[] = tx_t3m_addresses::getFeGroupUsers($group);
						foreach (tx_t3m_addresses::getFeGroupUsers($group) as $key => $val) {
							if (!(in_array($val, $userArray))) {
								$userArray[] = $val;
							}
// 							$uids[] = $val;
						}
					}
// 					array_unique($users); // get rid of multiple users
					$out = $userArray;
				break;
				case 'tx_tcdirectmail_target_beusers':
					$uids = explode(',',$row['beusers']);
					array_unique($uids); // get rid of multiple users
					foreach ($uids as $uid) {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'username,email',
							'be_users',
							'uid ='.$uid
						);
						$users[] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
					$out = $users;
				break;
				case 'tx_tcdirectmail_target_fepages':
					$pages = explode(',',$row['fepages']);
					foreach ($pages as $page) {
						$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'username,name,email',
							'fe_users',
							'deleted=0 AND pid = '.$page
						);
						while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
							$users[] = $row2;
						}
					}
					$out = $users;
				break;
// 				case 'tx_tcdirectmail_target_csvfile': //no thanks
// 				break;
				case 'tx_tcdirectmail_target_csvlist': //stole code from daniel
					$sepchar = $row['csvseparator']?$row['csvseparator']:',';
					$fields = array_map('trim', explode ($sepchar, $row['csvfields']));
					$lines = explode ("\n", $row['csvvalues']);
					foreach ($lines as $line) {
						$values = explode($sepchar, $line);
						foreach ($values as $i => $value) {
						$user[$fields[$i]] = trim($value);
						}
						$users[] = $user;
					}
					$out = $users;
				break;
// 				case 'tx_tcdirectmail_target_csvurl': //no thanks
// 				break;
				default:
// 					return 'Not supported so far';
				break;
			}
		} else { // update all of our targetgroups
			$out = $GLOBALS['LANG']->getLL('errorNoReceiverlist');
		}
// 		t3lib_div::debug($out);
		return $out;
	}


	/**
	 * Returns an epoch timestamp as birthdate for an integer age
	 *
	 * @param	int		$age: unix timestamp
	 * @return	int		epoch timestamp as birthdate for an integer age
	 */
	function getTimestampFromAge($age) {
		//1 year: 365 days, 5 hours, 49 minutes = 31536000 + 18000 + 2940 = 31556940
		$out = (time() - (intval($age) * 31556940));
		return $out;
	}


	/**
	* Shows the users of a page
	*
	* @return	string	table with receiver groups
	*/
// 	function getUsers($pid)	{
// 		$pid = $this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value'];
// 		if (!$pid) {
// 			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
// 		} else {
// 			$out = '<br/>Page with all pending and registered users: <a href="'.$GLOBALS['BACK_PATH'].'db_list.php?id='.$pid.'&table=fe_users">all users of srfeuserregister</a>';
// 		}
// 		return $out;
// 	}


	/**
	 * Returns user ids who chose a category
	 *
	 * @param	int		uid of category
	 * @return	array		users who chose a category
	 * @todo	Traversal through subcategories
	 */
 	function getCategoryUsers($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,tx_t3m_categories,username,gender,email,date_of_birth',
			'fe_users'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			if ($row['tx_t3m_categories'] != '' ) { //user has chosen at least one category
				$categories = explode(',',$row['tx_t3m_categories']);
				if (in_array($uid, $categories)) {
					$out[] = $row;
				}
			}
		}
		return $out;
	}


	/**
	 * Returns a list of users of a fegroup and editlinks here
	 *
	 * @param	int		uid of fe_group
	 * @return	string		links for editing a list of the users of a group
	 */
	function tableForFegroupUsers($gid)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('gender').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('email').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('date_of_birth').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_categories').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
			</tr>';
//  			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Delete').'</td>
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,username,usergroup,gender,email,date_of_birth,tx_t3m_categories,disable',
			'fe_users',
			'deleted=0' //' AND disable=0'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$userArrayAll[$row['uid']] = explode(',',$row['usergroup']);
			if (in_array(intval($gid), $userArrayAll[$row['uid']])) {
				$userArray[] = $row['uid'];
				$out .= '<tr><td>'.$row['username'].'</td>
					<td>'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$row['gender']).'</td>
					<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
					<td>';
				if (intval($row['date_of_birth']) != 0) {
					$out .= date('d-m-Y',intval($row['date_of_birth'])); //t3lib_BEfunc::date
				}
				$out .= '</td><td>';
				$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid,name',
					'tx_'.$this->extKey.'_categories',
					'uid = '.implode(' OR uid = ',explode(',',$row['tx_t3m_categories']))
				);
				while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
					$out .= $rowCategories['name'].'<br />';
				}
				$out .= '</td><td>'.tx_t3m_addresses::editUser($row['uid']).'</td>
					</tr>';
// 					<td>'.tx_t3m_addresses::deleteUser($row['uid']).'</td>
			}
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns a list of users and editlinks here
	 *
	 * @param	array		$users: users with data
	 * @return	string		links for editing a list of the users
	 */
	function tableForFeusers($users)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('gender').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('email').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('date_of_birth').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_categories').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
			</tr>';
		foreach ($users as $row) {
			$out .= '<tr><td>'.$row['username'].'</td>
				<td>'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$row['gender']).'</td>
				<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
				<td>';
			if (intval($row['date_of_birth']) != 0) {
				$out .= date('d-m-Y',intval($row['date_of_birth'])); //t3lib_BEfunc::date
			}
			$out .= '</td><td>';
			$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,name',
				'tx_'.$this->extKey.'_categories',
				'uid = '.implode(' OR uid = ',explode(',',$row['tx_t3m_categories']))
			);
			while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
				$out .= $rowCategories['name'].'<br />';
			}
			$out .= '</td><td>'.tx_t3m_addresses::editUser($row['uid']).'</td>
				</tr>';
// 				<td>'.tx_t3m_addresses::deleteUser($row['uid']).'</td>
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns a list of users who chose a category
	 *
	 * @param	array		$users: users with data
	 * @return	string		list of users
	 */
	function tableForCategoryFeusers($users)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('gender').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('email').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('date_of_birth').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_categories').'</td>
			</tr>'; //<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
		foreach ($users as $row) {
			$out .= '<tr><td>'.$row['username'].'</td>
				<td>'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$row['gender']).'</td>
				<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
				<td>';
			if (intval($row['date_of_birth']) != 0) {
				$out .= date('d-m-Y',intval($row['date_of_birth'])); //t3lib_BEfunc::date
			}
			$out .= '</td><td>';
			$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,name',
				'tx_'.$this->extKey.'_categories',
				'uid = '.implode(' OR uid = ',explode(',',$row['tx_t3m_categories']))
			);
			while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
				$out .= $rowCategories['name'].'<br />';
			}
			$out .= '</td></tr>';
				// <td>'.tx_t3m_addresses::editUser($row['uid']).'</td>	<td>'.tx_t3m_addresses::deleteUser($row['uid']).'</td>
		}
		$out .= '</table>';
		return $out;
	}






	/**
	 * Returns a list of users who are disabled
	 *
	 * @return	string		links for editing a list of the users of a group
	 */
	function getDisabledUsers() {
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('gender').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('email').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('date_of_birth').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_categories').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
			</tr>';
//  			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Delete').'</td>
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,username,usergroup,gender,email,date_of_birth,tx_t3m_categories,disable',
			'fe_users',
			'deleted=0 AND disable=1'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.$row['username'].'</td>
				<td>'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$row['gender']).'</td>
				<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
				<td>';
			if (intval($row['date_of_birth']) != 0) {
				$out .= date('Y-m-d',intval($row['date_of_birth']));
			}
			$out .= '</td><td>';
			$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,name',
				'tx_'.$this->extKey.'_categories',
				'uid = '.implode(' OR uid = ',explode(',',$row['tx_t3m_categories']))
			);
			while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
				$out .= $rowCategories['name'].'<br />';
			}
			$out .= '</td><td>'.tx_t3m_addresses::editUser($row['uid']).'</td>
				</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns a list of receiver definitions
	 *
	 * @param	int		uid of page where to find the receivers (not used yet)
	 * @return	string		table with receiver lists
	 */
	function getReceiverlists()	{

		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Type').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title,targettype,calculated_receivers,tx_t3m_target',
			'tx_tcdirectmail_targets',
			'deleted=0 AND hidden=0', //' AND disable=0'
			'',
			'calculated_receivers DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.tx_t3m_addresses::formReceivergroupSelector($row['uid']).'</td>
				<td>';
			if ($GLOBALS['LANG']->getLL($row['targettype'])) {
				$out .= $GLOBALS['LANG']->getLL($row['targettype']);
			} else {
				$out .= $row['targettype'];
			}
			$out .='</td>
				<td>';
			$supportedTargettypes = array(
				'tx_t3m_target1',
				'tx_tcdirectmail_target_fegroups',
				'tx_tcdirectmail_target_fepages',
				'tx_tcdirectmail_target_beusers',
				'tx_tcdirectmail_target_csvlist'
				);
			if (in_array($row['targettype'],$supportedTargettypes)) {
				$out .= $row['calculated_receivers'];
			} else {
				$out .= $GLOBALS['LANG']->getLL('notsupported');
			}
			$out .= '</td>
				<td>'.tx_t3m_addresses::editReceivergroup($row['uid']).'</td></tr>';
			$count = '';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Update usercount for our receivergroups
	 *
	 * @param	int		uid of the receivergroup to be updated
	 * @return	boolean		true if it went ok
	 */
	function updateCalculatedReceivers($uid) {

		if ($uid) { // update single targetgroup
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_tcdirectmail_targets',
				'uid ='.$uid
				);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			switch ($row['targettype']) {
				case 'tx_t3m_target1':
					$targetgroups = explode(',',$row['tx_t3m_target']);
					foreach ($targetgroups as $targetgroup) {
						$tmpusers = tx_t3m_addresses::getTargetgroupUsers($targetgroup);
						foreach ($tmpusers as $tmpuser) {
							if(!(in_array($tmpuser,$users))) { //is not already in array, so no dupe here
								$users[] = $tmpuser;
							}
						}
					}
// 					t3lib_div::debug($users);
					$count = count($users);
				break;
				case 'tx_tcdirectmail_target_fegroups':
					$groups = explode(',',$row['fegroups']);
					foreach ($groups as $group) {
						foreach (tx_t3m_addresses::getFeGroupUsers($group) as $key => $val) {
							$uids[] = $key['uid'];
// 							$uids[] = $val['uid'];
						}
					}
					array_unique($uids); // get rid of multiple users
					$count = count($uids);
				break;
				case 'tx_tcdirectmail_target_beusers':
					$uids = explode(',',$row['beusers']);
					array_unique($uids); // get rid of multiple users
					$count = count($uids);
				break;
				case 'tx_tcdirectmail_target_fepages':
					$pages = explode(',',$row['fepages']);
					foreach ($pages as $page) {
						$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid',
							'fe_users',
							'deleted=0 AND pid = '.$page
						);
						while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
							$uids[] = $row2['uid'];
						}
					}
					array_unique($uids); // get rid of multiple users
					$count = count($uids);
				break;
				case 'tx_tcdirectmail_target_csvfile':
				break;
				case 'tx_tcdirectmail_target_csvlist':
// 					$fieldcount = count(explode(',',$row['csvfields']));
					$count = count(explode("\n",$row['csvvalues']));
				break;
				case 'tx_tcdirectmail_target_csvurl':
				break;

				default:
					return 'Nothing updated';
				break;
			}
			$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_tcdirectmail_targets SET calculated_receivers = '.$count.' WHERE uid = '.$row['uid']);
		} else { // update all of our targetgroups
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid',
				'tx_tcdirectmail_targets',
				'deleted=0 AND hidden=0 AND targettype = tx_t3m_target1' //' AND disable=0'
				);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$count = count(tx_t3m_addresses::getTargetgroupUsers($row['tx_t3m_target']));
				$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_tcdirectmail_targets SET calculated_receivers = '.$count.' WHERE uid = '.$row['uid']);
			}
		}
		return true;
	}

	/**
	 * Update usercount for a targetgroup
	 *
	 * @param	int		$uid: targetgrup uid
	 * @return	boolean		true if it went ok
	 */
	function updateCalculatedTargetgroupUsers($uid) {
		$count = count(tx_t3m_addresses::getTargetgroupUsers(intval($uid)));
		$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_t3m_targetgroups SET calculated_receivers = '.$count.' WHERE uid = '.intval($uid));
	}



	/**
	 * Update usercount for all targetgroups
	 *
	 * @param	int		$uid: category uid
	 * @return	boolean		true if it went ok
	 */
	function updateCalculatedCategory($uid) {
		$count = count(tx_t3m_addresses::getCategoryUsers(intval($uid)));
		$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_t3m_categories SET calculated_receivers = '.$count.' WHERE uid = '.intval($uid));
	}



	/**
	 * Update usercount for all receivergroups
	 *
	 * @return	boolean		true if it went ok
	 */
	function updateAllCalculatedReceivers() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_tcdirectmail_targets',
			'deleted=0'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			tx_t3m_addresses::updateCalculatedReceivers($row['uid']);
		}
		return true;
	}


	/**
	 * Update usercount for all targetgroups
	 *
	 * @return	boolean		true if it went ok
	 */
	function updateAllCalculatedTargetgroupUsers() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_t3m_targetgroups',
			'deleted=0'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			tx_t3m_addresses::updateCalculatedTargetgroupUsers($row['uid']);
		}
		return true;
	}


	/**
	 * Update usercount for all targetgroups
	 *
	 * @return	boolean		true if it went ok
	 */
	function updateAllCalculatedCategories() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_t3m_categories',
			'deleted=0'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			tx_t3m_addresses::updateCalculatedCategory($row['uid']);
		}
		return true;
	}



	/**
	 * Returns if user has email (true) or not (false)
	 *
	 * @param	int		uid of the fe user
	 * @return	boolean		has email (true) or not (false)
	 */
	function feUserHasEmail($uid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'email',
				'fe_users',
				'deleted=0 AND uid = '.intval($uid) //' AND disable=0'
				);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['email']) {
			$out = true;
		} else {
			$out = false;
		}
		return $out;
	}


	/**
	 * Returns if user has email (true) or not (false)
	 *
	 * @param	int		uid of the be user
	 * @return	boolean		has email (true) or not (false)
	 */
	function beUserHasEmail($uid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'email',
				'be_users',
				'deleted=0 AND uid = '.intval($uid) //' AND disable=0'
				);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['email']) {
			$out = true;
		} else {
			$out = false;
		}
		return $out;
	}


	/**
	 * Returns a link for editing a  receiver definition
	 *
	 * @param	int		uid of the receiver group
	 * @return	string		links for editing a receiver group
	 */
	function editReceivergroup($uid)	{
		$params = '&edit[tx_tcdirectmail_targets]['.intval($uid).']=edit';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
		return $out;
	}

	/**
	 * Creates a form to enter a new receiver group definition
	 *
	 * @return	string		form for entering a new receiver group definition
	 */
	function createReceiverlist()	{
// 		$columnsOnly = '&columnsOnly=targettype,title,plain_only,tx_t3m_target';
		$defVals = '&defVals[tx_tcdirectmail_targets][hidden]=0&defVals[tx_tcdirectmail_targets][targettype]=tx_t3m_target1';
		$overrideVals = '&overrideVals=[tx_tcdirectmail_targets][hidden]=0';
		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['receivers_Sysfolder'].']=new'.$defVals.$overrideVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>'.$GLOBALS['LANG']->getLL('newReceiverlist').'</a>';
		return $out;
	}

	/**
	 * Creates a form to enter a new receiver group definition
	 *
	 * @return	string		form for entering a new receiver group definition
	 */
	function createReceiverlistForTargetgroup($uid)	{
// 		$columnsOnly = '&columnsOnly=targettype,title,plain_only,tx_t3m_target';
		$defVals = '&defVals[tx_tcdirectmail_targets][hidden]=0&defVals[tx_tcdirectmail_targets][targettype]=tx_t3m_target1&defVals[tx_tcdirectmail_targets][tx_t3m_target]='.intval($uid);
		$overrideVals = '&overrideVals=[tx_tcdirectmail_targets][hidden]=0';
		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['receivers_Sysfolder'].']=new'.$defVals.$overrideVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>'.$GLOBALS['LANG']->getLL('newReceiverlist').'</a>';
		return $out;
	}

	/**
	 * Creates a form to enter a new receiver group definition
	 *
	 * @return	string		form for entering a new receiver group definition
	 */
	function createTargetgroupDefinition()	{
		$table = 'tx_'.$this->extKey.'_targetgroups';
		$columnsOnly = '&columnsOnly=name,gender,age_from,age_to,zip,country,categories_uid,description';
		$defVals = '&defVals['.$table.'][country]='.$this->myConf['static_countries_uid'].'&defVals['.$table.'][zone]='.$this->myConf['static_country_zones_uid'];
		$params = '&edit['.$table.']['.$this->myConf['targetgroups_Sysfolder'].']=new'.$defVals.$overrideVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_targetgroups.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>'.$GLOBALS['LANG']->getLL('newTargetgroupdefinition').'</a><br/>';
		return $out;
	}


	/**
	 * Creates a link for a new user for a page
	 *
	 * @param	int		sysfolder in which to create the user
	 * @return	string		link for a new user for a page
	 */
	function createFeuser($pid)	{
		if (!intval($pid)) {
			$pid = $this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value'];
		}
		$columnsOnly = $this->columnsOnlyFeuser;
		$defVals = $this->defValsFeuser.'&defVals=[fe_users][usergroup]='.$this->rootTS['plugin.tx_srfeuserregister_pi1.userGroupAfterConfirmation'].'&defVals[fe_users][tx_t3m_country]='.$this->myConf['static_countries_uid'];				;
		$params = '&edit[fe_users]['.intval($pid).']=new'.$defVals.$columnsOnly.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgCreate.'&nbsp;'.$GLOBALS['LANG']->getLL('NewUser').'</a><br/>';
		return $out;
	}

	/**
	 * Creates a link for a new user for a page
	 *
	 * @param	int		sysfolder in which to create the group
	 * @return	string		link for a new user for a page
	 */
	function createFegroup($pid)	{
		if (!intval($pid)) {
			$pid = $this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value'];
		}
		$params = '&edit[fe_groups]['.intval($pid).']=new'.$defVals.$columnsOnly.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgCreate.'&nbsp;'.$GLOBALS['LANG']->getLL('NewGroup').'</a><br/>';
		return $out;
	}

	/**
	* Creates a new content category
	*
	* @return	string	link for entering a new content category
	*/
	function createCategory()	{ //[0]
		$columnsOnly = '&columnsOnly=name';
		$defVals = '&defVals[tx_'.$this->extKey.'_categories][name]=NewCategory';
		$params = '&edit[tx_'.$this->extKey.'_categories]['.$this->myConf['categories_Sysfolder'].']=new'.$defVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_categories.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewCategory').'</a><br/>';
		return $out;
	}

	/**
	* Creates a new content category
	*
	* @return	string	link for entering a new content category
	*/
	function createSalutation()	{ //[0]
		$params = '&edit[tx_'.$this->extKey.'_salutations]['.$this->myConf['salutations_Sysfolder'].']=new';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_salutations.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewSalutation').'</a><br/>';
		return $out;
	}

	/**
	 * Creates a link for editing a content category
	 *
	 * @param	int		$uid: category uid
	 * @return	string		form for editing a content category
	 */
	function editCategory($uid)	{
		$params = '&edit[tx_'.$this->extKey.'_categories]['.intval($uid).']=edit';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a>';
		return $out;
	}

	/**
	 * Creates a link for editing a salutation
	 *
	 * @param	int		uid of the salutation
	 * @return	string		link for editing a salutation
	 */
	function editSalutation($uid)	{
		$params = '&edit[tx_'.$this->extKey.'_salutations]['.intval($uid).']=edit';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
		return $out;
	}

	/**
	 * Returns a table for Targetgroup Users
	 *
	 * @param	array		$tid: targetgroup id
	 * @return	string		a table for Targetgroup Users
	 */
	function tableForTargetgroupUsers($tid)	{
		$users = tx_t3m_addresses::getTargetgroupUsers(intval($tid));
		if (count($users) > 0) {

			foreach ($users as $user) {
				$where[] = 'uid = '.$user['uid'];
			}
			$whereString = ' AND ('.implode(' OR ',$where).')';

// 			t3lib_div::debug($whereString);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,username,usergroup,gender,email,date_of_birth,tx_t3m_categories,disable',
				'fe_users',
				'deleted=0 '.$whereString //' AND disable=0'
			);
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('gender').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('email').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('date_of_birth').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_categories').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
				</tr>';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$out .= '<tr><td>'.$row['username'].'</td>
					<td>'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$row['gender']).'</td>
					<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
					<td>';
				if (intval($row['date_of_birth']) != 0) {
					$out .= date('d-m-Y',intval($row['date_of_birth'])); //t3lib_BEfunc::date
				}
				$out .= '</td><td>';
				$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid,name',
					'tx_'.$this->extKey.'_categories',
					'uid = '.implode(' OR uid = ',explode(',',$row['tx_t3m_categories']))
				);
				while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
					$out .= $rowCategories['name'].'<br />';
				}
				$out .= '</td><td>'.tx_t3m_addresses::editUser($row['uid']).'</td>
					</tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nousers');
		}
		return $out;
	}

	/**
	 * Returns a table for Targetgroup Users
	 *
	 * @param	array		$tid: targetgroup id
	 * @return	string		a table for Targetgroup Users
	 */
	function tableForReceivers($uid)	{
		$users = tx_t3m_addresses::getReceivers(intval($uid));

		$columns = array_keys($users[0]);
		if (count($users) > 0) {

// 			foreach ($users as $user) {
// 				$where[] = 'uid = '.$user['uid'];
// 			}
// 			$whereString = ' AND ('.implode(' OR ',$where).')';

// 			t3lib_div::debug($whereString);
// 			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
// 				'uid,username,usergroup,gender,email,date_of_birth,tx_t3m_categories,disable',
// 				'fe_users',
// 				'deleted=0 '.$whereString //' AND disable=0'
// 			);
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">';
			foreach ($columns as $column) {
				$out .= '<td class="c-headLineTable">';
				if ($GLOBALS['LANG']->getLL($column)) {
					$out .= $GLOBALS['LANG']->getLL($column);
				} else {
					$out .= $column;
				}
			}
			$out .= '</tr>';
// 			$out .= '<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('gender').'</td>
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('email').'</td>
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('date_of_birth').'</td>
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('tx_t3m_categories').'</td>
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
// 				</tr>';
			foreach($users as $user) {
				$out .= '<tr>';
				foreach ($columns as $column) {
					$out .= '<td>'.$user[$column].'</td>';
				}
// 				$out .= '<tr><td>'.$row['username'].'</td>
// 					<td>'.$GLOBALS['LANG']->getLL('tx_t3m_targetgroups.gender.I.'.$row['gender']).'</td>
// 					<td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td>
// 					<td>';
// 				if (intval($row['date_of_birth']) != 0) {
// 					$out .= date('d-m-Y',intval($row['date_of_birth'])); //t3lib_BEfunc::date
// 				}
// 				$out .= '</td><td>';
// 				$resCategories = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
// 					'uid,name',
// 					'tx_'.$this->extKey.'_categories',
// 					'uid = '.implode(' OR uid = ',explode(',',$row['tx_t3m_categories']))
// 				);
// 				while($rowCategories = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategories))	{
// 					$out .= $rowCategories['name'].'<br />';
// 				}
				$out .= '</tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nousers');
		}
		return $out;
	}



	/**
	 * Creates a set of links for viewing, editing and deleting a file
	 *
	 * @param	string		$fileURL: where to find the file
	 * @return	string		table which include links to view, edit and delete the file
	 */
	function tableForFile($fileURL)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td></tr>';
		if ($fileURL) {
			$tmpEdit = 'target='.constant('PATH_site').$fileURL.'&returnUrl=http://'.t3lib_div::getIndpEnv('TYPO3_HOST_ONLY').t3lib_div::getIndpEnv('SCRIPT_NAME');
			$tmpEditHtml = htmlspecialchars($tmpEdit);
			$out .= '<tr><td>'.$fileURL.'</td>
				<td><a href="'.$GLOBALS['BACK_PATH'].'../'.$fileURL.'">'.$this->iconImgView.'</a></td>
				<td><a href="'.$GLOBALS['BACK_PATH'].'file_edit.php?'.$tmpEditHtml.'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif').' title="'.$GLOBALS['LANG']->getLL('Edit').'" alt="'.$GLOBALS['LANG']->getLL('Edit').'" /></a></td></tr>'; //edit_file is not skinned :-(
		} else { // no url given
			$out .= '<tr><td colspan=3>'.$GLOBALS['LANG']->getLL('errorSubscriptionExtension').'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Creates a table for a sysfolder (link for editing and showing stats)
	 *
	 * @param	int		$pid: page id
	 * @return	string		table data html tags which include links to view, edit and delete the page
	 */
	function tableForSysfolder($pid)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('NewUser').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,pid,title,subtitle,description,alias',
			'pages',
			'uid='.intval($pid),
			'',
			''
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr>
				<td>'.$row['title'].'</td>
				<td>'.tx_t3m_mailings::editPage($row['uid']).'</td>
				<td>'.tx_t3m_addresses::createUser($row['uid']).'</td>
				</tr>';
		}
		if (!$out) { // no db-result
			$out = '<tr><td colspan=3>'.$GLOBALS['LANG']->getLL('errorSubscriptionExtension').'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns users with invalid data
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
	 * Return table for editing users with no e-mail adress
	 *
	 * @return	table for user data with problems like no email address
	 * @todo	 apart from not having email, show bounced, but not yet disabled users?
	 */
	function maintenanceUsers() {
		$users = tx_t3m_addresses::getInvalidUsers(); //
		$out = tx_t3m_addresses::tableForFeusers($users);
// 		t3lib_div::debug($users);

		return $out;
	}

}


?>