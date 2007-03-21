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

require_once(t3lib_extMgm::extPath('pbimagegraph').'class.tx_pbimagegraph_ts.php');

/**
 * Some statistical functions
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage tx_t3m
 */
class tx_t3m_stats	{

	/**
	* php4 constructor
	*
	* @return	string	given name of the object (no purpose right now))
	*/
// 	function tx_t3m_stats($name)	{
// 		tx_t3m_stats::__construct($name);
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
	}

	/**
	 * Initialize some variables
	 *
	 * @return	[type]		...
	 */
	function init()	{
	}

	/**
	 * Counts the users of a fe_group
	 *
	 * @param	int		uid of a fe_group
	 * @return	int		usercount of a fe_group
	 */
	function countUsers($gid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,username,usergroup', //COUNT(*) AS icount ONLY WORKS alone - alternative $GLOBALS['TYPO3_DB']->sql_query
			'fe_users',
			'deleted=0' // AND disable=0'
			);
		$out = 0;
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$userArrayAll[$row['uid']] = explode(',',$row['usergroup']);
			if (in_array(intval($gid), $userArrayAll[$row['uid']])) {
				$userArray[] = $row['uid'];
				$out += 1;
			}
		}
		return $out;
	}

	/**
	 * Counts the deregistered or deleted users
	 *
	 * @return	int		usercount of deregistered or deleted users
	 */
	function countDeletedUsers() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) AS icount',
			'fe_users',
			'deleted=1'
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['icount'];
		return $out;
	}

	/**
	 * Returns number of emails of a campaign
	 *
	 * @param	int		campaign uid
	 * @return	int		number of emails of a campaign
	 */
	function countEmails($uid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) AS icount',
			'pages',
			'tx_'.$this->extKey.'_campaign = '.intval($uid).' AND deleted=0 AND hidden=0'
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['icount'];
		return $out;
	}

	/**
	 * Returns number of users selected for an email
	 *
	 * @param	int		page uid
	 * @return	int		number of unique email-adresses for a mail
	 */
	function countEmailUsers($uid)	{
		require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_target.php');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_tcdirectmail_real_target',
			'pages',
			'uid='.intval($uid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['tx_tcdirectmail_real_target']) {
			$targets = explode(',',$row['tx_tcdirectmail_real_target']);
			foreach ($targets as $tuid) {
				$target = tx_tcdirectmail_target::loadTarget($tuid);
				while ($receiver = $target->getRecord()) {
					$users[] = $receiver['email'];
				}
			}
			$uniqueUsers = array_unique($users);
			$out = count($uniqueUsers);
		} else {
			$out = 0;
		}
		return $out;
	}


	/**
	 * Returns number contents of an email
	 *
	 * @param	int		page uid
	 * @return	int		number contents of an email
	 */
	function countContents($pid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) AS icount',
			'tt_content',
			'pid = '.intval($pid).' AND deleted=0 AND hidden=0'
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['icount'];
		return $out;
	}

	/**
	 * Returns boolean value indicating if mail was sent or not
	 *
	 * @param	pid		of the mail
	 * @return	boolean		value indicating if mail was sent or not
	 */
	function checkMailSent($pid)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) AS icount',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['icount'] > 0) {
			$out = true;
		} else {
			$out = false;
		}
		return $out;
	}

	/**
	 * Returns boolean value indicating if mail has been opened at least once or not
	 *
	 * @param	[type]		$pid: ...
	 * @return	boolean		value indicating if mail has been opened at least once or not
	 */
	function checkMailOpened($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'SUM(beenthere) AS ibeenthere',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['ibeenthere'] > 0) {
			$out = true;
		} else {
			$out = false;
		}
		return $out;
	}

	/**
	 * Returns boolean value indicating if all campaign mail were sent or not
	 *
	 * @param	uid		of the campaign
	 * @return	string		value indicating if the campaign is finished or not
	 */
	function checkCampaignFinished($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'pages',
			'tx_t3m_campaign = '.intval($uid).' AND deleted=0'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$sent = tx_t3m_stats::checkMailSent(intval($row['uid']));
			//$out .= 'sent:'.$sent.' ';
			if ($sent) {
				$out = $GLOBALS['LANG']->getLL('campaignfinished'); // ok this one is sent, now look for next one.
			} else {
				$out = $GLOBALS['LANG']->getLL('campaignunfinished');
				return $out; // bail out here because one mail was not sent
			}
		}
		return $out;
	}

	/**
	* Returns how many users have "ordered" that category
	*
	* @param	uid of the category
	* @return	int	how many users have "ordered" that category
	*/
	function updateCalculatedCategory($uid) {
		$out = count(tx_t3m_addresses::getCategoryUsers(intval($uid)));
 		return $out;
 	}

	/**
	 * Returns int value how often links in that mail were clicked
	 *
	 * @param	pid		of the mail
	 * @return	int		value how often links in that mail were clicked
	 */
	function mailClicks($pid) {
		$out = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'COUNT(*) as icount',
				'tx_tcdirectmail_clicklinks',
				'opened > 0 AND sentlog='.$row['uid']
				);
			$row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2);
			$out += intval($row2['icount']);
