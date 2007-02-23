<?php
/*************************************************************** 
*  Copyright notice 
* 
*  (c) 2006 Daniel Schledermann <daniel@typoconsult.dk> 
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
 
require_once(PATH_t3lib.'class.t3lib_extmgm.php');
require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(t3lib_extMgm::extPath('tcdirectmail').'class.tx_tcdirectmail_mailer.php'); 
foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tcdirectmail']['includeClassFiles'] as $file) {
    require_once($file);
}
 
/**
 * Toolbox for tcdirectmail and dependant extensions.
 *
 * @static
 */ 

class tx_tcdirectmail_tools {

    /**
     * Function to fetch the proper domain from with to fetch content for tcdirectmail.
     * This is either a sys_domain record from the page tree or the fetch_path property.
     *
     * @param    array       Record of page to get the correct domain for.
     * @return   string      Correct domain.
     */
    function getDomainForPage($p) {
       global $TYPO3_DB;

       /* Is anything hardcoded from TYPO3_CONF_VARS? */
       $theConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tcdirectmail']);
       if ($theConf['fetch_path']) {
          return $theConf['fetch_path'];
       }
       
       /* Else we try to resolve a domain */
    
       /* What pages to search */
       $pids = array_reverse(t3lib_befunc::BEgetRootLine($p['uid']));

       foreach ($pids as $page) {
          /* Domains */
          $rs = $TYPO3_DB->sql_query("SELECT domainName FROM sys_domain
                                            INNER JOIN pages ON sys_domain.pid = pages.uid
                                            WHERE NOT sys_domain.hidden
                                            AND NOT pages.hidden
                                            AND NOT pages.deleted
                                            AND pages.uid = $page[uid]
                                            ORDER BY sys_domain.sorting
                                            LIMIT 0,1");
          if ($TYPO3_DB->sql_num_rows($rs)) {
             list($domain) = $TYPO3_DB->sql_fetch_row($rs);
          }
       }

       return $domain;
    }

    /**
     * Gets the correct sendername for a tcdirectmail.
     * This is either:
     * The sender name defined on the page record.
     * or the sender name defined in $TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['senderName']
     * or The sites name as defined in $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']
     *
     * @param    array       Record of the directmail page
     * @return   string      The sender name
     */
    function getSenderForPage ($p) {
       global $TYPO3_DB;
    
       /* The sender defined on the page? */
       if ($p['tx_tcdirectmail_sendername']) {
          return $p['tx_tcdirectmail_sendername'];
       }
    
       /* Anything in typo3_conf_vars? */
       $theConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tcdirectmail']);
       $sender = $theConf['sender_name'];        
       if ($sender == 'user') {
          /* Use the page-owner as user */
          $rs = $GLOBALS['TYPO3_DB']->sql_query("SELECT realName 
                                                  FROM be_users bu
                                                  LEFT JOIN pages p ON bu.uid = p.perms_userid
                                                  WHERE p.uid = $p[uid]");
                
          list($sender) = $GLOBALS['TYPO3_DB']->sql_fetch_row($rs);
          if ($sender) {
             return $sender;
          }
       }
       
       /* Maybe it was a specifies name */
       if ($sender && $sender != 'user') {
          return $sender;
       }
    
       /* If none of above, just use the sitename */
       return $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
    }

    /**
     * Gets the correct sender email address for a tcdirectmail.
     * This is either:
     * The sender email address defined on the page record.
     * or the email address (if any) of the be_user owning the page.
     * or the email address defined in extConf
     * or the guessed email address of the user running the this process.
     * or the no-reply@$_SERVER['HTTP_HOST'].
     *
     * @param    array       Record of the directmail page
     * @return   string      The sender email
     */
    function getEmailForPage ($p) {
       global $TYPO3_DB;
       
       /* The sender defined on the page? */
       if (t3lib_div::validEmail($p['tx_tcdirectmail_senderemail'])) {
          return $p['tx_tcdirectmail_senderemail'];
       }
       
       /* Anything in typo3_conf_vars? */
       $theConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tcdirectmail']);
       $email = $theConf['sender_email'];        
       if ($email == 'user') {
          /* Use the page-owner as user */
          $rs = $GLOBALS['TYPO3_DB']->sql_query("SELECT email 
                                                  FROM be_users bu
                                                  LEFT JOIN pages p ON bu.uid = p.perms_userid
                                                  WHERE p.uid = $p[uid]");
                
          list($email) = $GLOBALS['TYPO3_DB']->sql_fetch_row($rs);
          if (t3lib_div::validEmail($email)) {
             return $email;
          }
       }
       
       /* Maybe it was a hardcoded email address? */
       if (t3lib_div::validEmail($email)) {
          return $email;
       }
   
        /* If this did not yield an email address, try to use the system-user */
       if( ini_get('safe_mode') || TYPO3_OS == 'WIN'){
          return  "no-reply@".$_SERVER['HTTP_HOST'];
       }
       
       return  trim(`whoami`).'@'.trim(`hostname -f`);
    }
    
    /**
     * Get the bounce address for the mail 
     *
     * @param   array     Record of the mail page 
     * @return  sting     Email address to collect bounces
     */
    function getBounceAddressForPage($page) {
       global $TYPO3_DB;
       
       $rs = $TYPO3_DB->exec_SELECTquery('email', 'tx_tcdirectmail_bounceaccount', "uid = $page[tx_tcdirectmail_bounceaccount]");
       if (list($address) =$TYPO3_DB->sql_fetch_row($rs)) {
          return $address;
       } else {
          return '';
       }
    }

    /**
     * Update a directmail with a new schedule.
     *
     * @param    array      Page record.
     * @return   void
     */
    function setScheduleAfterSending ($page) {
       global $TYPO3_DB;
  
       $senttime = $page['tx_tcdirectmail_senttime'];
    
       switch ($page['tx_tcdirectmail_repeat']) {
           case 0: $newtime = 0; break;
           case 1: $newtime = 86400 + $senttime; break;
           case 2: $newtime = 7 * 86400 + $senttime; break;
           case 3: $newtime = 14 * 86400 + $senttime; break;
           case 4: list($year, $month, $day, $hour, $minute) = explode('-', date("Y-n-j-G-i", $senttime));
                $month += 1;
                $newtime = mktime ($hour, $minute, 0, $month, $day, $year);
                break;
           case 5: list($year, $month, $day, $hour, $minute) = explode('-', date("Y-n-j-G-i", $senttime));
                $month += 3;
                $newtime = mktime ($hour, $minute, 0, $month, $day, $year);
                break;
           case 6: list($year, $month, $day, $hour, $minute) = explode('-', date("Y-n-j-G-i", $senttime));
                $month += 6;
                $newtime = mktime ($hour, $minute, 0, $month, $day, $year);
                break;
           case 7: list($year, $month, $day, $hour, $minute) = explode('-', date("Y-n-j-G-i", $senttime));
               $year += 1;
               $newtime = mktime ($hour, $minute, 0, $month, $day, $year);
               break;
       }
    
       $TYPO3_DB->exec_UPDATEquery('pages', "uid = $page[uid]", array('tx_tcdirectmail_senttime' => $newtime));
    }
        
    /**
     * Create a configured mailer from a directmail page record.
     * This mailer will have both plain and html content applied as well as files attached.
     *
     * @param    array       Page record.
     * @return   object      tx_tcdirectmail_mailer object preconfigured for sending.
     */
    function getConfiguredMailer ($page, $user) {
       $theConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tcdirectmail']);
    
       /* Configure the mailer */
       $mailer = new tx_tcdirectmail_mailer();
       $mailer->domain = tx_tcdirectmail_tools::getDomainForPage($page);
       $mailer->senderName = tx_tcdirectmail_tools::getSenderForPage($page);
       $mailer->senderEmail = tx_tcdirectmail_tools::getEmailForPage($page);
       $mailer->bounceAddress = tx_tcdirectmail_tools::getBounceAddressForPage($page);
       $mailer->setTitle($page['title']);
       $url = "http://$mailer->domain/index.php?id=$page[uid]&no_cache=1$theConf[append_url]";
	if ($user) {
		$url .= '&tx_t3m_pi1[fe_user_uid]='.intval($user);
	}
       $mailer->setHtml(t3lib_div::getURL($url));
   
       /* Construct plaintext */
       $plain = tx_tcdirectmail_plain::loadPlain($page, $mailer->domain);
       switch ($plain->fetchMethod) {
          case 'src' :  $plain->setHtml($mailer->html); break;
          case 'url' :  $plain->setHtml($url); break;
       }
       $mailer->setPlain($plain->getPlaintext());
   
       /* Attaching files */
       $files = explode (',', $page['tx_tcdirectmail_attachfiles']);
       foreach ($files as $file) {
           if (trim($file) != '') {
              $file = PATH_site."uploads/tx_tcdirectmail/$file";
              $mailer->addAttachment($file);
           }
       }
   
       /* hook for modifing the mailer before finish preconfiguring */
       if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tcdirectmail']['getConfiguredMailerHook'])) {
          foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tcdirectmail']['getConfiguredMailerHook'] as $_classRef) {
             $_procObj = & t3lib_div::getUserObj($_classRef);
             $mailer = $_procObj->getConfiguredMailerHook($mailer, $page);
          }
       }
   
       /* Done preconfiguring mailer */
      return $mailer;    
    }
    
    /**
     * Send a directmail page out to the real receivers.
     * 
     * @param   array        Page record.
     * @param   integer      Actual begin time. 
     * @return  void
     */
    function mailForReal($page, $begintime) {
       global $TYPO3_DB;

       $mailer = &tx_tcdirectmail_tools::getConfiguredMailer($page);

       /* Find the receivers */
       $targets = explode(',',$page['tx_tcdirectmail_real_target']);

       foreach ($targets as $tuid) {
          $target = tx_tcdirectmail_target::loadTarget($tuid);
          $target->startReal();
      
          while ($receiver = $target->getRecord()) {

		if ($page['tx_t3m_personalized']) {
 			$mailer = &tx_tcdirectmail_tools::getConfiguredMailer($page, $receiver['uid']);
		}

             $thetime = time();

             /* Register the receiver */
             $TYPO3_DB->exec_INSERTquery('tx_tcdirectmail_sentlog', array(   
                    'receiver' => $receiver['email'],
                    'begintime' => $begintime,
                    'sendtime' => $thetime,
                    'authcode' => $receiver['authCode'],
                    'pid' => $page['uid']));

             $sendid = $TYPO3_DB->sql_insert_id();
           
	          /* Give it the stamp */
             if ($receiver['uid'] && $receiver['authCode']) {
                $infoHeaders = array('X-tcdirectmail-info' => "//$page[uid]/$tuid/$receiver[uid]/$receiver[authCode]/$sendid//");
             } else {
                $infoHeaders = array();
             }

             /* Spy included? */
             if ($page['tx_tcdirectmail_spy']) {                                                                 
                $mailer->insertSpy($receiver['authCode'], $sendid);
             }
            
             /* Should we register what links have been clicked? */
             if ($page['tx_tcdirectmail_register_clicks']) {
                $mailer->substituteMarkers($receiver);
                $links = $mailer->makeClickLinks($receiver['authCode'], $sendid);
                $mailer->raw_send($receiver, $infoHeaders);
                $mailer->resetMarkers();
             
                /* Write to the DB the links that have been registered */
                foreach ($links as $type => $sublinks) {
                   foreach ($sublinks as $linkid => $url) {
                      $TYPO3_DB->exec_INSERTquery('tx_tcdirectmail_clicklinks', array(
                           'sentlog' => $sendid,
                           'linktype' => $type,
                           'linkid' => $linkid,
                           'url' => $url));
                   }
                }
             } else {
                /* Do the send */
                $mailer->send ($receiver, $infoHeaders);
             }
          }
          $target->endReal();
       }
    }

    /**
     * Send a directmail page out to the test receivers.
     * 
     * @param    array      Page record.
     * @param    array      List with receivers. If this is provided only the receivers in the list will be mailed.
     * @return   void
     */
    function mailForTest($page, $onlyReceivers = array()) {
       global $TYPO3_DB;
    
       $mailer = &tx_tcdirectmail_tools::getConfiguredMailer($page);

       /* Find the users */
       $target = tx_tcdirectmail_target::loadTarget($page['tx_tcdirectmail_test_target']);

       /* Only the provided users? */
       if (count($onlyReceivers)) {
          $targetdata = array();
          /* Then get the record, and replace the target with a new one, containing only this user */
          while ($receiver = $target->getRecord()) {
             if (in_array($receiver['email'], $onlyReceivers)) {
                $targetdata[] = $receiver;
             }
          }
       
          $target = new tx_tcdirectmail_target_array();
          $target->data = $targetdata;
          $target->resetTarget();
       }
   

       while ($receiver = $target->getRecord()) {
          if ($page['tx_tcdirectmail_spy']) {
             $mailer->testSpy();
          }
        
          if ($page['tx_tcdirectmail_register_clicks']) {
             $mailer->substituteMarkers($receiver);
             $mailer->testClickLinks();
             $mailer->raw_send($receiver);   
             $mailer->resetMarkers();
          } else {
             $mailer->send($receiver);
          }
       }
    }
}

?>
