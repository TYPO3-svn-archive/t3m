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
 * Class for showing some send options
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_send {

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
	 * Returns
	 *
	 * @return
	 */
	function sendMails()	{
		//sending mails:
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('currentlysendingmails').'</h3>';
		$mails = tx_t3m_send::getSendingMails();
		if ($mails) {
			foreach ($mails as $mail) {
				$out .= $mail['title'].'<br />';
			}
		} else {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		}

		//unsentmails:
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('unscheduledmails').'</h3>';
		$mails = tx_t3m_send::getUnsentUnscheduledMails();
		if ($mails) {
			$out .= tx_t3m_mailings::tableForMails($mails);
		} else {
			$out .= $GLOBALS['LANG']->getLL('nomails');
		}

		//scheduled mails:
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('scheduledmails').'</h3>';
		$mails = tx_t3m_send::getUnsentScheduledMails();
		if ($mails) {
			$out .= tx_t3m_send::tableForScheduledMails($mails);
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
		$mails = tx_t3m_send::getSentMails();
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('sentmails').'</h3>';
		$out .= tx_t3m_mailings::tableForMails($mails);

		return $out;
	}


	/**
	 * Returns
	 *
	 * @return
	 */
	function sendTestMails()	{
		$mails = tx_t3m_send::getUnsentUnscheduledMails();
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('unscheduledmails').'</h3>';
		$out .= tx_t3m_send::tableForTestMails($mails);

		$mails = tx_t3m_send::getUnsentScheduledMails();
		$out .= '<h3>'.$GLOBALS['LANG']->getLL('scheduledmails').'</h3>';
		$out .= tx_t3m_send::tableForTestMails($mails);

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
	 * Returns a table for sending test mails
	 *
	 * @param	array		$pids mailings
	 * @return	string		table for sending test mails
	 */
	function tableForTestMails($pids)	{
		if (count($pids) > 0) {
			$out = '<table class="typo3-dblist"><tr class="c-headLineTable">
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('Title').'</td>
				<td class="c-headLineTable">'.$GLOBALS['LANG']->getLL('testemail').'</td></tr>';
			foreach($pids as $mail) {
				$out .= '<tr><td>'.$mail['title'].'</td>
					<td>'.tx_t3m_send::formForTestSendingMailing($mail['uid']).'</td>
					</tr>';
			}
			$out .= '</table>';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}


	/**
	 * Returns a table for sending mails
	 *
	 * @param	array		$pids mailings
	 * @return	string		table for sending mails
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
					<td></td>';
				$out .= '<td';
				if ($mail['tx_tcdirectmail_senttime'] < time()) {
					$out .= ' bgcolor="red" title="'.$GLOBALS['LANG']->getLL('errorNotSent').'"';
				}
				$out .= '>'.strftime('%Y-%m-%d %H:%M:%S',intval($mail['tx_tcdirectmail_senttime'])).'</td>
					<td>'.tx_t3m_mailings::editTCDirectmailSendtime($mail['uid']).'</td></tr>';
			}
			$out .= '</table';
		} else {
			$out = $GLOBALS['LANG']->getLL('nomails');
		}
		return $out;
	}


	/**
	 * Returns a link for sending a mailing
	 *
	 * @param	int		$pid of mailing
	 * @return	string		a link for editing all extension settings
	 */
	function formForSendingMailing($pid) {
		$out = '<form><input type="submit" name="send_now" value="'.$GLOBALS['LANG']->getLL('send_now').'" />
			<input type="hidden" name="id" value="'.intval($pid).'" /></form>';
		return $out;
	}

	/**
	 * Returns a link for sending a mailing
	 *
	 * @param	int		$pid of mailing
	 * @return	string		a link for editing all extension settings
	 */
	function formForTestSendingMailing($pid) {
		$out = '<form><input type="submit" name="send_test" value="'.$GLOBALS['LANG']->getLL('send_test').'" />
			<input type="hidden" name="id" value="'.intval($pid).'" /></form>';
		return $out;
	}


}


?>