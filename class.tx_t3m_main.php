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
 * Main static class with most central functions for creating and editing pages, users, targetgroups, salutations, ... (includes main function for all modules, too)
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_main {
	var $extKey, $rootTS, $myConf, $INTERNAL, $EXTERNAL;

	/**
	* php4 constructor
	*
	* @return	string	given name of the object (no purpose right now))
	*/
// 	function tx_t3m_main($name)	{
// 		tx_t3m_main::__construct($name);
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
	 * Main function for the submodules. Write the content to $this->content
	 *
	 * @return	void		nothing to be returned
	 */
	function main()	{
		global $TCA_DESCR,$TCA,$CLIENT;

		tx_t3m_main::init();
		tx_t3m_main::handlePostVars();

		// Draw the header.
		$this->doc = t3lib_div::makeInstance('bigDoc'); //mediumDoc
		$this->doc->backPath = $GLOBALS['BACK_PATH'];

		$this->doc->form='<form action="" method="POST">';

		// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		';
		$this->doc->postCode= '
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			</script>
		';

		$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />';
// 		$headerSection .= $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

		$this->content.=$this->doc->startPage($GLOBALS['LANG']->getLL('title'));

		// Include own CSS
		$this->content.= '<link rel=stylesheet type="text/css" href="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'res/styles.css">';
// 		$this->content.= '<link rel=stylesheet type="text/css" href="'.$GLOBALS['BACK_PATH'].'stylesheet.css">';

		$this->content.=$this->doc->header($GLOBALS['LANG']->getLL('title').' - '.$GLOBALS['LANG']->sL($GLOBALS['MLANG']['default']['ll_ref'].':mlang_tabs_tab'));

		$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
		$this->content.=$this->doc->divider(5);

		// Render content:
		$this->moduleContent();

		// ShortCut
		if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}

		$this->content.=$this->doc->spacer(10);
		$this->content.=$this->myFooter;
	}

	/**
	 * Initialize some variables
	 *
	 * @return	void		nothing
	 */
	function init()	{

		$this->extKey = 't3m'; // how to get dynamically? $_EXTKEY is wrong and $EM_CONF empty..
		$this->myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
// 		$this->requiredExtensions  = array( //$EM_CONF[t3m]['constraints']['depends'] does NOT work!
// 			'rs_userimp',
// 			'sr_feuser_register',
// 			'static_info_tables'
// 			);
		$this->rootTS = tx_t3m_main::getTSConstants();
// 		$this->INTERNAL = '';
// 		$this->EXTERNAL = '';
		$this->ICON_PATH = $GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('t3m').'gfx/';

		$GLOBALS['LANG']->includeLLFile('EXT:tcdirectmail/mod1/locallang.xml');
		$GLOBALS['LANG']->includeLLFile('EXT:tcdirectmail/mod2/locallang.xml');
		$GLOBALS['LANG']->includeLLFile('EXT:tcdirectmail/locallang_db.xml');
		$GLOBALS['LANG']->includeLLFile('EXT:'.$this->extKey.'/locallang.xml');
		$GLOBALS['LANG']->includeLLFile('EXT:'.$this->extKey.'/locallang_db.xml');


		$this->tcColumnsOnly = '&columnsOnly=title,tx_tcdirectmail_senttime,doktype,hidden,tx_tcdirectmail_repeat,tx_tcdirectmail_sendername,tx_tcdirectmail_senderemail,tx_tcdirectmail_test_target,tx_tcdirectmail_real_target,tx_tcdirectmail_spy,tx_tcdirectmail_register_clicks,tx_tcdirectmail_dotestsend,tx_tcdirectmail_attachfiles,tx_tcdirectmail_plainconvert';
		$this->tcDefVals =  '&defVals[pages][doktype]=189&defVals[pages][hidden]=0&defVals[pages][tx_tcdirectmail_sendername]='.$this->myConf['sender_name'].'&defVals[pages][tx_tcdirectmail_senderemail]='.$this->myConf['sender_email'].'&defVals[pages][tx_tcdirectmail_test_target]=tx_tcdirectmail_targets_'.$this->myConf['targetTest'].'&defVals[pages][tx_tcdirectmail_spy]='.$this->myConf['tcdirectmail_spy'].'&defVals[pages][tx_tcdirectmail_register_clicks]='.$this->myConf['tcdirectmail_register_clicks'].'&defVals[pages][tx_tcdirectmail_dotestsend]='.$this->myConf['tx_tcdirectmail_dotestsend'].'&defVals[pages][tx_tcdirectmail_plainconvert]='.$this->myConf['tcdirectmail_plain'];
		/** @todo: make lynx only default if it is installed (linux), otherwise  'tx_tcdirectmail_plain_simple' , type99 is not a good choice, i got errors, lynx seems to have troubles with umlauts? */
		$this->tcOverrideVals =  '&overrideVals[pages][doktype]=189&overrideVals[pages][hidden]=0&overrideVals[pages][tx_tcdirectmail_sendername]='.$this->myConf['sender_name'].'&overrideVals[pages][tx_tcdirectmail_senderemail]='.$this->myConf['sender_email'].'&overrideVals[pages][tx_tcdirectmail_test_target]=tx_tcdirectmail_targets_'.$this->myConf['targetTest'].'&overrideVals[pages][tx_tcdirectmail_dotestsend]='.$this->myConf['tx_tcdirectmail_dotestsend'].'&overrideVals[pages][tx_tcdirectmail_plainconvert]='.$this->myConf['tcdirectmail_plain'];

		$this->columnsOnlyFeuser = '&columnsOnly=gender,first_name,last_name,telephone,fax,email,company,address,zip,city,tx_t3m_country,date_of_birth,username,password,usergroup,tx_t3m_categories,module_sys_dmail_html,disable,deleted,tx_t3m_salutation';
		$this->defValsFeuser = '&defVals[fe_users][tx_t3m_country]='.$this->myConf['static_countries_uid'];

		require_once(t3lib_extMgm::extPath('tcdmailstats').'modfunc1/class.tx_tcdmailstats_modfunc1.php');
		require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_tools.php');

		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_bounce.php');
		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_stats.php');
		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_postinstall.php');
		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_spam.php');

	}

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
	 * @todo	 	testing cvs via url
	 */
	function importReceivers() {

		//csv file
		$columnsOnly = '';
		$defVals = '&defVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvfile&defVals[tx_tcdirectmail_targets][title]='.$GLOBALS['LANG']->getLL('csvfile').'&defVals[tx_tcdirectmail_targets][csvfilename]=t3m-example-cvs.txt';
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
		$defVals = '&defVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvlist&defVals[tx_tcdirectmail_targets][title]='.$GLOBALS['LANG']->getLL('csvlist').'&defVals[tx_tcdirectmail_targets][csvfields]=name,email&defVals[tx_tcdirectmail_targets][csvvalues]=foo,bar@localhost';
		$overrideVals = '&overrideVals[tx_tcdirectmail_targets][targettype]=tx_tcdirectmail_target_csvlist';
		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['receivers_Sysfolder'].']=new'.$defVals.$columnsOnly;
		$out .= '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('csvlist').'</a><br/>';

		return $out;
	}

	/**
	 * Integrates rs_userimp extension for superadmins
	 *
	 * @return	string		content for csv import from EXT:rs_userimp for superadmins
	 * @todo	 get it to work for normal beusers
	 * @todo 	create a profile (demo cvs included, but e.g. outlook profile would be cool)
	 */
	function importFeusers() {

		if ($GLOBALS['BE_USER']->isAdmin()) {
			$out.= '<a href="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('rs_userimp').'mod1/index.php?SET[function]=1" title="CSV Import"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('rs_userimp').'mod1/moduleicon.gif', '').' />&nbsp;CSV User Import Tool</a>';
		} else {
			$out.= '<img src="'.$this->ICON_PATH.'icon_fatalerror.gif">&nbsp;'.$GLOBALS['LANG']->getLL('errorNoAdmin');
		}
		// <a href="#" onclick="top.goToModule(\'tools_txrsuserimpM1\');this.blur();return false;" title="CSV Import Tool">

		return $out;

	}

	/**
	 * Returns a string with the sr_feuser_register's sysfolder where users are saved
	 *
	 * @return	string		table with the sr_feuser_register sysfolder where users are saved
	 */
	function getSubscriptionSysfolder()	{
		$out = tx_t3m_main::tableForSysfolder($this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value']);
		return $out;
	}

	/**
	 * Returns a table with the sr_feuser_register profile creation page
	 *
	 * @return	array		table with the sr_feuser_register profile creation page
	 */
	function getSubscriptionPage()	{
		//find via
		//1.pages: where $plugin.tx_srfeuserregister_pi1.registerPID is set via subscription Typoscript ['plugin.tx_srfeuserregister_pi1.registerPID']['value'] or
 		//2.tt_content: where list-type = sr_feuser_register_pi1 and select_key = CREATE
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.registerPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$out = tx_t3m_main::getPage($uid);
		}
		return $out;
	}

	/**
	* Returns a string with the sr_feuser_register's templateFile
	*
	* @return	string	table with the sr_feuser_register profile creation page
	*/
	function getSubscriptionPageTemplate()	{
		if (!($this->rootTS['plugin.tx_srfeuserregister_pi1.file.templateFile']['value'])) {
			$file = 'EXT:sr_feuser_register/pi1/tx_srfeuserregister_pi1_css_tmpl.html'; // standard config file path
		} else {
			$file = $this->rootTS['plugin.tx_srfeuserregister_pi1.file.templateFile']['value'];
		}
		$out = tx_t3m_main::tableForFile($file);
		return $out;
	}


	/**
	 * Returns a table for a page
	 *
	 * @param	int		$uid: page uid
	 * @return	array		table for a page
	 */
	function getPage($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'pages',
			'uid = '.intval($uid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = tx_t3m_main::tableForPage($row['uid']);
		if (!$out) {
			$out = '<br /><img src="'.$this->ICON_PATH.'icon_fatalerror.gif">&nbsp;'.$GLOBALS['LANG']->getLL('errorNoPage');
		}
		return $out;
	}

	/**
	 * Returns a table with the sr_feuser_register profile creation confirmation page
	 *
	 * @return	array		table with the sr_feuser_register profile creation confirmation page
	 */
	function getSubscriptionConfirmationPage()	{
		//find via
		//1. pages: where $plugin.tx_srfeuserregister_pi1.confirmPID is set via subscription Typoscript OR
		//2. tt_content: where list-type = sr_feuser_register_pi1 and select_key = '' (according to manual)
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.confirmPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$out = tx_t3m_main::getPage($uid);
		}
		return $out;
	}

	/**
	 * Returns a table with the sr_feuser_register profile creation confirmation page
	 *
	 * @return	array		table with the sr_feuser_register profile creation confirmation page
	 */
	function getSubscriptionEditPage()	{
		//find via
 		//1. pages: where $plugin.tx_srfeuserregister_pi1.confirmPID is set via subscription Typoscript OR
 		//2: tt_content: where list-type = sr_feuser_register_pi1 and select_key = 'EDIT' (according to manual)
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.editPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$out = tx_t3m_main::getPage($uid);
		}
		return $out;
	}

	/**
	 * Returns a table with the feuser login page
	 *
	 * @return	array		table with the feuser login page
	 */
	function getLoginPage()	{
		//find via
 		//1. pages: where $plugin.tx_srfeuserregister_pi1.confirmPID is set via subscription Typoscript OR
 		//2: tt_content: where CType = login and select_key = 'EDIT' (according to manual)
		$uid = $this->rootTS['plugin.tx_srfeuserregister_pi1.loginPID']['value'];
		if (!$uid) {
			$out = $GLOBALS['LANG']->getLL('errorSubscriptionExtension');
		} else {
			$out = tx_t3m_main::getPage($uid);
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
			$out .= '<tr><td>'.tx_t3m_main::getGroupName($gid).'</td>';
			$out .= '<td>'.tx_t3m_main::editGroup($gid).'</td>';
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
			$userArrayAll[$row['uid']] = explode(',',$row['usergroup']);

			// check if the user is in that group and if so add him to the array
			if (in_array(intval($gid), $userArrayAll[$row['uid']])) {
				$userArray[] = $row['uid'];
			}
		}
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
				$userArray[] = $row['uid'];
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
	 * Returns a name (title) for a page
	 *
	 * @param	int		$pid: page uid
	 * @return	string		a name (title) for a page
	 */
	function getPageName($pid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'title',
			'pages',
			'uid="'.intval($pid).'"'
			);
		if ((!$pid) || (!$res)) {
			$out = $GLOBALS['LANG']->getLL('errorNoPage');
		} else {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$out = $row['title'];
		}
		return $out;
	}


	/**
	 * Returns a link for viewing a page
	 *
	 * @param	int		uid of page
	 * @return	string		links for editing a list of the users of a group
	 */
	function viewPage($uid) {
		$out = '<a href="#" onClick="'.htmlspecialchars(t3lib_BEfunc::viewOnClick(intval($uid), $GLOBALS['BACK_PATH'],t3lib_BEfunc::BEgetRootLine(intval($uid)))).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/zoom.gif" title="'.$GLOBALS['LANG']->getLL('View').'"/></a>';
		return $out;
	}


	/**
	 * Returns categories and edit buttons
	 *
	 * @return	string		a table with categories and edit buttons
	 */
	function categories()	{
		$table = 'tx_'.$this->extKey.'_categories';
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,calculated_receivers',
			$table,
			'deleted=0 AND hidden=0',
			'',
			'calculated_receivers DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.$row['name'].'</td>
				<td>'.$row['calculated_receivers'].'</td>
				<td>'.tx_t3m_main::editCategory($row['uid']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns contents and edit buttons
	 *
	 * @param	int		page id
	 * @return	string		a table with contents and edit buttons
	 */
	function getContents($pid)	{
		if (!$pid) {
			$pid = $this->myConf['mailings_Sysfolder'];
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,header',
			'tt_content',
			'pid = '.intval($pid).' AND deleted=0 AND hidden=0'
			);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) == 0) {
			$out = '';
		} else {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
					<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Header').'</td>
					<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td></tr>';
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
				$out .= '<tr><td>'.$row['header'].'</td><td>'.tx_t3m_main::editContent($row['uid']).'</td></tr>';
			}
			$out .= '</table>';
		}
		return $out;
	}

	/**
	 * Returns first campaign
	 *
	 * @return	int		first campaign
	 */
	function getFirstCampaign() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'MIN(uid) as imin',
			'tx_t3m_campaigns',
			'deleted=0 AND hidden=0'
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['imin'];
		return $out;
	}

	/**
	 * Returns stats for users
	 *
	 * @return	string		a table with stats for users
	 * @todo		clean up the structure mess
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
		$out.=tx_t3m_main::formGroupSelector();
		$out.='<br /><h3>'.$LANG->getLL('Group').'</h3>';
		$out.=tx_t3m_main::tableForFeGroup($gid);
		$out.='<br /><h3>'.$LANG->getLL('User').'</h3>';
		$out.=tx_t3m_main::tableForFeGroupUsers($gid);
		if ($_REQUEST['group'] == 'blocked') { // quick hack to shwo disabled users too.
			$out.='<br /><h3>'.$LANG->getLL('disabledUsers').'</h3>';
			$out.=tx_t3m_main::getDisabledUsers();
		}

		return $out;
	}

	/**
	 * Returns campaigns and edit buttons
	 *
	 * @return	string		a table with campaigns and edit buttons
	 */
	function campaigns()	{
		//model ;-)
		$campaigns = tx_t3m_main::getCampaigns();
		//view ;-)
		$out = tx_t3m_main::tableForCampaigns($campaigns);
		return $out;
	}

	/**
	 * Returns campaigns and edit buttons
	 *
	* @param	int		$cids: campaign uids
	 * @return	string		a table with campaigns and edit buttons
	 */
	function tableForCampaigns($cids)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Description').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countEmails').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('createEmail').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('campaignfinishedornot').'</td></tr>';
		foreach($cids as $cid)	{
			$out .= '<tr><td>'.$cid['name'].'</td>
				<td>'.$cid['description'].'</td>
				<td>'.tx_t3m_main::editCampaign($cid['uid']).'</td>
				<td>'.tx_t3m_stats::countEmails($cid['uid']).'</td>
				<td>'.tx_t3m_main::createCampaignMailing($cid['uid']).'</td>
				<td>'.tx_t3m_stats::checkCampaignFinished($cid['uid']).'</td>
				</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns the name of a campaign
	 *
	 * @param	int		campaign uid
	 * @return	string		name of a campaign
	 */
	function getCampaignName($uid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'name',
			'tx_'.$this->extKey.'_campaigns',
			'uid = '.intval($uid).' AND deleted=0 AND hidden=0'
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['name'];
		return $out;
	}

	/**
	 * Returns the description of a campaign
	 *
	 * @param	int		campaign uid
	 * @return	string		a description of a campaign
	 */
	function getCampaignDescription($uid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'description',
			'tx_'.$this->extKey.'_campaigns',
			'uid = '.intval($uid).' AND deleted=0 AND hidden=0'
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['description'];
		return $out;
	}

	/**
	 * Returns array of campaigns with uid and name
	 *
	 * @param	int		campaign uid
	 * @return	array		of campaigns with uid and name
	 */
	function getCampaigns()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,description',
			'tx_'.$this->extKey.'_campaigns',
			'deleted=0 AND hidden=0'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out[] = $row;
// 		   	$out[$row['uid']] = $row['name'];
		}
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function tcdirectmails()	{
		$out = $GLOBALS['LANG']->getLL('Newsletters').'<br />'.tx_t3m_main::newsletters();
		$out .= '<br />'.$GLOBALS['LANG']->getLL('OneOffMailings').'<br />'.tx_t3m_main::oneOffMailings();
		$out .= '<br />'.$GLOBALS['LANG']->getLL('MailsForCampaigns').'<br />'.tx_t3m_main::campaignMailings();
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function newsletters()	{
		//model ;-)
		$pids = tx_t3m_main::getNewsletters();
		//controller ;-)
		foreach ($pids as $pid) {
			if (!(tx_t3m_stats::checkMailSent($pid['uid']))) {
				$notmailedPids[] = $pid;
			}
		}
		//view ;-)
		$out = tx_t3m_main::tableForNewsletters($notmailedPids);
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function getNewsletters()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title,tx_tcdirectmail_repeat,tx_tcdirectmail_senttime,tx_t3m_spam_score',
			'pages',
			'pid='.$this->myConf['mailings_Sysfolder'].' AND deleted=0 AND hidden=0 AND tx_'.$this->extKey.'_campaign=0 AND NOT tx_tcdirectmail_repeat=0',
			'',
			'tx_tcdirectmail_senttime DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$out[] = $row;
		}
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @param	array		$pids pages
	 * @return	string		a table with created directmails and edit buttons
	 */
	function tableForNewsletters($pids)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countContents').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Create').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Repeat').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('checkForSpam').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('spamScore').'</td></tr>';
		foreach ($pids as $row)	{
			$out .= '<tr><td>'.$row['title'].'</td>
				<td>'.tx_t3m_main::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_main::editNewsletter($row['uid']).'</td>
				<td>'.tx_t3m_stats::countContents($row['uid']).'</td>
				<td>'.tx_t3m_main::createContent($row['uid']).'</td>
				<td>'.tx_t3m_main::editContents($row['uid']).'</td>
				<td>';
			if (intval($row['tx_tcdirectmail_senttime']) != 0) {
				$out .= t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
			}
			$out .= '</td>
				<td>'.$GLOBALS['LANG']->getLL('pages.tx_tcdirectmail_repeat.I.'.$row['tx_tcdirectmail_repeat']).'</td>
				<td>'.tx_t3m_main::formSpamCheck($row['uid']).'</td>
				<td>'.tx_t3m_spam::imgSpamCheck($row['tx_t3m_spam_score']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function oneOffMailings()	{
		//model ;-)
		$pids = tx_t3m_main::getOneOffMailings();
		//controller ;-)
		foreach ($pids as $pid) {
			if (!(tx_t3m_stats::checkMailSent($pid['uid']))) {
				$notmailedPids[] = $pid;
			}
		}
		//view ;-)
		$out = tx_t3m_main::tableForOneOffMailings($notmailedPids);
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function getOneOffMailings()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title,tx_tcdirectmail_senttime,tx_t3m_spam_score',
			'pages',
			'pid='.$this->myConf['mailings_Sysfolder'].' AND deleted=0 AND hidden=0 AND tx_'.$this->extKey.'_campaign=0 AND tx_tcdirectmail_repeat=0',
			'',
			'tx_tcdirectmail_senttime DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$out[] = $row;
		}
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @param	array		$pids: pages
	 * @return	string		a table with created directmails and edit buttons
	 */
	function tableForOneOffMailings($pids)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countContents').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Create').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('checkForSpam').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('spamScore').'</td></tr>';
		foreach ($pids as $row)	{
			$out .= '<tr><td>'.$row['title'].'</td>
				<td>'.tx_t3m_main::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_main::editOneOffMailing($row['uid']).'</td>
				<td>'.tx_t3m_stats::countContents($row['uid']).'</td>
				<td>'.tx_t3m_main::createContent($row['uid']).'</td>
				<td>'.tx_t3m_main::editContents($row['uid']).'</td>
				<td>';
			if (intval($row['tx_tcdirectmail_senttime']) != 0) {
				$out .= t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
			}
			$out .= '</td>
				<td>'.tx_t3m_main::formSpamCheck($row['uid']).'</td>
				<td>'.tx_t3m_spam::imgSpamCheck($row['tx_t3m_spam_score']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}


	/**
	 * Returns array with uids of pages of that campaign
	 *
	 * @param	int		$cid: campaign id
	 * @return	array		with uids of pages of that campaign
	 */
	function getCampaignMailings($cid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title,subtitle,tx_'.$this->extKey.'_campaign,tx_tcdirectmail_senttime,tx_t3m_spam_score',
			'pages',
			'pid='.$this->myConf['mailings_Sysfolder'].' AND deleted=0 AND hidden=0 AND tx_'.$this->extKey.'_campaign='.intval($cid),
			'',
			'tx_tcdirectmail_senttime DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$pids[] = $row;
		}
		//select pids where campaing = intval($cid);
		$out = $pids;
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function campaignMailings()	{
		$campaigns = tx_t3m_main::getCampaigns();
		foreach ($campaigns as $campaign) {
			foreach (tx_t3m_main::getCampaignMailings($campaign['uid']) as $cpids) {
				$pids[] = $cpids;
			}
		}
		foreach ($pids as $pid) {
			if (!(tx_t3m_stats::checkMailSent($pid['uid']))) {
				$notmailedPids[] = $pid;
			}
		}
		$out = tx_t3m_main::tableForCampaignMailings($notmailedPids);
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @param	array		$pids: pages
	 * @return	string		a table with created directmails and edit buttons
	 */
	function tableForCampaignMailings($pids)	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('campaign').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countContents').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Create').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('checkForSpam').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('spamScore').'</td></tr>';
		foreach ($pids as $row) {
			$out .= '<tr><td>'.tx_t3m_main::getCampaignName($row['tx_'.$this->extKey.'_campaign']).'</td>
				<td>'.$row['title'].'</td>
				<td>'.tx_t3m_main::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_main::editCampaignMailing($row['uid']).'</td>
				<td>'.tx_t3m_stats::countContents($row['uid']).'</td>
				<td>'.tx_t3m_main::createContent($row['uid']).'</td>
				<td>'.tx_t3m_main::editContents($row['uid']).'</td>
				<td>';
			if ($row['tx_tcdirectmail_senttime'] != 0) {
				$out .= t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
			}
			$out .= '</td>
				<td>'.tx_t3m_main::formSpamCheck($row['uid']).'</td>
				<td>'.tx_t3m_spam::imgSpamCheck($row['tx_t3m_spam_score']).'</td></tr>';
		}
		$out .= '</table>';
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
	 * Returns group selector
	 *
	 * @param	int		$tid: targetgroup id
	 * @return	string		group selector link
	 */
	function formTargetgroupSelector($tid)	{
		$out = '<a href="#" onclick="document.location=\'index.php?targetgroup=\' + '.intval($tid).'">'.tx_t3m_main::getTargetgroupName(intval($tid)).'</a>';
		return $out;
	}

	/**
	 * Returns campaign selector
	 *
	 * @param	int		$cid: campaing id
	 * @return	string		campaign selector link
	 */
	function formCampaignSelector($cid)	{
		$out = '<select onchange="document.location=\'index.php?campaign=\' + options[selectedIndex].value">';  //&SET[function]=group is set anyway
		$campaigns = tx_t3m_main::getCampaigns(); //gives: array(uid => name)
// 		ksort ($groups);
		asort($campaigns);
		foreach ($campaigns as $key => $val) {
			if ($_REQUEST['campaign'] == $key) {
				$out .= '<option selected value="'.$key.'">'.$val.'</option>';
			} else {
				$out .= '<option value="'.$key.'">'.$val.'</option>';
			}
		}
                $out .= '</select>';
		return $out;
	}

	/**
	 * Returns a button for spamcheck
	 *
	 * @param	int		$pid: page uid
	 * @return	string		button for spamcheck
	 */
	function formSpamCheck($pid)	{
		$out = '<form><input type="submit" name="check_for_spam" value="'.$GLOBALS['LANG']->getLL('spamOrNot').'" />
			<input type="hidden" name="id" value="'.intval($pid).'" /></form>';
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
				<td>'.tx_t3m_main::editSalutation($row['uid']).'</td></tr>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">
			<img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a>';
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
		$out ='<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'" title="'.$GLOBALS['LANG']->getLL('Edit').'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a>';
		return $out;
	}

	/**
	 * Returns links for exports of users and groups
	 *
	 * @return	string		links for exports of users and groups
	 * @todo		csv export of targetgroups
	 */
	function export()	{
		$out .='<a href="'.$GLOBALS['BACK_PATH'].'db_list.php?id='.$this->rootTS['plugin.tx_srfeuserregister_pi1.pid']['value'].'&table=fe_users&sortField=username&csv=1&returnUrl=http://'.t3lib_div::getIndpEnv('TYPO3_HOST_ONLY').t3lib_div::getIndpEnv('SCRIPT_NAME').'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/fileicons/csv.gif" title="'.$GLOBALS['LANG']->getLL('Export').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('export').'</a>';
		return $out;
	}


	/**
	* Returns the folders for our extension
	*
	* @return	array	array of uids which are our folders
	*/
// 	function getOurMailFolders()	{
// 		// select * from pages where module=dmail
// 		return 'List of Our folders: @todo<br/>';
// // 		<a href=typo3conf/ext/direct_mail/mod/index.php?id='.$i.'>';
// 	}

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
	 * @todo zone support (challenges: keep consistent with zip, reduce selection corresponding to selected country-code!)
	 */
	function getTargetgroupDefinitions()	{
		$out = '<table class="typo3-dblist">';

		$columns = 'name,gender,age_range,country,zip,categories,Edit,countUsers'; //zone,salutation,
		$columnsArray = explode(',',$columns);

		$out .= '<tr class="c-headLineTable">';
		foreach($columnsArray as $value) {
			$out .= '<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL($value).'</td>';
		}
		$out .= '</tr>';

		$resTargetgroups = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,gender,age_from,age_to,country,zip,categories_uid,calculated_receivers', //,zone,salutations_uid
			'tx_'.$this->extKey.'_targetgroups',
			'deleted=0 AND hidden=0',
			'',
			'calculated_receivers DESC'
			);
		while($rowTargetgroup = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTargetgroups))	{
			$out .= '<tr>';
			$rowTargetgroup['name'] = tx_t3m_main::formTargetgroupSelector($rowTargetgroup['uid']);

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

			$rowTargetgroup['Edit'] = tx_t3m_main::editTargetgroup($rowTargetgroup['uid']);

			$rowTargetgroup['countUsers'] = $rowTargetgroup['calculated_receivers'];

// 			$rowTargetgroup['userids'] = implode('<br/>',tx_t3m_main::getTargetgroupUsers($rowTargetgroup['uid']));

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
		$resTargetgroups = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,gender,age_from,age_to,country,zone,zip,salutations_uid,categories_uid',
			'tx_t3m_targetgroups',
			'uid = '.intval($uid).' AND deleted=0 AND hidden=0'
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
				$where[] = 'date_of_birth > '.tx_t3m_main::getTimestampFromAge($rowTargetgroup['age_from']);
			}
			if ($rowTargetgroup['age_to'] != 0 ) {
				$where[] = 'date_of_birth < '.tx_t3m_main::getTimestampFromAge($rowTargetgroup['age_to']);
			}
			if ($rowTargetgroup['zip'] != 0 ) {
				$where[] = 'zip LIKE \''.$rowTargetgroup['zip'].'%\'';
			}
			$whereString = implode(' AND ',$where).' AND deleted=0 AND disable=0';

			$where = '';