// 			if ($row2['icount'] > 0) {
// 				$out += $row2['icount'];
// 			} else {
// 				$out = $row2['icount'];
// 			}
		}
		return $out;
	}

	/**
	 * Returns string value which links in that mail were clicked
	 *
	 * @param	pid		of the mail
	 * @return	string		value which links in that mail were clicked
	 */
	function mailClickedLinks($pid) {
		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">URL</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('clicks').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'url,opened',
				'tx_tcdirectmail_clicklinks',
				'opened > 0 AND sentlog='.$row['uid']
				);
			if (($GLOBALS['TYPO3_DB']->sql_num_rows($res2)) > 0) {
// 				$out .= '<tr><td>';
				while ($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
					$out .= '<tr><td>';
					$out .= '<a href="'.$row2['url'].'">'.$row2['url'].'</a>';
					$out .= '</td><td>';
					$out .= $row2['opened'];
					$out .= '</td></tr>';
				}
// 				$out .= '</td><td>';
// 				while ($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
// 					$out .= $row2['opened'].'</br>';
// 				}
// 				$out .= '</td></tr>';
			}
		}
		$out .= '</table>';
		return $out;
	}


	/**
	 * Returns stats for one tcdirectmail
	 *
	 * @return	string		stats for one tcdirectmail
	 */
	function statsForTCDirectmail() {
		// get details from tcstats ext
	}

	/**
	 * Returns stats for all tcdirectmail
	 *
	 * @param	[type]		$uids: ...
	 * @return	string		stats for all tcdirectmail
	 */
	function statsForTCDirectmails($uids)	{
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'pid, COUNT(receiver) as ireceiver', //, MAX(sendtime) as maxsendtime
			'tx_tcdirectmail_sentlog',
			'',
			'pid'//			'sendtime DESC'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$receivers[$row['pid']] = $row['ireceiver'];
		}
		arsort($receivers);

		$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Receivers').'</td></tr>';
		foreach($receivers as $key => $val) {
			$out .= '<tr><td valign="top">'.tx_t3m_mailings::getPageName($key).'</td><td>'.$val.'</td></tr>';
		}
		$out .= '</table>';

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
	 * Returns stats for campaigns
	 *
	 * @return	string		a table with stats for campaigns
	 */
	function statsForCampaigns()	{
		if (!$_REQUEST['campaign']) {
			$_REQUEST['campaign'] = tx_t3m_mailings::getFirstCampaign();
		}
		$out = tx_t3m_stats::formCampaignSelector();
		$out .= '<br /><h3>'.tx_t3m_mailings::getCampaignName($_REQUEST['campaign']).'</h3>';
		$out .= tx_t3m_mailings::getCampaignDescription($_REQUEST['campaign']);

		$mails = tx_t3m_mailings::getCampaignMailings($_REQUEST['campaign']);
		foreach ($mails as $mail) {
			$pids[] = $mail['uid'];
		}

		// sent stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('sentstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForSentMails($pids);

		// opened stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('openstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForOpenedMails($pids);
		$out .= tx_t3m_stats::tableForNotOpenedMails($pids);

		// click stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('clickstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForClickedMails($pids);
		$out .= tx_t3m_stats::tableForNotClickedMails($pids);

		return $out;
	}


	/**
	 * Returns stats for newsletter
	 *
	 * @return	string		a table with stats for newsletter
	 */
	function statsForNewsletters() {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('SentMails').'</h3>';
		$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('PageSends').'</td></tr>';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,title,tx_tcdirectmail_repeat,tx_tcdirectmail_senttime',
			'pages',
			'pid='.$this->myConf['tcdirectmail_Sysfolder'].' AND deleted=0 AND hidden=0 AND tx_'.$this->extKey.'_campaign=0 AND NOT tx_tcdirectmail_repeat=0',
			'',
			'tstamp'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$out .= '<tr>';
			$out .= '<td valign="top">'.$row['title'].'</td>';
 			$out .= '<td>';
			if (tx_t3m_stats::checkMailSent($row['uid'])) {
				// $out .= tx_tcdmailstats_modfunc1::viewStatistics();
				// basically replacing this function because no static function call possible..
				/* Get numbers for each session */
				$sql = 'SELECT lck.begintime, lck.stoptime, COUNT(lg.receiver)
					FROM tx_tcdirectmail_lock lck
					LEFT JOIN tx_tcdirectmail_sentlog lg ON lck.begintime = lg.begintime
					WHERE lck.pid = '.$row['uid'].'
					AND lg.pid = '.$row['uid'].'
					GROUP BY 1,2';
				$rs = $GLOBALS['TYPO3_DB']->sql_query($sql);
				/* Display */
				$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
					<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
					<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countEmails').'</td></tr>';
				while (list($begintime, $stoptime, $num_receivers) = $GLOBALS['TYPO3_DB']->sql_fetch_row($rs)) {
					$out .= '<tr>
						<td>'.t3lib_BEfunc::datetime($begintime).'</td>
						<td>'.$num_receivers.'</td></tr>';
				}
				$out .= '</table>';
			} else {
				$out .= tx_t3m_send::formForSendingMailing($row['uid']);
			}
			$out .= '</td></tr>';
			$pids[] = $row['uid'];
		}
		$out .= '</table>';


		// opened stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('openstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForOpenedMails($pids);
		$out .= tx_t3m_stats::tableForNotOpenedMails($pids);

		// click stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('clickstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForClickedMails($pids);
		$out .= tx_t3m_stats::tableForNotClickedMails($pids);


		return $out;
	}



	/**
	* Returns stats for a tcdirectmail
	*
	* @return	string stats for a tcdirectmail
	*/
// 	function statsForTCDirectmail($uid)	{
// 		if (tx_t3m_stats::checkMailSent($uid)) {
// 			// $out .= tx_tcdmailstats_modfunc1::viewStatistics(); // basically replace this function..
// 			/* Get numbers for each session */
// 			$sql = "SELECT lck.begintime, lck.stoptime, COUNT(lg.receiver)
// 				FROM tx_tcdirectmail_lock lck
// 				LEFT JOIN tx_tcdirectmail_sentlog lg ON lck.begintime = lg.begintime
// 				WHERE lck.pid = $uid
// 				AND lg.pid = $uid
// 				GROUP BY 1,2";
// 			$rs = $GLOBALS['TYPO3_DB']->sql_query($sql);
// 			/* Display */
// 			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
//
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
// 				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countEmails').'</td></tr>';
// 			while (list($begintime, $stoptime, $num_receivers) = $GLOBALS['TYPO3_DB']->sql_fetch_row($rs)) {
// 				$out .= '<tr>
// 					<td>'.t3lib_BEfunc::datetime($begintime).'</td>
// 					<td>'.$num_receivers.'</td></tr>';
// 			}
// 			$out .= '</table>';
// 		} else {
// 			$out .= 'mail not sent, send now..';
// 		}
// 		return $out;
// 	}


	/**
	 * Returns stats for single mailings
	 *
	 * @return	string		a table with stats for single mailings
	 */
	function statsForOneOffMailings() {
		// get one off mailings
		$pids = '';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid',
			'pages',
			'pid='.$this->myConf['tcdirectmail_Sysfolder'].' AND deleted=0 AND hidden=0 AND tx_'.$this->extKey.'_campaign=0 AND tx_tcdirectmail_repeat=0',
			'',
			'tx_tcdirectmail_senttime'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$pids[] = $row['uid'];
		}

