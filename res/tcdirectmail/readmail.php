#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/advanced_connect.php');
require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_bouncehandler.php');
require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_tools.php');

$fd = fopen('php://stdin', 'r');
while ($buffer = fread($fd, 8096)) {
   $content .= $buffer;
}
fclose($fd);

$bounce = new tx_tcdirectmail_bouncehandler($content);

print 'We have a '.$type."\n";
switch ($bounce->status) {

	case 'TCDIRECTMAIL_HARDBOUNCE':
		$target = tx_tcdirectmail_target::getTarget(intval($bounce->targetUid));
		$out = $target->disableReceiver($bounce->uid, $bounce->authCode, $type);
	break;

	case 'TCDIRECTMAIL_SOFTBOUNCE':
		$target = tx_tcdirectmail_target::getTarget(intval($bounce->targetUid));
		$out = $target->disableReceiver($bounce->uid, $bounce->authCode, $type);
	break;

	case 'TCDIRECTMAIL_BOUNCE_UNREMOVABLE':
	$TYPO3_DB->exec_UPDATEquery(
		'tx_tcdirectmail_sentlog', 		
		'cryptid = '.$bounce->authCode.' AND uid = '.$bounce->sendid,
		array('bounced' => 1), 
		);
	break;

	default: 	/* Nothing to be done for other mail types. */
		$out = "No bounce \n";
	break;
}

print $out;

?>
