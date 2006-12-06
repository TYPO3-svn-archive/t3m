<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_t3m_campaigns=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_t3m_targetgroups=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_t3m_categories=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_t3m_salutations=1
');
$TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['includeClassFiles'][] = t3lib_extMgm::extPath('t3m').'class.tx_t3m_target1.php';

?>