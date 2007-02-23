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

/**
 * This is the tx_directmail_target's super class.
 * All directmail targets must inherit from this class though some inheritance path.
 *
 * @abstract
 */

class tx_tcdirectmail_target {
   var $fields;
   var $data;

   /**
    * This is the object factory, without init(), for all directmail targets.
    *
    * @final
    * @static
    * @param     integer     Uid of a tx_directmail_target from the database.
    * @return    object      Of directmail_target type.
    */
   function getTarget($uid) {
       global $TYPO3_DB;

       $rs = $TYPO3_DB->sql_query("SELECT * FROM tx_tcdirectmail_targets WHERE uid = $uid");
       $fields = $TYPO3_DB->sql_fetch_assoc($rs);
       $object = new $fields['targettype'];
       if (is_subclass_of($object, 'tx_tcdirectmail_target')) {
         $object->fields = $fields;
         return $object;
       } else {
         die ("Ooops..   $fields[targettype] is not a tx_tcdirectmail_target child class");
       }
   }

   /**
    * This is the object factory, with init(), for all directmail targets.
    *
    * @final
    * @static
    * @param     integer     Uid of a tx_directmail_target from the database.
    * @return    object      Of directmail_target type.
    */
   function loadTarget ($uid) {
      $object = tx_tcdirectmail_target::getTarget($uid);
      $object->init();
      return $object;
   }

   /**
    * This is the method called when a directmail_target is produced in the loadTarget factory
    *
    * @abstract
    * @return    void
    */
   function init() {
      die ('You need to implement the init-method.');
   }

   /**
    * Fetch one receiver record from the directmail target.
    * The record MUST contain an "email"-field. Without this one this mailtarget is useless.
    * In order for registered links to work, it should also contain an "authCode"-field.
    * To collect bounces you will need to have both fields "authCode" and "uid".
    * For compatibility with various subscription systems, the record can contain "tableName"-field.
    *
    * @abstract
    * @return   array      Assoc array with fields for the receiver
    */
   function getRecord() {
       die ('You need to implement the getRecord-method.');
   }

   /**
    * Reset the directmail target, so the next record being fetched will be the first.
    * Not currently in use by any known extension
    *
    * @abstract
    * @return   void
    */
   function resetTarget() {
       die ('You need to implement the resetTarget-method.');
   }

   /**
    * Get the number of receivers in this directmail target
    *
    * @abstract
    * @return   integer      Numbers of receivers.
    */
   function getCount() {
       die ('You need to implement the getCount-method.');
   }

   /**
    * Get error text if the fetching of the directmail target has somehow failed.
    *
    * @abstract
    * @return   string      Error text or empty string.
    */
   function getError() {
       die ('You need to implement the getError-method.');
   }

   /**
    * Here you can implement start events for a real send-out. This is not mandatory.
    *
    * @return    void
    */
   function startReal() {
   }

   /**
    * Here you can implement end events for a real send-out. This is not mandatory.
    *
    * @return    void
    */
   function endReal() {
   }

   /**
    * Here you can implement database operation done when an email address has failed.
    * This is typically disabling or deletion of the record. The $this->tableName-attribute is also used.
    * For external data-sources, you might consider collecting the addresses for later removal from the foreign system. 
    * Bounces can only be expected to work if the record contains "uid" and "authCode" fields. 
    * "tableName" should also be included for compatibility reasons. 
    * It is not mandatory to do anything, but bounce handling cannot work without it.
    *
    * @param   integer    Uid of the address that has failed.
    * @param   integer    Status of the bounce expect: TCDIRECTMAIL_HARDBOUNCE or TCDIRECTMAIL_SOFTBOUNCE 
    * @return  bool       Status of the success of the removal.
    */

   function disableReceiver($uid, $authcode, $bounce_type) {
       global $TYPO3_DB;

       if (t3lib_div::stdAuthCode($uid) == $authCode && $this->tableName <> 'undefinedtable') {
//         $TYPO3_DB->sql_query("DELETE FROM $this->tableName WHERE uid = $uid");
//         $TYPO3_DB->sql_query("UPDATE $this->tableName SET deleted = 1 WHERE uid = $uid");
           $TYPO3_DB->sql_query("UPDATE $this->tableName SET disable = 1 WHERE uid = $uid"); //stefan: i like disable = 1 best because deregistrations from srfeuserregister sets deleted = 1 and so i can differ disabling-reason "deregistered/bounced". :)
           return $TYPO3_DB->sql_affected_rows();
       } else {
           return false;
       }
   }

}

?>