// 			t3lib_div::debug($whereString);
			$resFeUsers = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,email,tx_t3m_categories,module_sys_dmail_html',
				'fe_users',
				$whereString
			);
			$i = 0;
			while($rowFeUser = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resFeUsers))	{
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
		return $userArray;
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
	 * Retruns user ids who chose a category
	 *
	 * @param	int		uid of category
	 * @return	array		user ids who chose a category
	 */
 	function getCategoryUsers($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,tx_t3m_categories',
			'fe_users'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			if ($row['tx_t3m_categories'] != '' ) { //user has chosen at least one category
				$categories = explode(',',$row['tx_t3m_categories']);
				if (in_array($uid, $categories)) {
					$out[] = $row['uid'];
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
				$out .= '</td><td>'.tx_t3m_main::editUser($row['uid']).'</td>
					</tr>';
// 					<td>'.tx_t3m_main::deleteUser($row['uid']).'</td>
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
			$out .= '</td><td>'.tx_t3m_main::editUser($row['uid']).'</td>
				</tr>';
// 				<td>'.tx_t3m_main::deleteUser($row['uid']).'</td>
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
			$out .= '</td><td>'.tx_t3m_main::editUser($row['uid']).'</td>
				</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns a list of receiver definitions
	 *
	 * @param	int		uid of page where to find the receivers
	 * @return	string		table with receiver lists
	 */
	function getReceivergroups($pid)	{

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
			$out .= '<tr><td>'.$row['title'].'</td>
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
				<td>'.tx_t3m_main::editReceivergroup($row['uid']).'</td></tr>';
			$count = '';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns a list of receiver definitions
	 *
	 * @return	string		table with receiver lists
	 */
	function createTCDirectmailForReceivergroups()	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Receivergroup').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('NewMail').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title,targettype,calculated_receivers,tx_t3m_target',
			'tx_tcdirectmail_targets',
			'deleted=0 AND hidden=0', //' AND disable=0'
			'',
			'calculated_receivers DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.$row['title'].'</td>
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
				<td>'.tx_t3m_main::createTCDirectmailForReceivergroup($row['uid']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns a link for a receiver definition
	 *
	 * @param	int		uid of receivergroup for which to create directmail
	 * @return	string		link for creating a tcdirectmail
	 */
	function createTCDirectmailForReceivergroup($uid) {

		$columnsOnly = $this->tcColumnsOnly;
// 		$columnsOnly = '&columnsOnly=title,tx_tcdirectmail_senttime,doktype,hidden,tx_tcdirectmail_repeat,tx_tcdirectmail_sendername,tx_tcdirectmail_senderemail,tx_tcdirectmail_test_target,tx_tcdirectmail_spy,tx_tcdirectmail_register_clicks,tx_tcdirectmail_dotestsend,tx_tcdirectmail_attachfiles,tx_tcdirectmail_plainconvert,tx_tcdirectmail_repeat,tx_t3m_campaign,tx_tcdirectmail_real_target';
		$defVals = $this->tcDefVals.'&defVals[pages][tx_tcdirectmail_real_target]='.intval($uid);
		$overrideVals = $this->tcOverrideVals.'&OverrideVals[pages][tx_tcdirectmail_real_target]='.intval($uid);
		if (!intval($pid)) {
			$pid = $this->myConf['mailings_Sysfolder'];
		}
		$params = '&edit[pages]['.intval($pid).']=new'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_directmails.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewMail').'</a>';
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
						$tmpusers = tx_t3m_main::getTargetgroupUsers($targetgroup);
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
						foreach (tx_t3m_main::getFeGroupUsers($group) as $key => $val) {
							$uids[] = $val;
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
				$count = count(tx_t3m_main::getTargetgroupUsers($row['tx_t3m_target']));
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
		$count = count(tx_t3m_main::getTargetgroupUsers(intval($uid)));
		$GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_t3m_targetgroups SET calculated_receivers = '.$count.' WHERE uid = '.intval($uid));
	}



	/**
	 * Update usercount for all targetgroups
	 *
	 * @param	int		$uid: category uid
	 * @return	boolean		true if it went ok
	 */
	function updateCalculatedCategory($uid) {
		$count = count(tx_t3m_main::getCategoryUsers(intval($uid)));
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
			tx_t3m_main::updateCalculatedReceivers($row['uid']);
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
			tx_t3m_main::updateCalculatedTargetgroupUsers($row['uid']);
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
			tx_t3m_main::updateCalculatedCategory($row['uid']);
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}

	/**
	 * Creates a form to enter a new receiver group definition
	 *
	 * @return	string		form for entering a new receiver group definition
	 */
	function createReceivergroup()	{
// 		$columnsOnly = '&columnsOnly=targettype,title,plain_only,tx_t3m_target';
		$defVals = '&defVals[tx_tcdirectmail_targets][hidden]=0&defVals[tx_tcdirectmail_targets][targettype]=tx_t3m_target1';
		$overrideVals = '&overrideVals=[tx_tcdirectmail_targets][hidden]=0';
		$params = '&edit[tx_tcdirectmail_targets]['.$this->myConf['receivers_Sysfolder'].']=new'.$defVals.$overrideVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tcdirectmail').'mailtargets.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>'.$GLOBALS['LANG']->getLL('newReceivergroup').'</a><br/>';
		return $out;
	}

	/**
	 * Creates a form to enter a new receiver group definition
	 *
	 * @return	string		form for entering a new receiver group definition
	 */
	function createTargetgroupDefinition()	{
		$table = 'tx_'.$this->extKey.'_targetgroups';
		$columnsOnly = '&columnsOnly=name,gender,age_from,age_to,zip,country,categories_uid,description'; /** @todo add zone */
		$defVals = '&defVals['.$table.'][country]='.$this->myConf['static_countries_uid'].'&defVals['.$table.'][zone]='.$this->myConf['static_country_zones_uid'];
		$params = '&edit['.$table.']['.$this->myConf['targetgroups_Sysfolder'].']=new'.$defVals.$overrideVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_targetgroups.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>'.$GLOBALS['LANG']->getLL('newTargetgroupdefinition').'</a><br/>';
		return $out;
	}

	/**
	 * Creates a link for a new sysfolder
	 *
	 * @return	string		link for a new sysfolder
	 */
	function createSysfolder()	{
		$params = '&edit[pages][0]=new&defVals[pages][title]=t3ms&defVals[pages][doktype]=254&columnsOnly=title,doktype';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'typo3/gfx/i/sysf.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/>'.$GLOBALS['LANG']->getLL('createSysfolder').'</a><br/>';
		return $out;
	}

	/**
	* Creates a link for a new folder
	*
	* @return	string	link for a new folder
	*/
	function createFolder()	{ // t3lib_extFileFunctions func_newfolder
		global $FILEMOUNTS;
		require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
		require_once(PATH_t3lib.'class.t3lib_extfilefunc.php');
		$out = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/sysf.gif','width="16" height="16"').' title="folder" alt="" />I just created folder ';
		$out .= $file['newfolder'][0]['target'] = constant('PATH_site').$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir']; // absolute path required (like '/var/www/html/fileadmin'), relative path  ../../../../$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'] or t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') do not work
		$out .= $file['newfolder'][0]['data'] = 't3mfolder'; //name of new folder
		$myFileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
		$myFileProcessor->init($FILEMOUNTS, $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$myFileProcessor->init_actionPerms($GLOBALS['BE_USER']->user['fileoper_perms']);
		$myFileProcessor->start($file);
		$myFileProcessor->processData();
// 		$out .= debug($GLOBALS['BE_USER']->returnFilemounts());
		return '<a href="#" onclick="">'.$out.'</a><br/>';
	}

	/**
	* Creates a link for a new page in given sysfolder
	*
	* @param	int	page/sysfolder in which to create the page
	* @return	string	link for a new page
	*/
// 	function createPage($pid)	{
// 		$params = '&edit[pages]['.$this->myConf['T3M_Sysfolder'].']=new&defVals[pages][header]=New%20Page&columnsOnly=title';
// 		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/new_page.gif" />'.$GLOBALS['LANG']->getLL('NewPage').'</a><br/>';
// 		return $out;
// 	}

	/**
	 * Creates a link for a new content for a page
	 *
	 * @param	int		page in which to create the page
	 * @return	string		link for a new content for a page
	 */
	function createContent($pid)	{
		if (!$pid) {
			$pid = $this->myConf['mailings_Sysfolder'];
		}
		$params = '&edit[tt_content]['.intval($pid).']=new&defVals[tt_content][header]=New%20Element';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/new_record.gif" />&nbsp;'.$GLOBALS['LANG']->getLL('NewContent').'</a><br/>';
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
		$params = '&edit[fe_users]['.intval($pid).']=new'.$defVals.$columnsOnly.$overrideVals; /** @todo: define default values */
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/new_el.gif" title="'.$GLOBALS['LANG']->getLL('New').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewUser').'</a><br/>';
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
		$params = '&edit[fe_groups]['.intval($pid).']=new'.$defVals.$columnsOnly.$overrideVals; /** @todo: define default values */
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/new_el.gif" title="'.$GLOBALS['LANG']->getLL('New').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewGroup').'</a><br/>';
		return $out;
	}


	/**
	* Creates a new campaign
	*
	* @return	string	link for entering a new campaign
	*/
	function createCampaign()	{ //[0]
		$params = '&edit[tx_'.$this->extKey.'_campaigns]['.$this->myConf['campaings_Sysfolder'].']=new&defVals[tx_'.$this->extKey.'_campaigns][name]=NewCampaign';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_campaigns.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewCampaign').'</a><br/>';
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
	 * Creates a new directmail
	 *
	 * @param	int		page oder sysfolder on which to create the directmail
	 * @return	string		form for entering a new campaign
	 */
	function createTcdirectmail($pid)	{
		$out = tx_t3m_main::createNewsletter($pid);
		$out .= '<br />'.tx_t3m_main::createOneOffMailing($pid);
		$out .= '<br />'.tx_t3m_main::createCampaignMailing($pid);
		return $out;
	}

	/**
	 * Creates a new directmail "Newsletter"
	 *
	 * @param	int		page oder sysfolder on which to create the "Newsletter" directmail
	 * @return	string		form for entering a new campaign
	 */
	function createNewsletter($pid)	{
		$columnsOnly = $this->tcColumnsOnly.',tx_tcdirectmail_repeat';
		$defVals = $this->tcDefVals;
		$overrideVals = $this->tcOverrideVals;
		if (!intval($pid)) {
			$pid = $this->myConf['mailings_Sysfolder'];
		}
		$params = '&edit[pages]['.intval($pid).']=new'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_directmails.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewNewsletter').'</a><br/>';
		return $out;
	}

	/**
	 * Creates a new directmail "OneOffMailing"
	 *
	 * @param	int		page oder sysfolder on which to create the "OneOffMailing" directmail
	 * @return	string		form for entering a new campaign
	 */
	function createOneOffMailing($pid)	{
		$columnsOnly = $this->tcColumnsOnly;
		$defVals = $this->tcDefVals.'&defVals[pages][tx_tcdirectmail_repeat]=0';
		$overrideVals = $this->tcOverrideVals.'&overrideVals[pages][tx_tcdirectmail_repeat]=0';
		if (!intval($pid)) {
			$pid = $this->myConf['mailings_Sysfolder'];
		}
		$params = '&edit[pages]['.intval($pid).']=new'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_directmails.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewOneOffMailing').'</a><br/>';
		return $out;
	}

	/**
	 * Creates a new directmail "ForCampaign"
	 *
	 * @param	int		page oder sysfolder on which to create the directmail "ForCampaign"
	 * @param	int		$pid: page id in which to create the new page
	 * @return	string		form for entering a new campaign
	 */
	function createCampaignMailing($cid, $pid)	{
		$columnsOnly = $this->tcColumnsOnly.',tx_t3m_campaign';
		$defVals = $this->tcDefVals.'&defVals[pages][tx_t3m_campaign]='.intval($cid);
		$overrideVals = $this->tcOverrideVals.'&overrideVals=[pages][tx_tcdirectmail_repeat]=0';
		if (!intval($pid)) {
			$pid = $this->myConf['mailings_Sysfolder'];
		}
		$params = '&edit[pages]['.intval($pid).']=new'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'icon_tx_'.$this->extKey.'_directmails.gif', '').'" title="'.$GLOBALS['LANG']->getLL('Create').'"/>&nbsp;'.$GLOBALS['LANG']->getLL('NewMailForCampaign').'</a><br/>';
		return $out;
	}

	/**
	* Edit a directmail
	*
	* @return	string	form for editing a directmail
	*/
// 	function editTCDirectmail($uid)	{
// 		$columnsOnly = '&columnsOnly=title,tx_tcdirectmail_senttime,tx_'.$this->extKey.'_campaign,tx_'.$this->extKey.'_targetgroups,tx_tcdirectmail_spy,tx_tcdirectmail_register_clicks';
// 		$params = '&edit[pages]['.intval($uid).']=edit&defVals[pages][doktype]=189'.$columnsOnly;
// 		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
// 		return $out;
// 	}

	/**
	 * Edit a newsletter
	 *
	 * @param	int		page to alter
	 * @return	string		form for editing a newsletter
	 */
	function editNewsletter($uid)	{
		$columnsOnly = '&columnsOnly=title,tx_tcdirectmail_senttime,tx_tcdirectmail_real_target,tx_tcdirectmail_plainconvert';
		$params = '&edit[pages]['.intval($uid).']=edit&defVals[pages][doktype]=189'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}

	/**
	 * Edit a OneOffMailing
	 *
	 * @param	int		page to alter
	 * @return	string		form for editing a OneOffMailing
	 */
	function editOneOffMailing($uid)	{
		$columnsOnly = '&columnsOnly=title,tx_tcdirectmail_senttime,tx_tcdirectmail_real_target,tx_tcdirectmail_plainconvert';
		$overrideVals = $this->tcOverrideVals.'&overrideVals=[pages][tx_tcdirectmail_repeat]=0';
		$params = '&edit[pages]['.intval($uid).']=edit&defVals[pages][doktype]=189'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}

	/**
	* Edit a campaign mailing
	*
	* @param	int	page to alter
	* @return	string	form for editing a campaign mailing
	*/
	function editCampaignMailing($uid)	{
		$columnsOnly = '&columnsOnly=title,tx_tcdirectmail_senttime,tx_'.$this->extKey.'_campaign,tx_tcdirectmail_real_target,tx_tcdirectmail_plainconvert';
		$defVals = '&defVals[pages][doktype]=189';
		$params = '&edit[pages]['.intval($uid).']=edit'.$defVals.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}


	/**
	* Edit a campaign mailing
	*
	* @param	int	page to alter
	* @return	string	form for editing a campaign mailing
	*/
	function editTCDirectmailSendtime($uid)	{
		$columnsOnly = '&columnsOnly=tx_tcdirectmail_senttime';
		$params = '&edit[pages]['.intval($uid).']=edit'.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath($this->extKey).'gfx/calendar.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a>';
		return $out;
	}

	/**
	 * Creates a link for editing a content category
	 *
	 * @param	int		$uid: campaign uid
	 * @return	string		form for editing a content category
	 */
	function editCampaign($uid)	{
		$params = '&edit[tx_'.$this->extKey.'_campaigns]['.intval($uid).']=edit';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'" title="'.$GLOBALS['LANG']->getLL('Edit').'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}

	/**
	 * Creates a link for editing a page
	 *
	 * @param	int		uid of the page
	 * @return	string		link for editing a page
	 */
	function editPage($uid)	{
		$columnsOnly = '';
		$params = '&edit[pages]['.intval($uid).']=edit'.$columnsOnly;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}

	/**
	 * Creates a link for editing a content element
	 *
	 * @param	int		uid of the content element
	 * @return	string		link for editing a content element
	 */
	function editContent($uid)	{
		$params = '&edit[tt_content]['.intval($uid).']=edit';
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}

	/**
	 * Creates a link for editing a content element
	 *
	 * @param	int		uid of the content element
	 * @return	string		link for editing a content element
	 */
	function editContents($uid)	{
		$out = '<a href="'.$GLOBALS['BACK_PATH'].'db_list.php?id='.intval($uid).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
		return $out;
	}


	/**
	* Creates a link for editing the contents of a page
	*
	* @param	int	pid of the contents of a page
	* @return	string	links for editing the content elements
	*/
// 	function editContents($pid)	{
// 		$params = '&edit[tt_content]['.intval($uid).']=edit';
// 		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit2.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a><br/>';
// 		return $out;
// 	}



	/**
	 * Creates a link for editing a content's category
	 *
	 * @param	int		$tt_content_uid: id
	 * @return	string		form for editing a content's category
	 */
// 	function editContentCategory($tt_content_uid)	{
// 		return true;
// 	}

	/**
	* Creates a form to enter a new mail
	*
	* @return	string	form for entering a new mail
	*/
// 	function createMail()	{
// 		return true;
// 	}

	/**
	* Creates a form to enter a new receiver group definition
	*
	* @return	string	form for entering a new receiver group definition
	*/
// 	function createAttachment()	{
// 		return true;
// 	}


	/**
	 * Returns a table for sending mails
	 *
	 * @param	array		$pids mailings
	 * @return	string		a link for editing all extension settings
	 */
	function tableForMails($pids)	{
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('send').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('schedule').'</td></tr>';
			foreach($pids as $pid) {
				$out .= '<tr><td>'.$pid['title'].'</td>
					<td><form><input type="submit" name="send_now" value="'.$GLOBALS['LANG']->getLL('send_now').'" />
					<input type="hidden" name="id" value="'.$pid['uid'].'" /></form></td>
					<td>'.tx_t3m_main::editTCDirectmailSendtime($pid['uid']).'</td></tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}


	/**
	 * Returns a table for Targetgroup Users
	 *
	 * @param	array		$tid: targetgroup id
	 * @return	string		a table for Targetgroup Users
	 */
	function tableForTargetgroupUsers($tid)	{
		$users = tx_t3m_main::getTargetgroupUsers(intval($tid));


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
				$out .= '</td><td>'.tx_t3m_main::editUser($row['uid']).'</td>
					</tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nousers');
		}
		return $out;
	}

	/**
	 * Returns a table for sending mails
	 *
	 * @param	array		$pids mailings
	 * @return	string		a link for editing all extension settings
	 */
	function tableForScheduledMails($pids)	{
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('send').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('schedule').'</td></tr>';
			foreach($pids as $mail) {
				$out .= '<tr><td>'.$mail['title'].'</td>
					<td><form><input type="submit" name="send_now" value="'.$GLOBALS['LANG']->getLL('send_now').'" />
					<input type="hidden" name="id" value="'.$mail['uid'].'" /></form></td>';
				$out .= '<td';
				if ($mail['tx_tcdirectmail_senttime'] < time()) {
					$out .= ' bgcolor="red" title="'.$GLOBALS['LANG']->getLL('errorNotSent').'"';
				}
				$out .= '>'.strftime('%Y-%m-%d %H:%M:%S',intval($mail['tx_tcdirectmail_senttime'])).'</td>
					<td>'.tx_t3m_main::editTCDirectmailSendtime($mail['uid']).'</td></tr>';
			}
			$out .= '</table';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}

	/**
	 * Returns a table for sending mails
	 *
	 * @param	array		$pids mailings
	 * @return	string		a link for editing all extension settings
	 */
	function tableForTestMails($pids)	{
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('testemail').'</td></tr>';
			foreach($pids as $mail) {
				$out .= '<tr><td>'.$mail['title'].'</td>
					<td><form><input type="submit" name="send_test" value="'.$GLOBALS['LANG']->getLL('send_test').'" />
					<input type="hidden" name="id" value="'.$mail['uid'].'" /></form></td>
					</tr>';
			}
			$out .= '</table';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
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
				<td>'.tx_t3m_main::editPage($row['uid']).'</td>
				<td>'.tx_t3m_main::createUser($row['uid']).'</td>
				</tr>';
		}
		if (!$out) { // no db-result
			$out = '<tr><td colspan=3>'.$GLOBALS['LANG']->getLL('errorSubscriptionExtension').'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}


	/**
	 * Creates a set of links for viewing, editing and deleting a page
	 *
	 * @param	int		$pid: page id
	 * @return	string		table which include links to view, edit and delete the page
	 */
	function tableForPage($pid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title',
			'pages',
			'uid='.intval($pid)
		);
		$out =  '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Edit').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td></tr>';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.$row['title'].'</td>
				<td>'.tx_t3m_main::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_main::editPage($row['uid']).'</td>
				<td>'.tx_t3m_main::getContents($row['uid']).'</td></tr>';
		}
		$out .= '</table>';
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
				<td><a href="'.$GLOBALS['BACK_PATH'].'../'.$fileURL.'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/zoom.gif"  title="'.$GLOBALS['LANG']->getLL('View').'"/></a></td>
				<td><a href="'.$GLOBALS['BACK_PATH'].'file_edit.php?'.$tmpEditHtml.'"><img src="'.$GLOBALS['BACK_PATH'].'gfx/edit_file.gif" title="'.$GLOBALS['LANG']->getLL('Edit').'"/></a></td></tr>';
		} else { // no url given
			$out .= '<tr><td colspan=3>'.$GLOBALS['LANG']->getLL('errorSubscriptionExtension').'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}



	/**
	 * Returns true if actions took place ok.
	 *
	 * @return	boolean		true if actions took place ok.
	 * @todo		implement some checks (return value evaluation)
	 */
	function handlePostVars()	{
		// simply taken from tcdirectmail
		if ($_REQUEST['send_now']) {
			$GLOBALS['TYPO3_DB']->sql_query('UPDATE pages SET tx_tcdirectmail_senttime = '.time().' WHERE uid = '.$_REQUEST['id']);
		}
		if ($_REQUEST['send_test']) {
			$rs = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'pages', 'uid = '.$_REQUEST['id']);
			tx_tcdirectmail_tools::mailForTest($GLOBALS['TYPO3_DB']->sql_fetch_assoc($rs));
		}
		if ($_REQUEST['check_for_spam']) {
			$content = tx_t3m_spam::checkForSpam($_REQUEST['id']);
			$this->myFooter=$content; //$this->doc->section('',$content,0,1);
		}
		if ($_REQUEST['targetgroup']) {
			$content = '<h3>'.$GLOBALS['LANG']->getLL('targetgroupusers').' \''.tx_t3m_main::getTargetGroupName($_REQUEST['targetgroup']).'\'</h3>';
			$content .= tx_t3m_main::tableForTargetgroupUsers($_REQUEST['targetgroup']);
			$this->myFooter=$content; //$this->doc->section('',$content,0,1);
		}
	}

	/**
	 * Returns
	 *
	 * @return
	 */
	function sendMails()	{
		//sending mails:
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('currentlysendingmails').'</h3>';
		$mails = tx_t3m_main::getSendingMails();
		if ($mails) {
			foreach ($mails as $mail) {
				$out .= $mail['title'].'<br />';
			}
		} else {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		}

		//unsentmails:
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('unscheduledmails').'</h3>';
		$mails = tx_t3m_main::getUnsentUnscheduledMails();
		if ($mails) {
			$out .= tx_t3m_main::tableForMails($mails);
		} else {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		}

		//scheduled mails:
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('scheduledmails').'</h3>';
		$mails = tx_t3m_main::getUnsentScheduledMails();
		if ($mails) {
			$out .= tx_t3m_main::tableForScheduledMails($mails);
		} else {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		}

		return $out;
	}


	/**
	 * Returns
	 *
	 * @return
	 */
	function resendMails()	{
		$mails = tx_t3m_main::getSentMails();
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('sentmails').'</h3>';
		$out .= tx_t3m_main::tableForMails($mails);

		return $out;
	}


	/**
	 * Returns
	 *
	 * @return
	 */
	function sendTestMails()	{
		$mails = tx_t3m_main::getUnsentUnscheduledMails();
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('unscheduledmails').'</h3>';
		$out .= tx_t3m_main::tableForTestMails($mails);

		$mails = tx_t3m_main::getUnsentScheduledMails();
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('scheduledmails').'</h3>';
		$out .= tx_t3m_main::tableForTestMails($mails);

		return $out;
	}


	/**
	 * Returns uids of mails which have not been sent
	 *
	 * @return	array		uids of mails which have not been sent
	 */
	function getUnsentMails()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,pid,title',
			'pages',
			'deleted=0 AND hidden=0 AND doktype=189'
		);
		$out = '';
		$i = 0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'pid',
				'tx_tcdirectmail_sentlog',
				'pid='.intval($row['uid'])
			);
			$timessent = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
			if ($timessent == 0) {
				$out[$i]['uid'] = $row['uid'];
				$out[$i]['title'] = $row['title'];
				$i++;
			}
		}
		return $out;
	}

	/**
	 * Returns uids of mails which have not been sent
	 *
	 * @return	array		uids of mails which have not been sent
	 */
	function getUnsentUnscheduledMails()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,pid,title',
			'pages',
			'deleted=0 AND hidden=0 AND doktype=189 AND tx_tcdirectmail_senttime=0'
		);
		$out = '';
		$i = 0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'pid',
				'tx_tcdirectmail_sentlog',
				'pid='.intval($row['uid'])
			);
			$timessent = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
			if ($timessent == 0) {
				$out[$i]['uid'] = $row['uid'];
				$out[$i]['title'] = $row['title'];
				$i++;
			}
		}
		return $out;
	}


	/**
	 * Returns uids of mails which have not been sent
	 *
	 * @return	array		uids of mails which have not been sent
	 */
	function getUnsentScheduledMails()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,pid,title,tx_tcdirectmail_senttime',
			'pages',
			'deleted=0 AND hidden=0 AND doktype=189 AND tx_tcdirectmail_senttime > 0',
			'',
			'tx_tcdirectmail_senttime'
		); // AND tx_tcdirectmail_senttime < '.time()
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// check if page has been sent:;
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'pid,sendtime',
				'tx_tcdirectmail_sentlog',
				'pid='.intval($row['uid'])
			);
			$timessent = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
			if ($timessent == 0) {
				$out[] = $row;
			}
		}
		return $out;
	}

	/**
	 * Returns uids of mails which have been sent
	 *
	 * @return	array		uids of mails which have been sent
	 */
	function getSentMails()	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,pid,title',
			'pages',
			'deleted=0 AND hidden=0 AND doktype=189'
		);
		$out = '';
		$i = 0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// check if page has been sent:;
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'pid,sendtime',
				'tx_tcdirectmail_sentlog',
				'pid='.intval($row['uid'])
			);
			$timessent = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
			if ($timessent > 0) {
				$out[$i]['timessent'] = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
				$out[$i]['uid'] = $row['uid'];
				$out[$i]['title'] = $row['title'];
	// 			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
	// 				$out[$i]['timesent'] =  $row['sendtime'];
	// 			}
				$i++;
			}
		}
		return $out;
	}


	/**
	 * Returns uids of mails which are currently being sent (60 seconds before pages.tx_tcdirectmail_senttime and after tx_tcdirectmail_sentlog.)
	 *
	 * @return	array		uids of mails which have been sent
	 */
	function getSendingMails()	{
		$time = time() + 10;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,pid,title,tx_tcdirectmail_senttime',
			'pages',
			'deleted=0 AND hidden=0 AND doktype=189 AND tx_tcdirectmail_senttime > 0 AND tx_tcdirectmail_senttime <= '.$time
		);
		$out = '';
		$i = 0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			if ((intval(time()) - intval($row['tx_tcdirectmail_senttime'])) <= 90) { // to be sent in the next 60 seconds
			// "about to be sent" or "currently sending"
			// more correct version of currently sending: select * from tx_tcdirectmail_sentlog where 'sendtime' = '' (but 'begintime' already set)
				$out[$i]['uid'] = $row['uid'];
				$out[$i]['title'] = $row['title'];
			// check if already sent before:
				$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'pid,sendtime',
					'tx_tcdirectmail_sentlog',
					'pid='.intval($row['uid'])
				);
				$timessent = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
				if ($timessent > 0) {
					$out[$i]['timessent'] = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
				}
				$i++;
			}
		}
		return $out;
	}





	/**
	* Returns an array with the typoscript constants of the template of the root page of the website
	*
	* @return	array	"global" typoscript constants array to be used by other functions
	*/
	function getTSConstants()	{ 	//	$PageTSconfig = t3lib_BEfunc::getPagesTSconfig('0'); does not work
		require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');
// 		var $pageId, $templateId;
		$ts = t3lib_div::makeInstance('t3lib_tsparser_ext');
// 		$ts->init();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( //from pages: "is_siteroot=1" is not needed (not always the case), a roottemplate should be there however
			'uid,pid',
			'sys_template',
			'root=1 '.t3lib_BEfunc::deleteClause('sys_template')
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$pageId = $row['pid'];
			$templateId = $row['uid'];
		}