// 		t3lib_div::debug($pids);
		// sent stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('sentstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForSentMails($pids);

		// opened stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('openstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForOpenedMails($pids);
		$out .= tx_t3m_stats::tableForNotOpenedMails($pids);

		// click stats
		//$out .= '<h3>'.$GLOBALS['LANG']->getLL('clickstatistics').'</h3>';
		$out .= tx_t3m_stats::tableForClickedMails($pids);
		$out .= tx_t3m_stats::tableForNotClickedMails($pids);

		return $out;
	}


	/**
	 * Returns user occurences of that year
	 *
	 * @param	[type]		$year: ...
	 * @return	string		an image for user occurences of that year
	 */
	function yearlyStatsForUsers($year) {

		// STATIC DATA:
		// PLOTAREA
		$filename = t3lib_extMgm::extPath('t3m').'res/pbimagegraph/plot_step.txt';
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		$ts = t3lib_div::makeInstance("t3lib_tsparser");
		$ts->parse($contents); // now we should have it in $ts->setup
		$static_plot_step = $ts->setup['lib.']['pbimagegraph.'];
		fclose($handle);

		// DYNAMIC DATA:
		// what year? this year if not set otherwise
		if (!isset($year)) {
			$year = date('Y');
		}
		$month = date('n'); //1-12
		$year_month = date('Y_n');

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'fe_users'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{ // get all data
			$usergroups = explode(',',$row['usergroup']);

			//tstamp
			$tmp_year_month_tstamp = date('Y_n',$row['tstamp']);
			$count_activity[$tmp_year_month_tstamp] += 1;
			$tmp_year_tstamp = date('Y',$row['tstamp']);
			$count_activity[$tmp_year_tstamp] += 1;
			$count[$tmp_year_tstamp] += 1;

			//get users created that year (registrations)
			$tmp_year_month_crdate = date('Y_n',$row['crdate']);
			$count_created[$tmp_year_month_crdate] += 1;
			$tmp_year_crdate = date('Y',$row['crdate']);
			$count_created[$tmp_year_crdate] += 1;
			$count[$tmp_year_crdate] += 1;


			if ((in_array($this->myConf['groupRegistered'],$usergroups)) && (!($row['disable'])) && (!($row['deleted']))) {
				$count_registered[$tmp_year_month_tstamp] += 1;
				$count_registered[$tmp_year_tstamp] += 1;

			}

			//get users disabled that year (could be pending or bounce-related)
			if ($row['disable']) {
				$count_disabled[$tmp_year_month_tstamp] += 1;
				$count_disabled[$tmp_year_tstamp] += 1;
				if (in_array($this->myConf['groupPending'],$usergroups)) {
					$count_pending[$tmp_year_month_tstamp] += 1;
					$count_pending[$tmp_year_tstamp] += 1;
				}
				if (in_array($this->myConf['groupSoftbounces'],$usergroups)) {
					$count_softbounces[$tmp_year_month_tstamp] += 1;
					$count_softbounces[$tmp_year_tstamp] += 1;
				}
				if (in_array($this->myConf['groupHardbounces'],$usergroups)) {
					$count_hardbounces[$tmp_year_month_tstamp] += 1;
					$count_hardbounces[$tmp_year_tstamp] += 1;
				}

			}

			//get users deleted that year (deregistered)
			if ($row['deleted']) { //deregistered themselves or "deregistered" by backend user
				$count_deleted[$tmp_year_month_tstamp] += 1;
				$count_deleted[$tmp_year_tstamp] += 1;
			}

			if ($this->demo)  { // i know its a bad place here... :-(
				$count['2005'] = 10;
				$count_registered['2005'] = 1000;

				$count_registered['2005_1'] = 10;
				$count_registered['2005_5'] = 20;
				$count_registered['2005_6'] = 400;
				$count_registered['2005_8'] = 100;

				$count_pending['2005_1'] = 10;
				$count_pending['2005_2'] = 10;
				$count_pending['2005_5'] = 40;
				$count_pending['2005_6'] = 100;
				$count_pending['2005_7'] = 50;

				$count_softbounces['2005_1'] = 100;
				$count_softbounces['2005_2'] = 10;
				$count_softbounces['2005_3'] = 10;
				$count_softbounces['2005_6'] = 20;
				$count_softbounces['2005_7'] = 10;
				$count_softbounces['2005_8'] = 60;

				$count_hardbounces['2005_4'] = 10;
// 				$count_hardbounces['2005_6'] = 10;
				$count_hardbounces['2005_7'] = 30;
				$count_hardbounces['2005_8'] = 50;

			}
		}


		$months=array (1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Aug", 9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec"); //@todo: use some $GLOBALS['LANG'] or t3lib?


		$plot_step_users_year = $static_plot_step;

		$plot_step_users_year['10.']['10.']['text'] = $GLOBALS['LANG']->getLL('userBehavior').' '.$year;
		$plot_step_users_year['10.']['20.']['10.']['axis.']['y.']['title'] = $GLOBALS['LANG']->getLL('User');

		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['10'] = 'trivial';
		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['10.']['name'] = $GLOBALS['LANG']->getLL('registered');
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['1'] = 'addColor';
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['1.']['color'] = 'green';

		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['20'] = 'trivial';
		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['20.']['name'] = $GLOBALS['LANG']->getLL('pending');
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['2'] = 'addColor';
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['2.']['color'] = 'yellow';

		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['30'] = 'trivial';
		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['30.']['name'] = $GLOBALS['LANG']->getLL('softbounces');
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['3'] = 'addColor';
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['3.']['color'] = 'orange';

		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['40'] = 'trivial';
		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['40.']['name'] = $GLOBALS['LANG']->getLL('hardbounces');
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['4'] = 'addColor';
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['4.']['color'] = 'red';

		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['50'] = 'trivial';
		$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['50.']['name'] = $GLOBALS['LANG']->getLL('deleted');
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['5'] = 'addColor';
		$plot_step_users_year['10.']['20.']['10.']['10.']['fillStyle.']['5.']['color'] = 'black';

		for ($i = 1; $i < 13; $i++) { // go through this year's data

			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['10.'][$i.'0'] = 'point';
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['10.'][$i.'0.']['x'] = $months[$i];
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['10.'][$i.'0.']['y'] = $count_registered[$year.'_'.$i];

			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['20.'][$i.'0'] = 'point';
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['20.'][$i.'0.']['x'] = $months[$i];
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['20.'][$i.'0.']['y'] = $count_pending[$year.'_'.$i];

			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['30.'][$i.'0'] = 'point';
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['30.'][$i.'0.']['x'] = $months[$i];
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['30.'][$i.'0.']['y'] = $count_softbounces[$year.'_'.$i];

			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['40.'][$i.'0'] = 'point';
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['40.'][$i.'0.']['x'] = $months[$i];
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['40.'][$i.'0.']['y'] = $count_hardbounces[$year.'_'.$i];

			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['50.'][$i.'0'] = 'point';
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['50.'][$i.'0.']['x'] = $months[$i];
			$plot_step_users_year['10.']['20.']['10.']['10.']['dataset.']['50.'][$i.'0.']['y'] = $count_deleted[$year.'_'.$i];

		}

		if ($count[$year] > 1) { //something happened in that year
			$out .= ' <br />'.tx_pbimagegraph_ts::make($plot_step_users_year);
//   			t3lib_div::debug($plot_step_users_year);

		} else {
// 			$out = 'no data for the year'.$year;
		}

		return $out;

	}

	/**
	 * Returns an image for all mails of that year
	 *
	 * @param	[type]		$year: ...
	 * @return	string		an image for all mails of that year
	 */
	function yearlyStatsForMails($year) {

		// STATIC DATA:
		// PLOTAREA
		$filename = t3lib_extMgm::extPath('t3m').'res/pbimagegraph/plotarea.txt';
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		$ts = t3lib_div::makeInstance("t3lib_tsparser");
		$ts->parse($contents); // now we should have it in $ts->setup
		$static_plotarea = $ts->setup['lib.']['pbimagegraph.'];
		fclose($handle);


		// DYNAMIC DATA:
		// what year? this year.
		if (!isset($year)) {
			$year = date('Y');
		}
		$month = date('n'); //1-12
		$year_month = date('Y_n');

		// get mails of the given year:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_tcdirectmail_sentlog'
			);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{ // get all data
			$tmp_year_month = date('Y_n',$row['sendtime']);
			$count[$tmp_year_month] += 1; // every sending results in the counter going up
			$tmp_year = date('Y',$row['sendtime']);
			$count[$tmp_year] += 1;
		}


		if ($this->demo) {
			$count['2005_3'] = 104;
			$count['2005'] = 104;
			$count['2006_6'] = 66;
		}

		$months=array (1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Aug", 9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec"); //@todo: some $GLOBALS['LANG'] or some t3lib?

