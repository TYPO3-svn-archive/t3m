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
class  tx_t3m_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $extensionKey;

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

		//if ($GLOBALS['BE_USER']->isAdmin()) {
			$this->MOD_MENU['function']['import'] = $LANG->getLL('import');
		//}

		$this->MOD_MENU['function'] += Array (
				'create' => $LANG->getLL('Create'),
				'subscriptionforms' => $LANG->getLL('subscriptionforms'),
				'groups'  => $LANG->getLL('Groups'),
				'targetgroups' => $LANG->getLL('targetgroups'),
				'receivers' => $LANG->getLL('Receivers'),
				'export' => $LANG->getLL('Export'),
				);

// // 				'groups' => Array ( // this kind of menu would be nice, but multidimensional menu is not possible here.
// // 					'pending' => $LANG->getLL('pending'),
// // 					'subscriptions' => $LANG->getLL('subscriptions'),
// // 					'tests' => $LANG->getLL('testGroup'),
// // 					'deregistrations' => $LANG->getLL('deregistrations'),
// // 					'blocked' => $LANG->getLL('blocked'),
// // 					'bounced' => $LANG->getLL('bounced'),

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
		global $LANG, $TYPO3_CONF_VARS;

		$tmpl = tx_t3m_main::getTSConstants();

		switch((string)$this->MOD_SETTINGS['function'])	{
			case import:
				// without double-opt-in!! which class or EXT to use?//HOW TO ""integrate"" not just link rs_userimp?

				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('importusers').'</h2>';
				$content.=$LANG->getLL('descriptionImport');

				$content.='<br /><h3>'.$LANG->getLL('importreceivers').'</h3>';
				$content.= $LANG->getLL('descriptionImportReceivers').'<br />';
				$content.=tx_t3m_main::importReceivers();

				$content.='<br /><h3>'.$LANG->getLL('importfeusers').'</h3>';
				$content.= $LANG->getLL('descriptionImportFeusers').'<br />';
				$content.=tx_t3m_main::importFeusers();

				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case create:
				//getSubscriptionSysfolder() gives whole folder

				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('Create').'</h2>';
				$content.=$LANG->getLL('descriptionCreate').'<br />';

// 				$content.='<br /><h3>'.$LANG->getLL('createfegroup').'</h3>';
// 				$content.=$LANG->getLL('descriptionCreateFegroup').'<br />';
// 				$content.=tx_t3m_main::createFegroup();

				$content.='<br /><h3>'.$LANG->getLL('createfeuser').'</h3>';
				$content.=$LANG->getLL('descriptionCreateFeuser').'<br />';
				$content.=tx_t3m_main::createFeuser();

				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case subscriptionforms:
				// ideas -
				// 1:show currently available fields
				// 2:link for editing the whole template constants of the template (find template+edit)
				// 3:form for direct altering (involving lots of regex and parse TS)
				// 4:form for setting it in an extension's setting value

				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('subscriptionforms').'</h2>';
				$content.=$LANG->getLL('descriptionSubscriptionforms').'<br />';

				$content.='<h3>'.$LANG->getLL('subscriptionformtemplate').'</h3>';
				$content.=$LANG->getLL('descriptionSubscriptionformTemplate').'<br />';
				$content.=tx_t3m_main::getSubscriptionPageTemplate();

				$content.='<br /><h3>'.$LANG->getLL('subscriptionpage').'</h3>';
				$content.=$LANG->getLL('descriptionSubscriptionPage').'<br />';
				$content.=tx_t3m_main::getSubscriptionPage();

				$content.='<br /><h3>'.$LANG->getLL('confirmationpage').'</h3>';
				$content.=$LANG->getLL('descriptionConfirmationPage').'<br />';
				$content.=tx_t3m_main::getSubscriptionConfirmationPage();

				$content.='<br /><h3>'.$LANG->getLL('loginpage').'</h3>';
				$content.=$LANG->getLL('descriptionLoginPage').'<br />';
				$content.=tx_t3m_main::getLoginPage();

				$content.='<br /><h3>'.$LANG->getLL('profileeditpage').'</h3>';
				$content.=$LANG->getLL('descriptionProfileEditPage').'<br />';
				$content.=tx_t3m_main::getSubscriptionEditPage();

				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case groups:
				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('Groups').'</h2>';
				$content.=$LANG->getLL('descriptionGroups').'<br />';
				$content.=tx_t3m_main::getGroups();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case targetgroups:
				tx_t3m_main::updateAllCalculatedTargetgroupUsers();
				// without recipients intervention (by age, sex, zip, ...)
				// IDEA: show a form for creating/deleting profiles, form options: fe_users:gender, fe_users:date_of_birth, fe_users:zone (bundesland), fe_users:zip (plz), big selectorbox for fe_groups, big selectorbox for fe_users (and a tooltip to encourage to use groups rather than single users.)
				// IDEA: show current definition names, number of users who fit into this group and delete button
				//IDEA: hierarchical view+editing of frontend groups, treelike-group-editing-extension?

				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('targetgroupdefinitions').'</h2>';
				$content.=$LANG->getLL('descriptionTargetgroupDefinitions');
				$content.='<br /><h3>'.$LANG->getLL('createTargetgroupdefinition').'</h3>';
				$content.=tx_t3m_main::createTargetgroupDefinition();
				$content.='<br /><h3>'.$LANG->getLL('getTargetgroupdefinitions').'</h3>';
				$content.=tx_t3m_main::getTargetgroupDefinitions();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case receivers:
				tx_t3m_main::updateAllCalculatedReceivers();
				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('Receivers').'</h2>';
				$content.=$LANG->getLL('descriptionReceivers');
				$content.='<br /><h3>'.$LANG->getLL('createReceivergroup').'</h3>';
				$content.=tx_t3m_main::createReceivergroup();
				$content.='<br /><h3>'.$LANG->getLL('getReceivergroups').'</h3>';
				$content.=tx_t3m_main::getReceivergroups();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
			case export:
				$content='<h2><img src="'.$GLOBALS['BACK_PATH'].'gfx/i/fe_users.gif" />'.$LANG->getLL('Export').'</h2>';
				$content.='<h3>'.$LANG->getLL('User').'</h3>';
				$content.=$LANG->getLL('descriptionExportUsers').'<br />';
				$content.=tx_t3m_main::export();
				$this->content.=$this->doc->section('',$content,0,1);
			break;
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3m_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();


?>
