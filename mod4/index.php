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

require_once (t3lib_extMgm::extPath('t3m').'class.tx_t3m_main.php');

/**
 * Module 'T3M Mail' for the 't3m' extension.
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class  tx_t3m_module4 extends t3lib_SCbase {
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
				'oneoffstatistics' => $LANG->getLL('oneoffstatistics'),
				'newsletterstatistics' => $LANG->getLL('newsletterstatistics'),
				'campaignstatistics' => $LANG->getLL('campaignstatistics'),
				'sendstatistics' => $LANG->getLL('sendstatistics'),
				'openstatistics' => $LANG->getLL('openstatistics'),
				'clickstatistics' => $LANG->getLL('clickstatistics'),
// 				'mailstatistics' => $LANG->getLL('mailstatistics'),
// 				'contentstatistics' => $LANG->getLL('contentstatistics'),
				'userstatistics' => $LANG->getLL('userstatistics'),
// 				'maintenance' => $LANG->getLL('maintenance'),
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
			case oneoffstatistics:
				$content='<h2>'.$LANG->getLL('oneoffstatistics').'</h2>';
				$content.=tx_t3m_stats::statsForOneOffMailings();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case newsletterstatistics:
				$content='<h2>'.$LANG->getLL('newsletterstatistics').'</h2>';
				$content.=tx_t3m_stats::statsForNewsletters();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case campaignstatistics:
				$content='<h2>'.$LANG->getLL('campaignstatistics').'</h2>';
				$content.=tx_t3m_stats::statsForCampaigns();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case sendstatistics:
				$content='<h2>'.$LANG->getLL('sendstatistics').'</h2>';
				$content.=$LANG->getLL('descriptionsendstatistics');
				$content.='<h3>'.$LANG->getLL('yearlystatistics').'</h3>'; // get data for last 3 years
				$year = (date('Y')-2);
				$content.=tx_t3m_stats::yearlyStatsForMails($year);
				$year = (date('Y')-1);
				$content.=tx_t3m_stats::yearlyStatsForMails($year);
				$content.=tx_t3m_stats::yearlyStatsForMails();
				$content.='<h3>'.$LANG->getLL('statsMostReceivers').'</h3>';
				$content.=tx_t3m_stats::statsForTCDirectmails();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case openstatistics:
				$content='<h2>'.$LANG->getLL('openstatistics').'</h2>';
				$content.=$LANG->getLL('descriptionopenstatistics');
				$content.=tx_t3m_stats::statsForOpenedMails();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case clickstatistics:
				$content='<h2>'.$LANG->getLL('clickstatistics').'</h2>';
				$content.=$LANG->getLL('descriptionclickstatistics');
				$content.=tx_t3m_stats::statsForClickedMails();
				$this->content.=$this->doc->section('',$content,0,1);
			break;

// 			case contentstatistics:
// 				$content='<h2>'.$LANG->getLL('contentstatistics').'</h2><br />'; // shows content which is not sent yet and stats of content grouped by categories
// 				$content.=tx_t3m_stats::getStatsForContent();
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
// 			case bouncestatistics:
// 				$content='<h3>'.$LANG->getLL('bouncestatistics').'</h3>';
// 				$content.=tx_t3m_stats::getStatsForBounces();
// 				$this->content.=$this->doc->section('',$content,0,1);
// 			break;
			case userstatistics:
				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('userstatistics').'</h2>';
				$content.=$LANG->getLL('descriptionuserstatistics');
				$content.='<h3>'.$LANG->getLL('yearlystatistics').'</h3>'; // get data for last 3 years
				$content.=$LANG->getLL('descriptionyearlyuserstatistics');
				$year = (date('Y')-2);
				$content.=tx_t3m_stats::yearlyStatsForUsers($year);
				$year = (date('Y')-1);
				$content.=tx_t3m_stats::yearlyStatsForUsers($year);
				$content.=tx_t3m_stats::yearlyStatsForUsers();
				$content.=tx_t3m_stats::statsForUsers();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case maintenance:
				$content='<h2>'.$LANG->getLL('maintenance').'</h2>';
				$content.='<h3>'.$GLOBALS['LANG']->getLL('maintenanceLogs').'</h3>';
				$content.=$LANG->getLL('descriptionMaintenanceLogs').'<br />';
				$content.=tx_t3m_main::maintenanceLogs();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod4/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod4/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3m_module4');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>