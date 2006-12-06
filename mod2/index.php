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
class  tx_t3m_module2 extends t3lib_SCbase {
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
				'createdirectmails' => $LANG->getLL('NewMail'),
				'oneoffmailings' => $LANG->getLL('OneOffMailings'),
				'newsletter' => $LANG->getLL('Newsletters'),
				'campaigns' => $LANG->getLL('campaigns'),
// 				'emails' => $LANG->getLL('emails'),
// 				'content' => $LANG->getLL('content'),
// 				'salutations' => $LANG->getLL('salutations'),
				'categories' => $LANG->getLL('categories'),
/*				'createCategory' => $LANG->getLL('createCategory'),
				'createCampaign' => $LANG->getLL('createCampaign'),
				'createPage' => $LANG->getLL('createPage'),
				'createContent' => $LANG->getLL('createContent'),
				'createSalutations' => $LANG->getLL('createSalutations'),
				'editCategory' => $LANG->getLL('editCategory'),
				'editCampaign' => $LANG->getLL('editCampaign'),
				'editPage' => $LANG->getLL('editPage'),
				'editContent' => $LANG->getLL('editContent'),
				'editSalutations' => $LANG->getLL('editSalutations'),
*/
// 				'directmails' => $LANG->getLL('directmails')
			)
		);
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
			case createdirectmails:
				$content='';
				$content.='<h3>'.$LANG->getLL('NewMail').'</h3>';
				$content.=$LANG->getLL('descriptionNewMail').'<br />';
				$content.=tx_t3m_main::createTCDirectmailForReceivergroups();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case oneoffmailings:
				$content='<h2>'.$LANG->getLL('oneoffmailings').'</h2>';
				$content.=$LANG->getLL('descriptionOneOffMailings');
				$content.='<h3>'.$LANG->getLL('NewOneOffMailing').'</h3>';
				$content.=tx_t3m_main::createTCDirectmailOneOffMailing();
				$content.='<br />'.tx_t3m_main::getTCDirectmailsOneOffMailings();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case newsletter:
				$content='<h2>'.$LANG->getLL('newsletters').'</h2>';
				$content.=$LANG->getLL('descriptionNewsletters');
				$content.='<h3>'.$LANG->getLL('NewNewsletter').'</h3>';
				$content.=tx_t3m_main::createTCDirectmailNewsletter();
				$content.='<br />'.tx_t3m_main::getTCDirectmailsNewsletters();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case campaigns:
				$content='<h2>'.$LANG->getLL('campaigns').'</h2>';
				$content.=$LANG->getLL('descriptionCampaigns');
				$content.='<br /><h3>'.$LANG->getLL('NewCampaign').'</h3>';
				$content.=tx_t3m_main::createCampaign();
				$content.='<br /><h3>'.$LANG->getLL('getCampaigns').'</h3>';
				$content.=tx_t3m_main::tableForCampaigns();
				$content.='<br /><h3>'.$GLOBALS['LANG']->getLL('MailsForCampaigns').'</h3>';
// 				$content.=tx_t3m_main::createTCDirectmailForCampaign();
				$content.=tx_t3m_main::getTCDirectmailsForCampaigns();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case emails:
				$content='<h2>'.$LANG->getLL('emails').'</h2>';
				$content.='<br /><h3>'.$LANG->getLL('createEmail').'</h3>';
				$content.=tx_t3m_main::createTCDirectmail();
				$content.='<br /><h3>'.$LANG->getLL('getEmails').'</h3>';
				$content.=tx_t3m_main::getTCDirectmails();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
// 			case content:
// 				$content='<h2>'.$LANG->getLL('content').'</h2>';
// 				$content.='<br /><h3>'.$LANG->getLL('createContent').'</h3>';
// 				$content.=tx_t3m_main::createContent();
// 				$content.='<br /><h3>'.$LANG->getLL('getContents').'</h3>';
// 				$content.=tx_t3m_main::getContents();
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
			case salutations:
				$content='<h2>'.$LANG->getLL('salutations').'</h2>';
				$content.='<br /><h3>'.$LANG->getLL('createSalutation').'</h3>';
				$content.=tx_t3m_main::createSalutation();
				$content.='<br /><h3>'.$LANG->getLL('getSalutations').'</h3>';
				$content.=tx_t3m_main::getSalutations();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
// 			case createCategory:
// 				$content=$LANG->getLL('createCategory');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case createCampaign:
// 				$content=$LANG->getLL('createCampaign');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case createPage:
// 				$content=$LANG->getLL('createPage');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case createContent:
// 				$content=$LANG->getLL('createContent');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case createSalutations:
// 				$content=$LANG->getLL('createSalutations');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case editCategory:
// 				$content=$LANG->getLL('editCategory');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case editCampaign:
// 				$content=$LANG->getLL('editCampaign');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case editPage:
// 				$content=$LANG->getLL('editPage');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case editContent:
// 				$content=$LANG->getLL('editContent');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case editSalutations:
// 				$content=$LANG->getLL('editSalutations');
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
			case directmails: //show legacy stuff from directmail and tcdirectmail
				$content=$LANG->getLL('directmails');
				$content.='<br /><br />'.tx_t3m_main::getDirectMailFolders();
				$content.='<br /><br />'.tx_t3m_main::getDirectMails();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case categories:// recipients choose themselves (at some subscriptionform)
					//IDEA: import categories from: fe_users:module_sys_dmail_category, tt_address:module_sys_dmail_category, sys_dmail_category:category, tt_news_cat:title+description ? (OR just use?)
				tx_t3m_main::updateAllCalculatedCategories();
				$content='<h2>'.$LANG->getLL('categories').'</h2>';
				$content.=$LANG->getLL('descriptionCategories');
				$content.='<br /><h3>'.$LANG->getLL('createCategory').'</h3>';
				$content.=tx_t3m_main::createCategory();
				$content.='<br /><h3>'.$LANG->getLL('getCategories').'</h3>';
				$content.=tx_t3m_main::getCategories();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod2/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod2/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3m_module2');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();


?>