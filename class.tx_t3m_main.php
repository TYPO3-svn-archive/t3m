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
	 * @todo	Move all graphical typo3 customizing (CSS, Icons) to an own typo3-macher-skin extension
	 */
	function main()	{
		global $TCA_DESCR,$TCA,$CLIENT;

		// initialize variables and include libs
		tx_t3m_main::init();

		// get postvars, prepare data for module and write data to footer to include later
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

		// Content from index.php
		$this->moduleContent(); //

		// Content depending on actionhandler
		$this->content.=$this->doc->spacer(10);
		$this->content.=$this->myFooter;

		// ShortCut
		if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}
	}

	/**
	 * Initialize some variables
	 *
	 * @return	void		nothing
	 * @todo: Evaluate and choose best method and write to $this->tcDefVals. (Make Lynx only the default fetch method if it is installed and exectuable (linux). Otherwise use 'tx_tcdirectmail_plain_simple'. Method "type99" seems not to be a good choice, i got errors and Lynx seems to have troubles with umlauts.
	 */
	function init()	{
		require_once(t3lib_extMgm::extPath('tcdmailstats').'modfunc1/class.tx_tcdmailstats_modfunc1.php');
		require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_tools.php');

		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_addresses.php');

		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_mailings.php');
		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_spam.php');

		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_send.php');

		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_bounce.php');
		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_stats.php');

		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_settings.php');
		require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_postinstall.php');

		$this->extKey = 't3m'; // how to get dynamically? $_EXTKEY is wrong and $EM_CONF empty..
		$this->demo = 0; //enable or disable demo data
		$this->debug = 0; //enable or disable debugging

		$this->myConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
// 		$this->requiredExtensions  = array( //$EM_CONF[t3m]['constraints']['depends'] does NOT work!
// 			'rs_userimp',
// 			'sr_feuser_register',
// 			'static_info_tables'
// 			);
		$this->rootTS = tx_t3m_settings::getTSConstants();

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

		$this->tcOverrideVals =  '&overrideVals[pages][doktype]=189&overrideVals[pages][hidden]=0&overrideVals[pages][tx_tcdirectmail_sendername]='.$this->myConf['sender_name'].'&overrideVals[pages][tx_tcdirectmail_senderemail]='.$this->myConf['sender_email'].'&overrideVals[pages][tx_tcdirectmail_test_target]=tx_tcdirectmail_targets_'.$this->myConf['targetTest'].'&overrideVals[pages][tx_tcdirectmail_dotestsend]='.$this->myConf['tx_tcdirectmail_dotestsend'].'&overrideVals[pages][tx_tcdirectmail_plainconvert]='.$this->myConf['tcdirectmail_plain'].'&overrideVals[pages][tx_tcdirectmail_spy]='.$this->myConf['tcdirectmail_spy'].'&overrideVals[pages][tx_tcdirectmail_register_clicks]='.$this->myConf['tcdirectmail_register_clicks'];

		$this->columnsOnlyFeuser = '&columnsOnly=gender,first_name,last_name,telephone,fax,email,company,address,zip,city,tx_t3m_country,date_of_birth,username,password,usergroup,tx_t3m_categories,module_sys_dmail_html,disable,deleted,tx_t3m_salutation';
		$this->defValsFeuser = '&defVals[fe_users][tx_t3m_country]='.$this->myConf['static_countries_uid'];

		$this->iconImgCreate = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/new_record.gif').' title="'.$GLOBALS['LANG']->getLL('New').'" alt="'.$GLOBALS['LANG']->getLL('New').'" />'; //new_el.gif
		$this->iconImgView = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/zoom.gif').' title="'.$GLOBALS['LANG']->getLL('View').'" alt="'.$GLOBALS['LANG']->getLL('View').'" />';
		$this->iconImgEdit = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/edit2.gif').' title="'.$GLOBALS['LANG']->getLL('Edit').'" alt="'.$GLOBALS['LANG']->getLL('Edit').'" />';
		$this->iconImgOk = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_ok2.gif').' title="'.$GLOBALS['LANG']->getLL('OK').'" alt="'.$GLOBALS['LANG']->getLL('OK').'" />';
		$this->iconImgWarning = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_warning.gif').' title="'.$GLOBALS['LANG']->getLL('Warning').'" alt="'.$GLOBALS['LANG']->getLL('Warning').'" />';
		$this->iconImgNote = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_note.gif').' title="'.$GLOBALS['LANG']->getLL('Note').'" alt="'.$GLOBALS['LANG']->getLL('Note').'" />';
		$this->iconImgError = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/icon_fatalerror.gif').' title="'.$GLOBALS['LANG']->getLL('Error').'" alt="'.$GLOBALS['LANG']->getLL('Error').'" />';



	}

	/**
	 * Creates a link for a new sysfolder
	 *
	 * @return	string		link for a new sysfolder
	 */
// 	function createSysfolder()	{
// 		$params = '&edit[pages][0]=new&defVals[pages][title]=t3ms&defVals[pages][doktype]=254&columnsOnly=title,doktype';
// 		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/sysf.gif','width="16" height="16"').' title="folder" alt="" />'.$GLOBALS['LANG']->getLL('createSysfolder').'</a><br/>';
// 		return $out;
// 	}

	/**
	* Creates a link for a new folder
	*
	* @return	string	link for a new folder
	*/
// 	function createFolder()	{ // t3lib_extFileFunctions func_newfolder
// 		global $FILEMOUNTS;
// 		require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');
// 		require_once(PATH_t3lib.'class.t3lib_extfilefunc.php');
// 		$out = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'],'gfx/i/sysf.gif','width="16" height="16"').' title="folder" alt="" />I just created folder ';
// 		$out .= $file['newfolder'][0]['target'] = constant('PATH_site').$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir']; // absolute path required (like '/var/www/html/fileadmin'), relative path  ../../../../$GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'] or t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') do not work
// 		$out .= $file['newfolder'][0]['data'] = 't3mfolder'; //name of new folder
// 		$myFileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
// 		$myFileProcessor->init($FILEMOUNTS, $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
// 		$myFileProcessor->init_actionPerms($GLOBALS['BE_USER']->user['fileoper_perms']);
// 		$myFileProcessor->start($file);
// 		$myFileProcessor->processData();
// // 		$out .= debug($GLOBALS['BE_USER']->returnFilemounts());
// 		return '<a href="#" onclick="">'.$out.'</a><br/>';
// 	}


	/**
	 * Returns true if actions took place ok.
	 *
	 * @return	boolean		true if actions took place ok.
	 * @todo	Implement some security checks
	 * @todo	Improve handler to show or hide tables if they become too full for massive lists ("scalability" and "eye-candy")
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
			$content = '<h3>'.$GLOBALS['LANG']->getLL('targetgroupusers').' \''.tx_t3m_addresses::getTargetGroupName($_REQUEST['targetgroup']).'\'</h3>';
			$content .= tx_t3m_addresses::tableForTargetgroupUsers($_REQUEST['targetgroup']);
			$this->myFooter=$content; //$this->doc->section('',$content,0,1);
		}
		if ($_REQUEST['receivergroup']) {
			$content = '<h3>'.$GLOBALS['LANG']->getLL('receivergroupusers').'  \''.tx_t3m_addresses::getReceiverGroupName($_REQUEST['receivergroup']).'\'</h3>';
			$content .= tx_t3m_addresses::tableForReceivers($_REQUEST['receivergroup']);
			$this->myFooter=$content; //$this->doc->section('',$content,0,1);
		}
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

}


?>