// 		t3lib_div::debug($count);
// 		t3lib_div::debug($static_plotarea);

		$plotarea_mails_year = $static_plotarea;
		// now what we want is all data from that year
		$plotarea_mails_year['10.']['10.']['text'] = 'E-Mails sent in '.$year;
		$plotarea_mails_year['10.']['20.']['10.']['axis.']['y.']['title'] = 'E-Mails';
		$plotarea_mails_year['10.']['20.']['10.']['10.']['title'] = $GLOBALS['LANG']->getLL('SentMails');
		for ($i = 1; $i < 13; $i++) { // go through this year's data
			$plotarea_mails_year['10.']['20.']['10.']['10.']['dataset.']['10.'][$i.'0'] = 'point';
			$plotarea_mails_year['10.']['20.']['10.']['10.']['dataset.']['10.'][$i.'0.']['x'] = $months[$i];
			$plotarea_mails_year['10.']['20.']['10.']['10.']['dataset.']['10.'][$i.'0.']['y'] = $count[$year.'_'.$i];
		}
// 		t3lib_div::debug($plotarea_mails_year);
		if ($count[$year] > 0) {
			$out = ' <br />'.tx_pbimagegraph_ts::make($plotarea_mails_year);
		} else {
// 			$out = 'no data for the year'.$year;
		}

		return $out;
	}


	/**
	 * Returns stats for users
	 *
	 * @return	string		a table with stats for users
	 */
	function statsForUsers()	{

		// STATIC DATA:
		// PIE
		$filename = t3lib_extMgm::extPath('t3m').'res/pbimagegraph/pie.txt';
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		$ts = t3lib_div::makeInstance("t3lib_tsparser");
		$ts->parse($contents); // now we should have it in $ts->setup
		$static_pie = $ts->setup['lib.']['pbimagegraph.'];
		fclose($handle);


		// SUBSCRIPTIONS
		$out = '<h3>'.$GLOBALS['LANG']->getLL('subscriptions').'</h3>';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) AS icount',
			'fe_users'
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$usercountAll = $row['icount'];
		$usercountPending = tx_t3m_stats::countUsers($this->rootTS['plugin.tx_srfeuserregister_pi1.userGroupUponRegistration']['value']);
		$usercountRegistered = tx_t3m_stats::countUsers($this->rootTS['plugin.tx_srfeuserregister_pi1.userGroupAfterConfirmation']['value']);
		$usercountDeleted = tx_t3m_stats::countDeletedUsers();
		// fe_users.crdate    fe_users.tstamp
		$usercountOthers = $usercountAll - $usercountPending - $usercountRegistered;
		$confirmedratio = round((($usercountRegistered/$usercountPending) * 100),2);

		$out .= '<table class="typo3-dblist">';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('TotalUsers').'</td><td>'.$usercountAll.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('pending').'</td><td>'.$usercountPending.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('registered').'</td><td>'.$usercountRegistered.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('deleted').'</td><td>'.$usercountDeleted.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('confirmationRatio').'</td><td>'.$confirmedratio.'%</td></tr>';
		$out .= '</table>';

		// DYNAMIC DATA:
		//pending / registered users

		$pie_registrations = $static_pie;
		$pie_registrations['10.']['10.']['text'] = $GLOBALS['LANG']->getLL('confirmationPie');

		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['10'] = 'point';
		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['x'] = $GLOBALS['LANG']->getLL('registered').': '.$usercountRegistered;
		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['y'] = $usercountRegistered;
		$pie_registrations['10.']['20.']['10.']['10.']['fillStyle.']['1'] = 'addColor';
		$pie_registrations['10.']['20.']['10.']['10.']['fillStyle.']['1.']['color'] = 'green';

		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['20'] = 'point';
		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['x'] = $GLOBALS['LANG']->getLL('pending').': '.$usercountPending;
		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['y'] = $usercountPending;
		$pie_registrations['10.']['20.']['10.']['10.']['fillStyle.']['2'] = 'addColor';
		$pie_registrations['10.']['20.']['10.']['10.']['fillStyle.']['2.']['color'] = 'orange';

		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['30'] = 'point';
		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['30.']['x'] = $GLOBALS['LANG']->getLL('deleted').': '.$usercountDeleted;
		$pie_registrations['10.']['20.']['10.']['10.']['dataset.']['10.']['30.']['y'] = $usercountDeleted;
		$pie_registrations['10.']['20.']['10.']['10.']['fillStyle.']['3'] = 'addColor';
		$pie_registrations['10.']['20.']['10.']['10.']['fillStyle.']['3.']['color'] = 'red';


