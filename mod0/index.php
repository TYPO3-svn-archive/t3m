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
// $BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

require_once ('../class.tx_t3m_main.php');

/**
 * Module 'T3M Mail' for the 't3m' extension.
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class  tx_t3m_module1 extends t3lib_SCbase {
				var $pageinfo;
				//var $modwebdmail;
				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS,$modwebdmail;

					parent::init();
					//$modwebdmail = new mod_web_dmail();
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
 							'infos' => $LANG->getLL('infos')
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
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
// 					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
// 					$access = is_array($this->pageinfo) ? 1 : 0;
//
// 					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;
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
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
// 					} else {
// 							// If no access or if ID == zero
//
// 						$this->doc = t3lib_div::makeInstance('mediumDoc');
// 						$this->doc->backPath = $BACK_PATH;
//
// 						$this->content.=$this->doc->startPage($LANG->getLL('title'));
// 						$this->content.=$this->doc->header($LANG->getLL('title'));
// 						$this->content.=$this->doc->spacer(5);
// 						$this->content.=$this->doc->spacer(10);
// 					}
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
// 						global $my_tx_t3m_main = new tx_t3m_main('php5 or php4 constructors');
						switch((string)$this->MOD_SETTINGS['function'])	{

// ADRESSES
						case import: // without double-opt-in!! which class or EXT to use?
							$content=$LANG->getLL('import');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case subscriptionforms: // show pages on which the form is included and a link for a new page to create a new form
							$content=$LANG->getLL('subscriptionforms');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case subscriptions: // shows list of
							$content=$LANG->getLL('subscriptions');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case deregistrations: // shows users who are still in the system but who cancelled the newsletter
							$content=$LANG->getLL('deregistrations');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case pending:
							$content=$LANG->getLL('pending'); // shows pending users
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case blocked:
							$content=$LANG->getLL('blocked');
							$this->content.=$this->doc->section('',$content,0,1);
						break;


						case bounced:
							$content=$LANG->getLL('bounced');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case export:
							$content=$LANG->getLL('export');
							$this->content.=$this->doc->section('',$content,0,1);
						break;

// TARGET GROUPS
						case targetgroupdefinitions: // without recipients intervention (by age, sex, zip, ...)
							$content=$LANG->getLL('targetgroupdefinitions');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case targetgroups: //
							$content=$LANG->getLL('targetgroups');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case categories: // recipients choose themselves (at some subscriptionform)
							$content=$LANG->getLL('categories');
							$this->content.=$this->doc->section('',$content,0,1);
						break;

// CONTENT
						case content: // tt_content parts group by pages
							$content=$LANG->getLL('content');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case pages:
							$content=$LANG->getLL('pages');
							$this->content.=$this->doc->section('',$content,0,1);
						break;

						case makeNewPage:
							$content=$LANG->getLL('makeNewPage');
							$content.='<br /><br />'.tx_t3m_main::makeNewSysfolder();
							$this->content.=$this->doc->section('',$content,0,1);
						break;


						case directmails: //show legacy stuff from directmail and tcdirectmail
							$content=$LANG->getLL('directmails');
							$content.='<br /><br />'.tx_t3m_main::getDirectMailFolders();
							$content.='<br /><br />'.tx_t3m_main::getDirectMails();
							$content.='<br /><br />'.tx_t3m_main::getDirectMailsTc();
							$this->content.=$this->doc->section('',$content,0,1);
						break;

// SEND MAILS
						case sendtestmail: // send test mails,
							$content=$LANG->getLL('sendtestmail');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case sendtestcampaign: // send test campaing,
							$content=$LANG->getLL('sendtestcampaign');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case sendcampaign: // send test campaing,
							$content=$LANG->getLL('sendcampaign');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case automaticsend: // configure automatic test campaing,
							$content=$LANG->getLL('sendcampaign');
							$this->content.=$this->doc->section('',$content,0,1);
						break;


// STATISTICS
						case campaignstatistics:
							$content=$LANG->getLL('campaingstatistics');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case mailstatistics:
							$content=$LANG->getLL('mailstatistics');
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case contentstatistics:
							$content=$LANG->getLL('contentstatistics'); // shows content which is not sent yet and stats of content grouped by categories
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case userstatistics:
							$content=$LANG->getLL('userstatistics');
							$this->content.=$this->doc->section('',$content,0,1);
						break;

// DEFINE BEHAVIOR
						case settings:
							$content=$LANG->getLL('settings'); // bouncehandling here ?
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case actions: // send mails,
							$content=$LANG->getLL('actions');
							$this->content.=$this->doc->section('',$content,0,1);
						break;


// INFOS
						case infos: //system infos for the user
							$content=$LANG->getLL('infos');
							$content.='- This is version no: ... (latest in repository is ...)<br />- Extensions used: ...<br />- Mails sent: ...<br />- Total users: ...';
							$this->content.=$this->doc->section('',$content,0,1);
						break;
						case debug: //system infos for the developer
							$content=$LANG->getLL('debug');
							$content.='I am here: '.substr(t3lib_extMgm::extPath(t3m),strlen(PATH_site)).'mod1/index.php<br />';
							$content.='<br />This is the GET/POST vars sent to the script:<br />GET:'.t3lib_div::view_array($_GET).'<br />POST:'.t3lib_div::view_array($_POST).'<br />';
//							fread('/var/log/httpd/error_log');
// 							$content.=debug($LANG);
							$this->content.=$this->doc->section('',$content,0,1);
						break;
					}
				}
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod0/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod0/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3m_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();



?>