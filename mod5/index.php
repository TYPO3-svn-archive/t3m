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


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:t3m/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

require_once ('../class.tx_t3m_main.php');

/**
 * Module 'T3M Mail' for the 't3m' extension.
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class  tx_t3m_module5 extends t3lib_SCbase {
	var $pageinfo;
	/**
	* Initializes the Module
	* @return	void
	*/
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();
		/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}

	/**
	* Adds items to the ->MOD_MENU array. Used for the function menu selector.
	*
	* @return	void
	*/
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'settings' => $LANG->getLL('settings')
// 				'bounces' => $LANG->getLL('bounces')
// 				'actions' => $LANG->getLL('actions'),
			)
		);
		if ($GLOBALS['BE_USER']->isAdmin()) {
			$this->MOD_MENU['function']['bounces'] = $LANG->getLL('bounces');
			$this->MOD_MENU['function']['infos'] = $LANG->getLL('infos');
		}
		parent::menuConfig();
	}

	/**
	* Main function of the module. Write the content to $this->content
	* If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	*
	* @return	[type]		...
	*/
	function main()	{
		tx_t3m_main::main();
	}

	/**
	* Prints out the module HTML
	*
	* @return	void
	*/
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	* Generates the module content
	*
	* @return	void
	*/
	function moduleContent()	{
		global $LANG;
		switch((string)$this->MOD_SETTINGS['function'])	{
			case settings: // bouncehandling set here
				require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_tools.php');
				require_once(t3lib_extMgm::extPath('t3m').'class.tx_tcdirectmail_sysstat.php');
				require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_settings.php');

				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/sys_action.gif" />'.$LANG->getLL('settings').'</h2>';
				$content.=$LANG->getLL('descriptionSettings');
				$content.='<br />'.$LANG->getLL('CheckingSettings');

				$content.='<h3>'.$LANG->getLL('checkExternalSettings').'</h3>';
				$content.=tx_t3m_settings::checkExternalSettings();
				if ($GLOBALS['BE_USER']->isAdmin()) {
					$content.='<br />'.tx_t3m_settings::editExternalExtensionConfig();
				}
// 				$content.='<h3>'.$LANG->getLL('checkSystemSettings').'</h3>';
				$content.=tx_tcdirectmail_sysstat::viewSysStatus(); //lynx, fetchmail, cronjobs
// 				$content.='<br />'.tx_t3m_settings::checkForLynx();
// 				$content.='<br />'.tx_t3m_settings::checkForMailCronjob();
// 				$content.='<br />'.tx_t3m_settings::checkForBounceCronjob();
				$content.='<h3>'.$GLOBALS['LANG']->getLL('checkForSpamProgram').'</h3>';
				$content.=tx_t3m_settings::checkForSpamProgram();

				$content.='<h3>'.$LANG->getLL('checkOwnSettings').'</h3>';
				$content.=tx_t3m_settings::checkOwnSettings();
				if ($GLOBALS['BE_USER']->isAdmin()) {
					$content.='<br />'.tx_t3m_settings::editOwnExtensionConfig();
					$content.='<br />'.tx_t3m_postinstall::postinstallActions();
				}
				$this->content.=$this->doc->section('',$content,0,1);
			break;
// 			case actions:
// 				$content=$LANG->getLL('actions');
//
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
			case infos:
				$content=$LANG->getLL('infos');
				$content.='just some debug testing stuff here...<br />'.tx_t3m_main::testSomeStuff();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case bounces:
				$content=$LANG->getLL('bounces');
				$content.='<br /><h3>'.$LANG->getLL('bouncesettings').'</h3>';
				$content.='<br />'.tx_t3m_bounce::checkForBounceMail();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod5/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod5/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3m_module5');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>