// 		t3lib_div::debug($static_pie);
// 		t3lib_div::debug($pie_registrations);
// 		include($GLOBALS['BACK_PATH'].'../index.php?id=97');

		$out .= '<br />'.tx_pbimagegraph_ts::make($pie_registrations);

		// BOUNCES - softbounce / hardbounces
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('bounces').'</h3>';

		$out .= '<br />'.$GLOBALS['LANG']->getLL('descriptionsoftbounce').'<br />
			'.$GLOBALS['LANG']->getLL('descriptionhardbounce').'<br />';

		$usercountSoftbounces = tx_t3m_stats::countUsers($this->myConf['groupSoftbounces']);
		$usercountHardbounces = tx_t3m_stats::countUsers($this->myConf['groupHardbounces']);
		$bounceratio = round((($usercountSoftbounces/$usercountHardbounces) * 100),2);

		$out .= '<table class="typo3-dblist">';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('TotalUsers').'</td><td>'.$usercountAll.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('softbounces').'</td><td>'.$usercountSoftbounces.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('hardbounces').'</td><td>'.$usercountHardbounces.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('bounceRatio').'</td><td>'.$bounceratio.'%</td></tr>';
		$out .= '</table>';

		$pie_bounces = $static_pie;
		$pie_bounces['10.']['10.']['text'] = $GLOBALS['LANG']->getLL('bouncePie');

		$pie_bounces['10.']['20.']['10.']['10.']['dataset.']['10.']['10'] = 'point';
		$pie_bounces['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['x'] = $GLOBALS['LANG']->getLL('softbounces').': '.$usercountSoftbounces;
		$pie_bounces['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['y'] = $usercountSoftbounces;
		$pie_bounces['10.']['20.']['10.']['10.']['fillStyle.']['1'] = 'addColor';
		$pie_bounces['10.']['20.']['10.']['10.']['fillStyle.']['1.']['color'] = 'orange';

		$pie_bounces['10.']['20.']['10.']['10.']['dataset.']['10.']['20'] = 'point';
		$pie_bounces['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['x'] = $GLOBALS['LANG']->getLL('hardbounces').': '.$usercountHardbounces;
		$pie_bounces['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['y'] = $usercountHardbounces;
		$pie_bounces['10.']['20.']['10.']['10.']['fillStyle.']['2'] = 'addColor';
		$pie_bounces['10.']['20.']['10.']['10.']['fillStyle.']['2.']['color'] = 'red';

		$out .= '<br />'.tx_pbimagegraph_ts::make($pie_bounces);

		return $out;
	}

	/**
	 * Returns last sent time
	 *
	 * @param	[type]		$pid: ...
	 * @return	string		time of last sent time for a page
	 */
	function getLastSentTime($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'MAX(sendtime) as isendtime',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = '';
		if ($row['isendtime'] != 0) {
			$out = t3lib_BEfunc::datetime($row['isendtime']);
		}
		return $out;
	}

	/**
	 * Returns last sent time
	 *
	 * @param	[type]		$pid: ...
	 * @return	string		time of last sent time for a page
	 */
	function getOpenedSentTime($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'MAX(sendtime) as isendtime',
			'tx_tcdirectmail_sentlog',
			'beenthere > 0 AND pid = '.intval($pid)
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = '';
		if ($row['isendtime'] != 0) {
			$out = t3lib_BEfunc::datetime($row['isendtime']);
		}
		return $out;
	}

	/**
	 * Returns last sent time
	 *
	 * @param	[type]		$pid: ...
	 * @return	string		time of last sent time for a page
	 */
	function getNotOpenedSentTime($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'MIN(sendtime) as isendtime',
			'tx_tcdirectmail_sentlog',
			'beenthere = 0 AND pid = '.intval($pid)
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = '';
		if ($row['isendtime'] != 0) {
			$out = t3lib_BEfunc::datetime($row['isendtime']);
		}
		return $out;
	}


	/**
	 * Returns stats for opened mails
	 *
	 * @return	string		a table with stats for opened mails
	 */
	function statsForOpenedMails()	{

		$sentMailsCount = tx_t3m_stats::getSentMailsSingle();
		$openedMailsCount = tx_t3m_stats::getOpenedMailsSingle();
		$notOpenedMailsCount = ($sentMailsCount - $openedMailsCount);


		$out .= '<h3>'.$GLOBALS['LANG']->getLL('Overview').'</h3>';
		$out .= '<table class="typo3-dblist">';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('TotalPageSends').'</td><td>'.$sentMailsCount.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('OpenedMails').'</td><td>'.$openedMailsCount.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('NotOpenedMails').'</td><td>'.$notOpenedMailsCount.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('PageViewRatio').'</td><td>'.round((($openedMailsCount / $notOpenedMailsCount) * 100),2).'%</td></tr>';
		$out .= '</table>';


		// STATIC DATA:
		// PIE
		$filename = t3lib_extMgm::extPath('t3m').'res/pbimagegraph/pie.txt';
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		$ts = t3lib_div::makeInstance("t3lib_tsparser");
		$ts->parse($contents); // now we should have it in $ts->setup
		$static_pie = $ts->setup['lib.']['pbimagegraph.'];
		fclose($handle);

		// DYNAMIC DATA:
		// opened /not opened

		$pie_opened = $static_pie;
		$pie_opened['10.']['10.']['text'] = $GLOBALS['LANG']->getLL('pageViewRatio');

		$pie_opened['10.']['20.']['10.']['10.']['dataset.']['10.']['10'] = 'point';
		$pie_opened['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['x'] = $GLOBALS['LANG']->getLL('OpenedMails').': '.$openedMailsCount;
		$pie_opened['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['y'] = $openedMailsCount;
		$pie_opened['10.']['20.']['10.']['10.']['fillStyle.']['1'] = 'addColor';
		$pie_opened['10.']['20.']['10.']['10.']['fillStyle.']['1.']['color'] = 'green';


		$pie_opened['10.']['20.']['10.']['10.']['dataset.']['10.']['20'] = 'point';
		$pie_opened['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['x'] = $GLOBALS['LANG']->getLL('NotOpenedMails').': '.$notOpenedMailsCount;
		$pie_opened['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['y'] = $notOpenedMailsCount;
		$pie_opened['10.']['20.']['10.']['10.']['fillStyle.']['2'] = 'addColor';
		$pie_opened['10.']['20.']['10.']['10.']['fillStyle.']['2.']['color'] = 'red';


		$out .=' <br />'.tx_pbimagegraph_ts::make($pie_opened);

		$out.= tx_t3m_stats::tableForOpenedMails();
		$out.= tx_t3m_stats::tableForNotOpenedMails();

		return $out;

	}

	/**
	 * Returns count for sent mails
	 *
	 * @return	int		count for sent mails
	 */
	function getSentMailsSingle() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) as icount',
			'tx_tcdirectmail_sentlog'
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['icount'];
		return $out;
	}

	/**
	 * Returns array of all sent mails
	 *
	 * @return	array		of all sent mails
	 */
	function getSentMails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'pid',
			'tx_tcdirectmail_sentlog',
			'',
			'pid'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$sentMails[] = $row['pid'];
		}
		$out = array_unique($sentMails);
		return $out;
	}

	/**
	 * Returns stats for sent mails
	 *
	 * @param	[type]		$pids: ...
	 * @return	string		a table with stats for sent mails
	 */
	function tableForSentMails($pids) {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('SentMails').'</h3>';
// 		if ($pids) {
// 			$pids = array_intersect(tx_t3m_stats::getSentMails(),$pids);
// 		} else {
// 			$pids = tx_t3m_stats::getSentMails();
// 		}
		$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Sent').'?</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDatePlanned').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SentDate').'</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('countEmails').'</td></tr>';
		foreach ($pids as $pid) {
			$out .= '<tr>';
			$out .= '<td>'.tx_t3m_mailings::getPageName($pid).'</td><td>';
			if (tx_t3m_stats::checkMailSent($pid)) {
				$out .= '<img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_ok2.gif" />';
			} else {
				$out .= '<img src="'.$GLOBALS['BACK_PATH'].'gfx/icon_fatalerror.gif" />';
			}
			$out .= '</td><td>'.tx_t3m_stats::getPlannedSentTime($pid).'</td>';
			$out .= '<td>'.tx_t3m_stats::getLastSentTime($pid).'</td>';
			$out .= '<td>'.tx_t3m_stats::getSendCountForPage($pid).'</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
		return $out;
	}

	/**
	 * Returns stats for opened mails
	 *
	 * @param	[type]		$pid: ...
	 * @return	string		a table with stats for opened mails
	 */
	function getPlannedSentTime($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_tcdirectmail_senttime',
			'pages',
			'uid='.intval($pid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if ($row['tx_tcdirectmail_senttime'] != 0) {
			$out = t3lib_BEfunc::datetime($row['tx_tcdirectmail_senttime']);
		}
		return $out;
	}

	/**
	 * Returns count for single opened mails
	 *
	 * @return	int		count for single opened mails
	 */
	function getOpenedMailsSingle() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) as icount', //,uid,beenthere
			'tx_tcdirectmail_sentlog',
			'beenthere > 0'
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['icount'];
		return $out;
	}

	/**
	 * Returns array of opened mails
	 *
	 * @return	array		of opened mails
	 */
	function getOpenedMails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'pid,SUM(beenthere) as ibeenthere', //,uid,beenthere
			'tx_tcdirectmail_sentlog',
			'beenthere > 0',
			'pid',
			'ibeenthere DESC'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$openedMails[] = $row['pid'];
		}
		$out = array_unique($openedMails);
		return $out;
	}

	/**
	 * Returns array of not opened mails
	 *
	 * @return	array		of not opened mails
	 */
	function getNotOpenedMails() {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'pid',
			'tx_tcdirectmail_sentlog',
			'beenthere = 0',
			'pid'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			if (!(tx_t3m_stats::checkMailOpened($row['pid']))) {
				$notOpenedMails[] = $row['pid'];
			}
		}
		$out = array_unique($notOpenedMails);
		return $out;
	}

	/**
	 * Returns how many users that page was opened
	 *
	 * @param	int		pid of page
	 * @return	int		send count how often the page was opened (how many recipients got the page)
	 */
	function getOpenedCountForPage($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'SUM(beenthere) as isum',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['isum'];
		return $out;
	}


	/**
	 * Returns how many users that page was clicked in
	 *
	 * @param	int		pid of page
	 * @return	int		send count how often the page was clicked in
	 */
	function getClickedCountForPage($pid) {
		$clickCount = 0;
		// get the sentlogs where clicks happened
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'SUM(opened) as iopened',
			'tx_tcdirectmail_clicklinks',
			'opened > 0', // opened: 'pageclick''iopened DESC'
			'sentlog'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// if the pid is right for that sentlog add the clicks.
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'pid',
				'tx_tcdirectmail_sentlog',
				'uid = '.$row['sentlog'].' AND pid = '.intval($pid)
			);
			while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2))	{ // if the pid was right we have a result and can add the count
				$clickCount += $row['iopened'];
			}
		}
		$out = $clickCount;
		return $out;
	}


	/**
	 * Returns stats for opened mails
	 *
	 * @param	[type]		$pids: ...
	 * @return	string		a table with stats for opened mails
	 */
	function tableForOpenedMails($pids) {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('OpenedMails').'</h3>';
		if ($pids) {
			$pids = array_intersect(tx_t3m_stats::getOpenedMails(),$pids);
		} else {
			$pids = tx_t3m_stats::getOpenedMails();
		}
		if (!$pids) {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		} else {
			// else get all pids
			$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('PageView').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('PageViewRatio').'</td>
				</tr>';
			foreach ($pids as $pid) {
				$out .= '<tr><td>'.tx_t3m_mailings::getPageName($pid).'</td>
					<td>'.tx_t3m_mailings::viewPage($pid).'</td>
					<td>'.tx_t3m_stats::getOpenedSentTime($pid).'</td>
					<td>'.tx_t3m_stats::getOpenedCountForPage($pid).'</td>
					<td>'.round(((tx_t3m_stats::getOpenedCountForPage($pid) / tx_t3m_stats::getSendCountForPage($pid)) * 100),2).'%</td>
					</tr>';
			}
			$out .= '</table>';
		}
		return $out;
	}


	/**
	 * Returns stats for not opened mails
	 *
	 * @param	[type]		$pids: ...
	 * @return	string		a table with stats for not opened mails
	 */
	function tableForNotOpenedMails($pids) {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('NotOpenedMails').'</h3>';
		if ($pids) {
			$pids = array_intersect(tx_t3m_stats::getNotOpenedMails(),$pids);
		} else {
			$pids = tx_t3m_stats::getNotOpenedMails();
		}
		if (!$pids) {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		} else {
			// else get all pids
			$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
				</tr>';
			foreach ($pids as $pid) {
				$out .= '<tr><td>'.tx_t3m_mailings::getPageName($pid).'</td>
					<td>'.tx_t3m_mailings::viewPage($pid).'</td>
					<td>'.tx_t3m_stats::getNotOpenedSentTime($pid).'</td>
					</tr>';
			}
			$out .= '</table>';
		}
		return $out;
	}

	/**
	 * Returns count of how many deliveries resulted in at least one click
	 *
	 * @return	int		count of how many deliveries resulted in at least one click
	 */
	function getClickedMailsSingle() {
		// get the sentlogs where clicks happened
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) as icount',
			'tx_tcdirectmail_clicklinks',
			'opened > 0',
			'sentlog'
		);
