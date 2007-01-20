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
 * Class for creating listing and editing newsletters, one-offmailings and campaigns
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_mailings {

	/**
	* php4 constructor
	*
	* @return	string	given name of the object (no purpose right now))
	*/
// 	function tx_t3m_mailings($name)	{
// 		tx_t3m_mailings::__construct($name);
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
	 * Returns campaigns and edit buttons
	 *
	 * @return	string		a table with campaigns and edit buttons
	 */
	function campaigns()	{
		//model ;-)
		$campaigns = tx_t3m_mailings::getCampaigns();
		//view ;-)
		$out = tx_t3m_mailings::tableForCampaigns($campaigns);
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
				<td>'.tx_t3m_mailings::editCampaign($cid['uid']).'</td>
				<td>'.tx_t3m_stats::countEmails($cid['uid']).'</td>
				<td>'.tx_t3m_mailings::createCampaignMailing($cid['uid']).'</td>
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
		$out = $GLOBALS['LANG']->getLL('Newsletters').'<br />'.tx_t3m_mailings::newsletters();
		$out .= '<br />'.$GLOBALS['LANG']->getLL('OneOffMailings').'<br />'.tx_t3m_mailings::oneOffMailings();
		$out .= '<br />'.$GLOBALS['LANG']->getLL('MailsForCampaigns').'<br />'.tx_t3m_mailings::campaignMailings();
		return $out;
	}

	/**
	 * Returns created directmails and edit buttons
	 *
	 * @return	string		a table with created directmails and edit buttons
	 */
	function newsletters()	{
		//model ;-)
		$pids = tx_t3m_mailings::getNewsletters();
		//controller ;-)
// 		foreach ($pids as $pid) {
// 			if (!(tx_t3m_stats::checkMailSent($pid['uid']))) {
// 				$notmailedPids[] = $pid;
// 			}
// 		}
		//view ;-)
		$out = tx_t3m_mailings::tableForNewsletters($pids);
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
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countContents').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Create').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Repeat').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('checkForSpam').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('spamScore').'</td></tr>';
		foreach ($pids as $row)	{
			$out .= '<tr><td>'.$row['title'].'</td>
				<td>'.tx_t3m_mailings::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editNewsletter($row['uid']).'</td>
				<td>';
			$countEmailUsers = intval(tx_t3m_stats::countEmailUsers($row['uid']));
			if ($countEmailUsers != 0) {
				$out .= $countEmailUsers;
			} else {
				$out .= $this->iconImgError;
			}
			$out .= '</td><td>';
			$countContents = intval(tx_t3m_stats::countContents($row['uid']));
			if ($countContents != 0) {
				$out .= $countContents;
			} else {
				$out .= $this->iconImgWarning;
			}
			$out .= '</td>
				<td>'.tx_t3m_mailings::createContent($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editContents($row['uid']).'</td>
				<td>';
			if (intval($row['tx_tcdirectmail_senttime']) != 0) {
				$out .= t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
			} elseif (intval($row['tx_tcdirectmail_senttime']) < time()) {
				$out .= $this->iconImgError;
			} else {
				$out .= $this->iconImgWarning;
			}
			$out .= '</td>
				<td>'.$GLOBALS['LANG']->getLL('pages.tx_tcdirectmail_repeat.I.'.$row['tx_tcdirectmail_repeat']).'</td>
				<td>'.tx_t3m_spam::formSpamCheck($row['uid']).'</td>
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
		$pids = tx_t3m_mailings::getOneOffMailings();
		//controller ;-)
		foreach ($pids as $pid) {
			if (!(tx_t3m_stats::checkMailSent($pid['uid']))) {
				$notmailedPids[] = $pid;
			}
		}
		//view ;-)
		$out = tx_t3m_mailings::tableForOneOffMailings($notmailedPids);
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
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countContents').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Create').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('checkForSpam').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('spamScore').'</td></tr>';
		foreach ($pids as $row)	{
			$out .= '<tr><td>'.$row['title'].'</td>
				<td>'.tx_t3m_mailings::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editOneOffMailing($row['uid']).'</td>
				<td>';
			$countEmailUsers = intval(tx_t3m_stats::countEmailUsers($row['uid']));
			if ($countEmailUsers != 0) {
				$out .= $countEmailUsers;
			} else {
				$out .= $this->iconImgError;
			}
			$out .= '</td><td>';
			$countContents = intval(tx_t3m_stats::countContents($row['uid']));
			if ($countContents != 0) {
				$out .= $countContents;
			} else {
				$out .= $this->iconImgWarning;
			}
			$out .= '</td>
				<td>'.tx_t3m_mailings::createContent($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editContents($row['uid']).'</td>
				<td>';
			if (intval($row['tx_tcdirectmail_senttime']) != 0) {
				$out .= t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
			} else {
				$out .= $this->iconImgError;
			}
			$out .= '</td>
				<td>'.tx_t3m_spam::formSpamCheck($row['uid']).'</td>
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
		$campaigns = tx_t3m_mailings::getCampaigns();
		foreach ($campaigns as $campaign) {
			foreach (tx_t3m_mailings::getCampaignMailings($campaign['uid']) as $cpids) {
				$pids[] = $cpids;
			}
		}
		foreach ($pids as $pid) {
			if (!(tx_t3m_stats::checkMailSent($pid['uid']))) {
				$notmailedPids[] = $pid;
			}
		}
		$out = tx_t3m_mailings::tableForCampaignMailings($notmailedPids);
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
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countContents').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Create').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('editContent').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('checkForSpam').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('spamScore').'</td></tr>';
		foreach ($pids as $row) {
			$out .= '<tr><td>'.tx_t3m_mailings::getCampaignName($row['tx_'.$this->extKey.'_campaign']).'</td>
				<td>'.$row['title'].'</td>
				<td>'.tx_t3m_mailings::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editCampaignMailing($row['uid']).'</td>
				<td>';
			$countEmailUsers = intval(tx_t3m_stats::countEmailUsers($row['uid']));
			if ($countEmailUsers != 0) {
				$out .= $countEmailUsers;
			} else {
				$out .= $this->iconImgError;
			}
			$out .= '</td><td>';
			$countContents = intval(tx_t3m_stats::countContents($row['uid']));
			if ($countContents != 0) {
				$out .= $countContents;
			} else {
				$out .= $this->iconImgWarning;
			}
			$out .= '</td>
				<td>'.tx_t3m_mailings::createContent($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editContents($row['uid']).'</td>
				<td>';
			if ($row['tx_tcdirectmail_senttime'] != 0) {
				$out .= t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
			} else {
				$out .= $this->iconImgError;
			}
			$out .= '</td>
				<td>'.tx_t3m_spam::formSpamCheck($row['uid']).'</td>
				<td>'.tx_t3m_spam::imgSpamCheck($row['tx_t3m_spam_score']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
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
				<td>'.tx_t3m_mailings::createTCDirectmailForReceivergroup($row['uid']).'</td></tr>';
		}
		$out .= '</table>';
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
	 * Returns categories and edit buttons
	 *
	 * @return	string		a table with categories and edit buttons
	 * @todo	Make use of other category systems like tx_commerce_categories,  sys_dmail_category,  tt_news_cat
	 */
	function categories()	{
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Name').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countUsers').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('EditDelete').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,name,calculated_receivers',
			'tx_t3m_categories',
			'deleted=0 AND hidden=0',
			'',
			'calculated_receivers DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr><td>'.$row['name'].'</td>
				<td>'.$row['calculated_receivers'].'</td>
				<td>'.tx_t3m_mailings::editCategory($row['uid']).'</td></tr>';
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
				$out .= '<tr><td>'.$row['header'].'</td><td>'.tx_t3m_mailings::editContent($row['uid']).'</td></tr>';
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
	 * Returns campaign selector
	 *
	 * @return	string		campaign selector box
	 */
	function formCampaignSelector()	{
		$out = '<select onchange="document.location=\'index.php?campaign=\' + options[selectedIndex].value">';  //&SET[function]=group is set anyway
		$campaigns = tx_t3m_mailings::getCampaigns(); //gives: array(uid => name)
// 		ksort ($groups);
		asort($campaigns);
		foreach ($campaigns as $key => $val) {
			if ($_REQUEST['campaign'] == $val['uid']) {
				$out .= '<option selected value="'.$val['uid'].'">'.$val['name'].'</option>';
			} else {
				$out .= '<option value="'.$val['uid'].'">'.$val['name'].'</option>';
			}
		}
                $out .= '</select>';
		return $out;
	}



	/**
	 * Returns a link for a receiver definition
	 *
	 * @param	int		uid of receivergroup for which to create directmail
	 * @return	string		link for creating a tcdirectmail
	 */
	function createTCDirectmailForReceivergroup($uid) {

		$columnsOnly = $this->tcColumnsOnly.',tx_t3m_campaign';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgCreate.'&nbsp;'.$GLOBALS['LANG']->getLL('NewContent').'</a><br/>';
		return $out;
	}



	/**
	 * Creates a new directmail
	 *
	 * @param	int		page oder sysfolder on which to create the directmail
	 * @return	string		form for entering a new campaign
	 */
	function createTcdirectmail($pid)	{
		$out = tx_t3m_mailings::createNewsletter($pid);
		$out .= '<br />'.tx_t3m_mailings::createOneOffMailing($pid);
		$out .= '<br />'.tx_t3m_mailings::createCampaignMailing($pid);
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
		$overrideVals = $this->tcOverrideVals.'&overrideVals[pages][tx_tcdirectmail_repeat]=0';
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
// 		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
// 		return $out;
// 	}

	/**
	 * Edit a newsletter
	 *
	 * @param	int		page to alter
	 * @return	string		form for editing a newsletter
	 */
	function editNewsletter($uid)	{
		$columnsOnly = '&columnsOnly=title,tx_tcdirectmail_repeat,tx_tcdirectmail_senttime,tx_tcdirectmail_real_target,tx_tcdirectmail_plainconvert';
		$params = '&edit[pages]['.intval($uid).']=edit&defVals[pages][doktype]=189'.$columnsOnly.$defVals.$overrideVals;
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'" title="'.$GLOBALS['LANG']->getLL('Edit').'">'.$this->iconImgEdit.'</a>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
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
		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgEdit.'</a><br/>';
		return $out;
	}

	/**
	 * Creates a link for editing the contents of a page
	 *
	 * @param	int		uid of a page
	 * @return	string		link for editing the content elements of a page
	 */
	function editContents($uid)	{
		$out = '<a href="'.$GLOBALS['BACK_PATH'].'db_list.php?id='.intval($uid).'">'.$this->iconImgEdit.'</a><br/>';
		return $out;
	}


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
	 * Returns a table for mails
	 *
	 * @param	array		$pids mailings
	 * @return	string		a table with mails
	 */
	function tableForMails($pids)	{
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('send').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('schedule').'</td></tr>';
			foreach($pids as $pid) {
				$out .= '<tr><td>'.$pid['title'].'</td>
					<td>'.tx_t3m_send::formForSendingMailing($pid['uid']).'</td>
					<td>'.tx_t3m_mailings::editTCDirectmailSendtime($pid['uid']).'</td></tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
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
				<td>'.tx_t3m_mailings::viewPage($row['uid']).'</td>
				<td>'.tx_t3m_mailings::editPage($row['uid']).'</td>
				<td>'.tx_t3m_mailings::getContents($row['uid']).'</td></tr>';
		}
		$out .= '</table>';
		return $out;
	}



	/**
	 * Table for mails with problems
	 *
	 * @param	array		$pids: mail ids
	 * @return	string		table for showing mail problems and editing mails
	 */
	function tableForMaintenanceMails($pids) {
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('maintenanceMails').'</td></tr>';
			foreach($pids as $pid) {
				$out .= '<tr><td>'.$pid['title'].'</td>
					<td><form><input type="submit" name="check_validity" value="'.$GLOBALS['LANG']->getLL('maintenanceMails').'" />
					<input type="hidden" name="id" value="'.$pid['uid'].'" /></form></td></tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}


	/**
	 * Return e-mails with problems encountered by tcdirectmail
	 *
	 * @return	mails with problems encountered by tcdirectmail
	 * @todo	better integration into tcdirectmail (function calls - return values?)
	 */
// 	function getMailsWithWarnings() {
// 		//select * from pages where ...
// 		//foreach page run tcdirectmails tests
//
// 		return $out;
//
// 	}

	/**
	 * Returns a table for a page
	 *
	 * @param	int		$uid: page uid
	 * @return	array		table for a page
	 */
	function page($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'pages',
			'uid = '.intval($uid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = tx_t3m_mailings::tableForPage($row['uid']);
		if (!$out) {
			$out = '<br />'.$this->iconImgError.'&nbsp;'.$GLOBALS['LANG']->getLL('errorNoPage');
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
		$out = '<a href="#" onClick="'.htmlspecialchars(t3lib_BEfunc::viewOnClick(intval($uid), $GLOBALS['BACK_PATH'],t3lib_BEfunc::BEgetRootLine(intval($uid)))).'">'.$this->iconImgView.'</a>';
		return $out;
	}


	/**
	* Creates a link for a new page in given sysfolder
	*
	* @param	int	page/sysfolder in which to create the page
	* @return	string	link for a new page
	*/
// 	function createPage($pid)	{
// 		$params = '&edit[pages]['.$this->myConf['T3M_Sysfolder'].']=new&defVals[pages][header]=New%20Page&columnsOnly=title';
// 		$out = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params,$GLOBALS['BACK_PATH'])).'">'.$this->iconImgCreate.$GLOBALS['LANG']->getLL('NewPage').'</a><br/>';
// 		return $out;
// 	}


}


?>