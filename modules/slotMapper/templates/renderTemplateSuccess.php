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
  
  /**/
  switch ($status)
  {
  	case 1:
		  echo $template->renderPage(ESC_RAW);
  		break;
	  case 2:
      $message = __('The template cannot be rendered because a required parameter misses.');
      echo w3sCommonFunctions::displayMessage($message);      
      break;
    case 4:
      $message = __('Your session has been expired: you must login again.');
      echo w3sCommonFunctions::displayMessage($message);      
      break;
  }