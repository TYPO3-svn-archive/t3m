<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Stefan Koch <typo3@stefkoch.de>
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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Salutation' for the 't3m' extension.
 *
 * @author	Stefan Koch <typo3@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_pi1 extends tslib_pibase {
	var $prefixId = 'tx_t3m_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_t3m_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 't3m';	// The extension key.
	var $pi_checkCHash = TRUE;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	string		$out: The content that is displayed on the website (Header)
	 */
	function main($content,$conf)	{
		$out = '<H2>';
		$out .= $this->cObj->data['header'];
		$out .= '</H2>';
		$out .= '<H3>';

		if ($_REQUEST['tx_t3m_pi1']) { // we are called by tcdirectmail!
			// e.g. http://my.domain/index.php?id=123&tx_t3m_pi1[fe_user_uid]=1

			$GP = $_REQUEST['tx_t3m_pi1'];


			// get the fe_user
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'first_name,last_name,gender,tx_t3m_salutation',
				'fe_users',
				'uid='.$GP['fe_user_uid']
			);
			$user = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1);

			// get the salutation
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_t3m_salutations',
				'uid='.$user['tx_t3m_salutation']
			);
			$salutation = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2);



			// use male or female form?
			if ($user['gender'] == 0)	{ //'e.g. Dear Mr.'
				$out .= $salutation['single_male'];
			} elseif ($user['gender'] == 1) {
				$out .= $salutation['single_female'];
			} else { // not specified
				$out .= $salutation['plural'];
			}


			//include names if we are supposed to and if the names exist
			if (($salutation['include_first_name']) && ($user['first_name'])) {
				$out .= ' '.$user['first_name'];
			}
			if (($salutation['include_last_name']) && ($user['last_name'])) {
				$out .= ' '.$user['last_name'];
			}

			$out .= $salutation['append'];

		}
		$out .= '</H3>';
		return $out;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/pi1/class.tx_t3m_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3m/pi1/class.tx_t3m_pi1.php']);
}

?>