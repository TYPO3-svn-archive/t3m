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
 * Actions for spam evalutation (e.g. runs spam checks and saves spam values)
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_spam	{

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
	 * Returns an evaluation for spam
	 *
	 * @param	[type]		$pid: ...
	 * @return	string		an evaluation for spam
	 */
	function checkForSpam($pid)	{
		$out = '<br />'.$GLOBALS['LANG']->getLL('checkingForSpam').':';
// 		$out .= '<br /> '.$GLOBALS['LANG']->getLL('runningScript').': '.$this->myConf['spam_checker_script'].'<br />';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'pages',
			'uid = '.intval($pid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$mailer = tx_tcdirectmail_tools::getConfiguredMailer($row);
		$mailcontent = $mailer->html;
// 		$mailcontent = 'test mer ma hier';

		$spamstring .= exec('echo \''.escapeshellcmd($mailcontent).'\' | '.$this->myConf['spam_checker_script']);


		if ($spamstring == '') {
			$out .= $GLOBALS['LANG']->getLL('errorSpamc');
		} elseif ($spamstring == '0/0') {
			$out .= $GLOBALS['LANG']->getLL('errorSpamc');
		} else {
			$spamarray = explode('/',$spamstring);
			$spamscore = floatval($spamarray[0])/floatval($spamarray[1]);
			$out .= tx_t3m_spam::imgSpamCheck($spamscore).'<br />';
			if ($spamscore > 1) {
				$out .= $GLOBALS['LANG']->getLL('thisisspam');
			} elseif ($spamscore > 0.5) {
				$out .= $GLOBALS['LANG']->getLL('thisisnearlyspam');
			} else {
				$out .= $GLOBALS['LANG']->getLL('thisisnotspam');
			}
			tx_t3m_spam::saveSpamScore($pid, $spamscore);
			$out .= '<br/>'.$GLOBALS['LANG']->getLL('spamProbability').': '.$spamstring.' = '.$spamscore; //'content:- '.strip_tags($mailcontent)
		}

		return $out;
	}


	/**
	 * Saves a spamscore for a page
	 *
	 * @param	pid		page id
	 * @param	spamscore		spamscore
	 * @return	[type]		...
	 */
	function saveSpamScore($pid, $spamscore)	{
		$fields_values = array(
			'tx_t3m_spam_score' => floatval($spamscore)
		);
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'pages',
			'uid = '.intval($pid),
			$fields_values
			);
	}

	/**
	* Gets a spamscore for a page
	*
	* @param pid page id
	* @return float spamscore
	*/
	function getSpamScore($pid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_t3m_spam_score',
			'pages',
			'uid = '.intval($pid)
			);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$out = $row['tx_t3m_spam_score'];
		return $out;
	}


	/**
	 * Returns image indicating if the score is spam or not
	 *
	 * @param	[type]		$spamscore: ...
	 * @return	string		image indicating if the score is spam or not
	 */
	function imgSpamCheck($spamscore) {
		if ($spamscore == 0) { //not checked
			$out .= '';
		} elseif ($spamscore > 1) { //spam
			$out .= $this->iconImgError;
		} elseif ($spamscore > 0.5) {
			$out .= $this->iconImgWarning;
		} else { //nospam
			$out .= $this->iconImgOk;
		}
		return $out;
	}


	/**
	 * Returns a button for spamcheck
	 *
	 * @param	int		$pid: page uid
	 * @return	string		button for spamcheck
	 */
	function formSpamCheck($pid)	{
		$out = '<form><input type="submit" name="check_for_spam" value="'.$GLOBALS['LANG']->getLL('spamOrNot').'" />
			<input type="hidden" name="id" value="'.intval($pid).'" /></form>';
		return $out;
	}


}


?>