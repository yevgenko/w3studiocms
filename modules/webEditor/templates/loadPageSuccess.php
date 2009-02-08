<?php
/*
 * This file is part of the w3studioCMS package library and it is distributed 
 * under the LGPL LICENSE Version 2.1. To use this library you must leave 
 * intact this copyright notice.
 *  
 * (c) 2007-2008 Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.w3studiocms.com
 */

  use_helper('I18N', 'Javascript'); 
  
  switch ($status)
  {
  	case 1:
		  echo $template->renderPage();
		  echo javascript_tag(sprintf('%s;InteractiveMenu = null; InteractiveMenu = new interactiveMenu(\'%s\');InteractiveMenu.setEvents();', $template->getSortables(), $template->getInteractiveMenuEvents())); 
  		break;
	  case 2:
      $message = __('Your session has been expired: you must login again.');      
      echo w3sCommonFunctions::displayMessage($message);      
      break;
    case 4:
      $message = __('The page you have requested is in use. Please open the structure menu and choose another one from the File Manager tab.');      
      echo w3sCommonFunctions::displayMessage($message);      
      break;
  }