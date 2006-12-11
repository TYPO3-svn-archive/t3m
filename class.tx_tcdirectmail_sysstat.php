<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2006 Daniel Schledermann (daniel@typoconsult.dk)
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
 * Unaltered export of viewSysStatus() function 'tcdirectmail' so it can be used outside.
 *
 * @author	Daniel Schledermann <daniel@typoconsult.dk>
 * @package	TYPO3
 * @subpackage	tx_t3m
 */
class tx_tcdirectmail_sysstat {

   /**
   * Show some system settings
   *
   * @return	string	some system settings
   */
   function viewSysStatus() {
       global $LANG;
       global $TYPO3_DB;
       global $TYPO3_CONF_VARS;
//        global $ICON_PATH;
       $ICON_PATH = $GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('t3m').'gfx/';

      /*********************** cron-scripts **********************/
      /* Check if the cronScripts are setup correctly is executable */
      $theConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['tcdirectmail']);
      $correct_php = $theConf['path_to_php'];

      $content .= '<h3>'.$LANG->getLL('cronjobs').'</h3>';
      if ($_REQUEST['try_correct_path']) {
         foreach ($TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['cliScripts'] as $script => $dummy) {
            /* Correct the sh-bang */
            $mailer_lines = file($script);
            $mailer_lines[0] = "#!$correct_php\n";
            $fd = fopen($script, 'w');
            fwrite($fd, implode("", $mailer_lines));
            fclose ($fd);

            /* Set exe-bit */
            list(,,$mode) = stat($script);
            $mode |= 0110;
            chmod($script, $mode);
         }

         clearstatcache();
      }

      /* Display if it's ok */
      if (file_exists($correct_php)) {
         $content .= str_replace('###PHP_PATH###', $correct_php, $LANG->getLL('current_php_path'));

         foreach ($TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['cliScripts'] as $script => $frequency) {
            $mailer_lines = file($script);
            $current_php = trim(str_replace('#!', '', $mailer_lines[0]));

            if ($correct_php == $current_php) {
               /* There is no crontab..  dont explain..  just say 'ok' */
               if ($frequency == '') {
                  $content .= '<p><img src="'.$ICON_PATH.'icon_ok.gif" />'.$script.'</p>';
               } else {
                  $content .= '<p><img src="'.$ICON_PATH.'icon_ok.gif" />'.
                    str_replace(array('###SCRIPT###', '###FREQUENCY###'),
                                array($script, $frequency),
                                $LANG->getLL('crontab_explain')).'</p>';
               }
            } else {
               $script_errors = 1;
               $content .= '<p><img src="'.$ICON_PATH.'icon_fatalerror.gif" />'.
                    str_replace('###SCRIPT###', $script, $LANG->getLL('crontab_bad_php_path')).'</p>';
            }
         }
      } else {
         $content .= '<p><img src="'.$ICON_PATH.'icon_fatalerror.gif" />'.
               str_replace('###PHP_PATH###', $correct_php, $LANG->getLL('php_not_found'));
      }

      /* If any errors, show the correct button */
      if ($script_errors) {
         $content .= '<form>';
         $content .= '<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />';
         $content .= '<p><input type="submit" name="try_correct_path" value="'.$LANG->getLL('try_correct_path').'" /></p>';
         $content .= '</form>';
      }

      /********************** Helper programs *************************/
      /* Check if we have a Lynx-executable */
      $content .= '<h3>'.$LANG->getLL('lynx_browser').'</h3>';
      if (file_exists($theConf['path_to_lynx'])) {
         $content .= '<p><img src="'.$ICON_PATH.'icon_ok.gif" />'.
                  str_replace('###LYNX_PATH###', $theConf['path_to_lynx'],
                  $LANG->getLL('lynx_found')).'</p>';
      } else {
         $content .= '<p><img src="'.$ICON_PATH.'icon_warning.gif" />'.
                  str_replace('###LYNX_PATH###', $theConf['path_to_lynx'],
                  $LANG->getLL('lynx_not_found')).'</p>';
      }

      /* Check if we have a Fetchmail-executable */
      $content .= '<h3>'.$LANG->getLL('fetchmail_program').'</h3>';
      if (file_exists($theConf['path_to_fetchmail'])) {
         $content .= '<p><img src="'.$ICON_PATH.'icon_ok.gif" />'.
                  str_replace('###FETCHMAIL_PATH###', $theConf['path_to_fetchmail'],
                  $LANG->getLL('fetchmail_found')).'</p>';
      } else {
         $content .= '<p><img src="'.$ICON_PATH.'icon_warning.gif" />'.
                  str_replace('###FETCHMAIL_PATH###', $theConf['path_to_fetchmail'],
                  $LANG->getLL('fetchmail_not_found')).'</p>';
      }

      /* Any other checks provided externally */
      if (is_array($TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['viewSysStatusHook'])) {
         foreach ($TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['viewSysStatusHook'] as $_classRef) {
            $_procObj = & t3lib_div::getUserObj($_classRef);
            $content = $_procObj->viewSysStatusHook($content, $LANG, $this);
         }
      }

      return $content;
   }

}

?>