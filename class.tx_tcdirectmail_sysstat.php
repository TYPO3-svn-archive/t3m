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
 * Module 'Directmail' for the 'tcdirectmail' extension.
 *
 * @author	Daniel Schledermann <daniel@typoconsult.dk>
 */


class tx_tcdirectmail_sysstat {




   function viewSysStatus () {
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


















// 	function viewSysStatusold () {
// 	    global $LANG;
// 	    global $TYPO3_DB;
// 	    global $TYPO3_CONF_VARS;
// // 	    global $ICON_PATH;
// 		$ICON_PATH = $GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('t3m').'gfx';
//
// 	    /* Check if we're in safemode. If we are, these checks are better done manually */
// 	    if( ini_get('safe_mode') ){
// 		return $LANG->getLL('system_status_safemode');
// 	    }
//
// 	    /* Check if the mailer.php is executable */
// 	    $content .= '<h4>'.$LANG->getLL('mailer_php').'</h4>';
// 	    //$mailer_php = dirname(dirname(__FILE__)).'/mailer.php';
// 	    $mailer_php = PATH_typo3conf.t3lib_extMgm::extRelPath('tcdirectmail').'/mailer.php';
// 	    $mailer_lines = file($mailer_php);
// 	    $current_php = trim(str_replace('#!', '', $mailer_lines[0]));
// 	    $new_php = trim($TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['pathToPHP']);
//
// 	    if ($_REQUEST['try_correct_path']) {
// 		/* Correct the sh-bang */
// 		$mailer_lines[0] = "#!$new_php\n";
// 		$fd = fopen($mailer_php, 'w');
// 		fwrite($fd, implode("", $mailer_lines));
// 		fclose ($fd);
//
// 		/* Reload file */
// 		$mailer_lines = file($mailer_php);
// 		$current_php = trim(str_replace('#!', '', $mailer_lines[0]));
// 	    }
//
//
// 	    if ($_REQUEST['try_make_exe']) {
//
// 		/* Set exe-bit */
// 		list(,,$mode) = stat($mailer_php);
// 		$mode |= 0110;
// 		chmod($mailer_php, $mode);
// 		clearstatcache();
// 	    }
//
// 	    exec("$mailer_php --test", $dummy, $return_status);
//
// 	    if (!$return_status && $new_php == $current_php) {
// 		$content .= '<p><img src="'.$ICON_PATH.'icon_ok.gif" />'.$LANG->getLL('mailer_exe_ok').'</p>';
// 		$content .= '<p>'.str_replace('###MAILERPATH###', $mailer_php, $LANG->getLL('crontab_explain')).'</p>';
//
// 	    } else {
// 		$content .= '<form>';
// 		$content .= '<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />';
//
//
// 		if ($new_php != $current_php) {
// 		    $content .= '<p><img src="'.$ICON_PATH.'icon_fatalerror.gif" />'.
// 									    str_replace('###CONFIGURED_PHP###', $new_php,
// 										str_replace('###CURRENT_PHP###', $current_php,
// 										$LANG->getLL('php_wrong_path'))).'</p>';
// 		    $content .= '<p><input type="submit" name="try_correct_path" value="'.$LANG->getLL('try_correct_path').'" /></p>';
// 		} else {
// 		    $content .= '<p><img src="'.$ICON_PATH.'icon_fatalerror.gif" />'.$LANG->getLL('mailer_exe_notok').'</p>';
// 		    if (!is_executable($current_php)) {
// 			$content .= '<p>'.$LANG->getLL('php_not_executable').'</p>';
// 		    } else {
// 			$content .= '<p><input type="submit" name="try_make_exe" value="'.$LANG->getLL('try_make_exe').'" /></p>';
// 		    }
// 		}
//
// 		$content .= '</form>';
// 	    }
//
//
//
//
//
// 	    /* Check if we have a Lynx-executable */
// 	    $content .= '<h4>'.$LANG->getLL('lynx_browser').'</h4>';
//
// 	    exec ($TYPO3_CONF_VARS['EXTCONF']['tcdirectmail']['pathToLynx'].' -version', $lynxoutput);
// 	    list ($lynxversion) = $lynxoutput;
//
// 	    if (preg_match ('|^Lynx|', $lynxversion)) {
// 		$content .= '<p><img src="'.$ICON_PATH.'icon_ok.gif" />'.str_replace('###LYNX_VERSION###', $lynxversion, $LANG->getLL('lynx_found')).'</p>';
// 	    } else {
// 		$content .= '<p><img src="'.$ICON_PATH.'icon_warning.gif" />'.
// 						str_replace('###LYNX_PATH###', $GLOBALS['TYPO3_CONF_VARS']['tcdirectmail']['pathToLynx'],
// 					    $LANG->getLL('lynx_not_found')).'</p>';
// 	    }
//
//
// 	    return $content;
// 	}

}

?>