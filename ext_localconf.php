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

## WOP:[pi][1][addType]
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_t3m_pi1.php','_pi1','header_layout',1);
?>