// 		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
// 		$out = $row['icount'];
		$out = $GLOBALS['TYPO3_DB']->sql_affected_rows();
		return $out;
	}

	/**
	 * Returns array of clicked mails
	 *
	 * @return	array		with clicked mails
	 */
	function getClickedMails() {
		// get the sentlogs where clicks happened
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'sentlog,linkid,linktype,url,opened',
			'tx_tcdirectmail_clicklinks',
			'opened > 0', // opened: 'pageclick''iopened DESC'
			'sentlog'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			// get the pids where clicks happened
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'pid',
				'tx_tcdirectmail_sentlog',
				'uid = '.$row['sentlog'],
				'pid'
			);
			while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2))	{
				$clickedMails[] = $row2['pid'];
			}
		}
		// get rid of multiple pids:
		$out = array_unique($clickedMails);
		return $out;
	}

	/**
	 * Returns stats for sent but not clicked mails
	 *
	 * @return	array		with sent but not clicked mails
	 */
	function getNotClickedMails() {
		$out = array_diff(tx_t3m_stats::getSentMails(),tx_t3m_stats::getClickedMails());
		return $out;
	}

	/**
	 * Returns stats for clicked mails
	 *
	 * @param	[type]		$pids: ...
	 * @return	string		a table with stats for clicked mails
	 */
	function tableForClickedMails($pids) {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('ClickedMails').'</h3>';
		if ($pids) {
			$pids = array_intersect(tx_t3m_stats::getClickedMails(),$pids);
		} else {
			$pids = tx_t3m_stats::getClickedMails();
		}
		if (!$pids) {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		} else {
			$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('PageClicks').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('LinksClicked').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('PageClickRatio').'</td>
				</tr>';


			// sort pids by clicks:
			foreach ($pids as $pid) {
				$pidClickCount[$pid] = tx_t3m_stats::mailClicks($pid);
			}
			arsort($pidClickCount);

	// 		t3lib_div::debug($pidClickCount);
			//now get all the clicks for the pids.
			foreach ($pidClickCount as $key => $val) {
				$out .= '<tr><td>'.tx_t3m_mailings::getPageName($key).'</td>
					<td>'.tx_t3m_mailings::viewPage($key).'</td>
					<td>'.tx_t3m_stats::getOpenedSentTime($key).'</td>
					<td>'.$val.'</td>
					<td>';
				$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid',
					'tx_tcdirectmail_sentlog',
					'pid = '.$key
				);
				$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
					<td class="c-headLineTable">URL</td>
					<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('clicks').'</td></tr>';
				while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2))	{
	// 				$out .= $row2['uid'];
					$res3 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'url,SUM(opened) as iopened',
						'tx_tcdirectmail_clicklinks',
						'opened > 0 AND sentlog = '.$row2['uid'],
						'url'
					);
					// if we have a click for this sentlog then save click count for the links
					while($row3 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res3))	{
						$links[$row3['url']] +=  $row3['iopened'];
						//$opened['url'] =  $row3['iopened'];
						//$out .=	'<td>'.tx_t3m_stats::mailClickedLinks($row2['pid']).'</td>';
					}
				}
				foreach ($links as $key2 => $val2) {
					$out .= '<tr><td>'.$key2.'</td>
						<td>'.$val2.'</td></tr>';
				}
				$out .=	'</table>';
				$out .=	'</td><td>'.round((($val / tx_t3m_stats::getSendCountForPage($key)) * 100),2).'%</td></tr>';
				$links = '';
			}

	// 			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	// 				'pid,sendtime,beenthere',
	// 				'tx_tcdirectmail_sentlog',
	// 				'uid = '.$row['sentlog'],
	// 				'pid'
	// 			);
	// 			while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2))	{
	// 				if (!($hadPageBefore[$row2['pid']])) {
	// 					$out .= '<tr><td>'.tx_t3m_mailings::getPageName($row2['pid']).'</td>
	// 						<td>'.tx_t3m_mailings::viewPage($row2['pid']).'</td>
	// 						<td>'.t3lib_BEfunc::datetime($row2['sendtime']).'</td>
	// 						<td>'.tx_t3m_stats::mailClicks($row2['pid']).'</td>
	// 						<td>'.tx_t3m_stats::mailClickedLinks($row2['pid']).'</td>
	// 						</tr>';
	// 					$hadPageBefore[$row2['pid']] = true;
	// 				}
	// 			}

			$out .= '</table>';
		}
		return $out;
	}

	/**
	 * Returns stats for clicked mails
	 *
	 * @param	[type]		$pids: ...
	 * @return	string		a table with stats for clicked mails
	 */
	function tableForNotClickedMails($pids) {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('NotClickedMails').'</h3>';
		if ($pids) {
			$pids = array_intersect(tx_t3m_stats::getNotClickedMails(),$pids);
		} else {
			$pids = tx_t3m_stats::getNotClickedMails();
		}
		if (!$pids) {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		} else {
			$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('View').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('SendDate').'</td>
				</tr>';
			foreach ($pids as $pid) {
				$out .= '<tr><td>'.tx_t3m_mailings::getPageName($pid).'</td>
					<td>'.tx_t3m_mailings::viewPage($pid).'</td>
					<td>'.tx_t3m_stats::getNotOpenedSentTime($pid).'</td></tr>';
			}
			$out .= '</table>';
		}
		return $out;
	}

	/**
	 * Returns stats for clicked mails
	 *
	 * @return	string		a table with stats for clicked mails
	 */
	function statsForClickedMails() {

		// get all urls and clicks
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'sentlog,url',
			'tx_tcdirectmail_clicklinks',
			'opened > 0' // opened: 'pageclick'
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { //get the URLs and click counts:
			$clickcount['total'] += 1;
			$clickcount['urls'][$row['url']] += 1;
			$logs[] = $row['sentlog'];
		}
		$totalMailsCount = tx_t3m_stats::getSentMailsSingle();
		$clickedMailsCount = tx_t3m_stats::getClickedMailsSingle();
		$notClickedMailsCount = ($totalMailsCount - $clickedMailsCount);


		$out .= '<h3>'.$GLOBALS['LANG']->getLL('Overview').'</h3>';
		$out .= '<table class="typo3-dblist">';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('TotalPageClicks').'</td><td>'.$clickcount['total'].'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('ClickedMails').'</td><td>'.$clickedMailsCount.'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('NotClickedMails').'</td><td>'.($totalMailsCount - $clickedMailsCount).'</td></tr>';
		$out .= '<tr><td>'.$GLOBALS['LANG']->getLL('PageClickRatio').'</td><td>'.round((($clickedMailsCount / ($totalMailsCount - $clickedMailsCount)) * 100),2).'%</td></tr>';
		$out .= '</table>';

		// STATIC DATA:
		// PIE
		$filename = t3lib_extMgm::extPath('t3m').'res/pbimagegraph/pie.txt';
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		$ts = t3lib_div::makeInstance("t3lib_tsparser");
		$ts->parse($contents); // now we should have it in $ts->setup
		$static_pie = $ts->setup['lib.']['pbimagegraph.'];
		fclose($handle);

		// clicked / not clicked
		$pie_clicked = $static_pie;
		$pie_clicked['10.']['10.']['text'] = $GLOBALS['LANG']->getLL('ClickedMails').' / '.$GLOBALS['LANG']->getLL('NotClickedMails');

		$pie_clicked['10.']['20.']['10.']['10.']['dataset.']['10.']['10'] = 'point';
		$pie_clicked['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['x'] = $GLOBALS['LANG']->getLL('ClickedMails').': '.$clickedMailsCount;
		$pie_clicked['10.']['20.']['10.']['10.']['dataset.']['10.']['10.']['y'] = $clickedMailsCount;
		$pie_clicked['10.']['20.']['10.']['10.']['fillStyle.']['1'] = 'addColor';
		$pie_clicked['10.']['20.']['10.']['10.']['fillStyle.']['1.']['color'] = 'green';

		$pie_clicked['10.']['20.']['10.']['10.']['dataset.']['10.']['20'] = 'point';
		$pie_clicked['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['x'] = $GLOBALS['LANG']->getLL('NotClickedMails').': '.$notClickedMailsCount;
		$pie_clicked['10.']['20.']['10.']['10.']['dataset.']['10.']['20.']['y'] = $notClickedMailsCount;
		$pie_clicked['10.']['20.']['10.']['10.']['fillStyle.']['2'] = 'addColor';
		$pie_clicked['10.']['20.']['10.']['10.']['fillStyle.']['2.']['color'] = 'red';

		$out .=' <br />'.tx_pbimagegraph_ts::make($pie_clicked);


		$out .= '<h3>Total URLs clicked </h3>'; // limit to top ten?
		$out .= $GLOBALS['LANG']->getLL('descriptionTotalClicks');
		arsort ($clickcount['urls']); // sort by most successful ones
		$out .= '<table class="typo3-dblist"><tr class="c-headLineTable">
			<td class="c-headLineTable">URL</td>
			<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('clicks').'</td>
			</tr>';
		foreach (array_keys($clickcount['urls']) as $url) {
			$out .= '<tr><td><a href="'.$url.'">'.$url.'</a></td>
				<td>'.$clickcount['urls'][$url].'</td></tr>';
		}
		$out .= '</table>';


		$out .= tx_t3m_stats::tableForClickedMails();
		$out .= tx_t3m_stats::tableForNotClickedMails();


		// 2do;
		// VISITS, ADLEADS, ADSALES possible ??? and how?

		// - 'cost' related
		// - 'performance', 'KPI', 'management ratio'
		// - graph showing total deliveries vs opened vs clicked


		return $out;
	}

	/**
	 * Returns how many users that page was sent to
	 *
	 * @param	int		pid of page
	 * @return	int		send count how often the page was sent (how many recipients got the page)
	 */
	function getSendCountForPage($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(*) as icount',
			'tx_tcdirectmail_sentlog',
			'pid = '.intval($pid)
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = '';
		if ($row['icount'] != 0) {
			$out = $row['icount'];
		}
		return $out;
	}

	/**
	 * Returns stats for bounces
	 *
	 * @return	string		a table with stats for bounces
	 */
	function statsForBounces() {
		$out = true;
		return $out;
	}


	/**
	 * Return table for clearing invalid logs encountered by tcdirectmail
	 *
	 * @return	table for clearing invalid logs encountered by tcdirectmail
	 * @todo	better integration into tcdirectmail (function calls - return values? - find log errors globally !)
	 */
	function maintenanceLogs() {
		$out = '<h3>'.$GLOBALS['LANG']->getLL('maintenanceLogdata').'@todo...</h3>';
		$mails = tx_t3m_send::getSentMails(); //@todo: change to pages for which invalid log data is found
		$out .= tx_t3m_settings::tableForLogdataMaintenance($mails);

		return $out;
	}


	/**
	 * Function for clearing invalig logs
	 *
	 * @param	array		$pids: mail ids
	 * @return	string		table for clearing invalig logs and check validity
	 */
	function tableForLogdataMaintenance($pids) {
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('maintenanceLogdata').'</td></tr>';
			foreach($pids as $pid) {
				$out .= '<tr><td>'.$pid['title'].'</td>
					<td><form><input type="submit" name="clear_invalid" value="'.$GLOBALS['LANG']->getLL('maintenanceLogdata').'" />
					<input type="hidden" name="id" value="'.$pid['uid'].'" /></form></td></tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}


	/**
	 * Function for clearing invalig logs
	 *
	 * @param	array		$pids: array with uids of pages
	 * @return	boolean		if clearing invalig logs went ok
	 */
	function clearInvalidLogdata($pid) { //rewrite of tcdirectmail?
		if ($pid) { //
		} else { // clear all log data
		}
	}



}


?>