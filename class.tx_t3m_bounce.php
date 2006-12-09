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
 * This function is a dummy test class
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package TYPO3
 * @subpackage tx_t3m
 */
class tx_t3m_bounce	{
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
	}

	/**
	 * Initialize some variables
	 *
	 * @return	[type]		...
	 */
	function init()	{
	}

	/**
	 * Returns an evaluation for bounce mails
	 *
	 * @param	[type]		$pid: ...
	 * @return	string		an evaluation for spam
	 */
	function checkForBounceMail($pid)	{
		$out = '<br />Checking for bounce mails:'.$pid.' <br />';
		$sender_email = explode('@',$this->myConf['sender_email']);
		$mailuser = $sender_email[0];
		$mailpassword = $this->myConf['email_password'];

		$mailserver =$sender_email[1];
		$mailserver ='localhost';
		$mailserver ='127.0.0.1';
		$mailserver ='{127.0.0.1:110/pop3}';
		$mailserver ='{127.0.0.1:143/notls}INBOX';



		$out .= '<br />trying:'.$mailserver.$mailuser.$mailpassword."<p><h1>Mailboxes</h1>\n";

		$mbox = imap_open ($mailserver, $mailuser, $mailpassword, OP_HALFOPEN);
 		t3lib_div::debug($mbox);
		$debug1 = get_class_methods($mbox);
 		t3lib_div::debug($debug1);

		if(!$mbox) {
			$out .= "1a:".imap_last_error();
		}

		$out .= "<p><h1>Check</h1>\n";
		$check = imap_check ($mbox);
 		t3lib_div::debug($check);
		if($check) {
			$out .= "Date: "    . $check->Date    . "<br>\n" ;
			$out .= "Driver: "  . $check->Driver  . "<br>\n" ;
			$out .= "Mailbox: "  . $check->Mailbox . "<br>\n" ;
			$out .= "Messages: " . $check->Nmsgs  . "<br>\n" ;
			$out .= "Recent: "  . $check->Recent  . "<br>\n" ;
		} else {
			$out .= "1b:".imap_last_error(). "<br>\n";
		}

		$out .= "<p><h1>Status</h1>\n";
// 		$mbox = imap_open ('{'.$mailserver.':143/notls}INBOX', $mailuser, $mailpassword) || $out .= "erro:".imap_last_error();
		$status = imap_status ($mbox, $mailserver, SA_ALL);
 		t3lib_div::debug($status);
		if($status) {
			$out .= "Messages:    " . $status->messages   . "<br>\n";
			$out .= "Recent:      " . $status->recent     . "<br>\n";
			$out .= "Unseen:      " . $status->unseen     . "<br>\n";
			$out .= "UIDnext:    " . $status->uidnext     . "<br>\n";
			$out .= "UIDvalidity: " . $status->uidvalidity . "<br>\n";
		} else {
			$out .= "1c:".imap_last_error();
		}

		$out .= "<p><h1>Folders</h1>\n";
		$folders = imap_listmailbox ($mbox, $mailserver, "*");
 		t3lib_div::debug($folders);
		if ($folders == false) {
			$out .= "Call failed<br>\n";
			$out .= "2:".imap_last_error();
		} else {
			while (list ($key, $val) = each ($folders)) {
				$out .= $val . "<br>\n";
			}
		}

		$out .= "<p><h1>Headers</h1>\n";
		$headers = imap_headers ($mbox);
 		t3lib_div::debug($headers);
		if ($headers == false){
			$out .= "Call failed<br>\n";
			$out .= "3:".imap_last_error();
		}
		else {
			while (list ($key, $val) = each ($headers)) {
				$out .= $val . "<br>n";
			}
		}

		// get imap_fetch header and put single lines into array
		$header = explode("\n", imap_fetchheader($mbox, 1));
		t3lib_div::debug($header);
		// browse array for additional headers
		if (is_array($header) && count($header)) {
			$head = array();
			foreach($header as $line) {
				// is line with additional header?
				if (eregi("^X-", $line)) {
				// separate name and value
				eregi("^([^:]*): (.*)", $line, $arg);
				$head[$arg[1]] = $arg[2];
				}
			}
		}
// imap_search
//
// imap_listscan($mbox, ref, pattern, content);
// 		$out .= "<p><h1>List</h1>\n";
// 		$list = imap_list($mbox, $mailserver, "*");
//  		t3lib_div::debug($list);
// 		if (is_array($list)) {
// 			foreach ($list as $val) {
// 				$out .= imap_utf7_decode($val) . "<br />\n";
// 			}
// 		} else {
// 			$out .=  "4 imap_list failed: " . imap_last_error() . "\n";
// 		}

		imap_close ($mbox);

		return $out;
	}



	/**
	 * Returns users soft bounce record
	 *
	 * @param	int		uid of fe_users
	 * @return	int		bounce count
	 */
	function getPreviousSoftBounces($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_t3m_softbounces',
			'fe_users',
			'uid='.intval($uid)
			);
		$i = 0;
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['tx_t3m_softbounces'];
		return $out;
	}

	/**
	 * Returns users hard bounce record
	 *
	 * @param	int		uid of fe_users
	 * @return	int		bounce count
	 */
	function getPreviousHardBounces($uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_t3m_hardbounces',
			'fe_users',
			'uid='.intval($uid)
			);
		$i = 0;
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['tx_t3m_hardbounces'];
		return $out;
	}

	/**
	 * Lets users define bounce management rules
	 *
	 * @return	array		bounce config
	 */
	function getBounceRules()	{
// 		$out = '<form>How many returns should be allowed befor users get disabeld?<br/><input type="text" name="bouncecount" /></form><br/>';
// 		$reason_text = array(
// 		'550' => 'no mailbox|account does not exist|user unknown|user is unknown|unknown user|unknown local part|unrouteable address|does not have an account here|no such user|user not listed|account has been disabled or discontinued|user disabled|unknown recipient|invalid recipient|recipient problem|recipient name is not recognized|mailbox unavailable|550 5\.1\.1 recipient|status: 5\.1\.1|delivery failed 550|550 requested action not taken|receiver not found|unknown or illegal alias|is unknown at host|is not a valid mailbox|no mailbox here by that name|we do not relay|5\.7\.1 unable to relay|cuenta no activa|inactive user|user is inactive|mailaddress is administratively disabled|not found in directory|not listed in public name & address book|destination addresses were unknown|rejected address|not listed in domino directory|domino directory entry does not',
// 		'551' => 'over quota|quota exceeded|mailbox full|mailbox is full|not enough space on the disk|mailfolder is over the allowed quota|recipient reached disk quota|temporalmente sobre utilizada|recipient storage full|mailbox lleno|user mailbox exceeds allowed size',
// 		'552' => 't find any host named|unrouteable mail domain|not reached for any host after a long failure period|domain invalid|host lookup did not complete: retry timeout exceeded|no es posible conectar correctamente',
// 		'554' => 'error in header|header error|invalid message|invalid structure|header line format error');
// 		t3lib_div::debug($reason_text);
		$out['max_hardbounce'] = 2; //$this->myConf['max_hardbounce']
		$out['max_softbounce'] = 3; //$this->myConf['max_softbounce']
		return $out;
	}

}


?>