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

require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_target_array.php');
require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_main.php');
require_once(t3lib_extMgm::extPath('t3m').'class.tx_t3m_bounce.php');

/**
 * Debug class
 *
 * @author	Stefan Koch <t3m@stefkoch.de>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_t3m_debug {


	/**
	 * Returns some system infos
	 *
	 * @return	string		some interesting stuff
	 */
	function testSomeStuff()	{

		$class = $GLOBALS['TYPO3_DB'];  //t3lib_extMgmt3lib_BEfunc't3lib_iconWorks';//
 		$out[0] = get_declared_classes();
		$out[1] = get_class_methods($class);
		$out[2] = get_defined_constants();
		$out[3] = $GLOBALS['TYPO3_CONF_VARS']; //$GLOBALS['TCA']. $GLOBALS['TBE_STYLES']
		t3lib_div::debug($out);

		return $out;
	}

}

?>