// 		$tplRow = $tmpl->ext_getFirstTemplate($pageId,$template_uid);
// 		$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine = t3lib_BEfunc::BEgetRootLine($pageId); //$sys_page->getRootLine($pageId);
		$ts->runThroughTemplates($rootLine,$templateId);	// This generates the constants/config + hierarchy info for the template.
		$theConstants = $ts->generateConfig_constants();	// The editable constants are returned in an array.
		$ts->ext_categorizeEditableConstants($theConstants);	// The returned constants are sorted in categories, that goes into the $tmpl->categories array
		return $theConstants;
	}

	/**
	 * Returns all module settings
	 *
	 * @return	array		the extension setting
	 */
	function getExtensionConfig()	{
		$out = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		return $out;
	}

	/**
	 * Returns all modules settings
	 *
	 * @return	array		the extension setting
	 */
	function getExtensionConfigs()	{
		foreach($requiredExtensions as $value) {
			$out[$value] = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$value]);
		}
		return $out;
	}



	/**
	* Loads context sensitive help
	*
	* @return	array	the extension setting
	*/
// 	function loadCSH()	{
// 		// Descriptions:
// 		$this->descrTable = '_MOD_'.$GLOBALS['MCONF']['name'];
// 		if ($BE_USER->uc['edit_showFieldHelp']) {
// 			$GLOBALS['LANG']->loadSingleTableDescription($this->descrTable);
// 		}
// 	}



	/**
	 * Returns some system infos
	 *
	 * @return	string		some interesting stuff
	 */
	function testSomeStuff()	{

		$var = tx_t3m_main::getTimestampFromAge(36);
// 		$var2 = tx_t3m_main::getTimestampFromAge(37);
		//$GLOBALS['BE_USER'];$GLOBALS['TYPO3_DB'], t3lib_extMgm, $BE_USER, 't3lib_BEfunc', $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3m']; $GLOBALS['_EXTKEY'], $GLOBALS['MCONF'], $GLOBALS['EM_CONF']['t3m'], $GLOBALS['ICON_PATH']$this->myConf

		$class = 't3lib_BEfunc'; //t3lib_extMgm

 		$out[0] = get_declared_classes();
		$out[1] = get_class_methods($class);
		$out[2] = get_defined_constants();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( //from pages: "is_siteroot=1" is not needed (not always the case), a roottemplate should be there however
			'*',
			'be_groups',
			'uid=10'
		);
		$out = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

 		t3lib_div::debug($out);

		return $out;
	}

}


?>