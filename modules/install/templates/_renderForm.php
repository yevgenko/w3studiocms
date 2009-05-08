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

  use_helper('I18N');
 
  echo '<form action="' . url_for('install/install') . '" method="post" >' .
       '<table>'
          . $form
          . sprintf('<tr><td></td><td><input type="submit" value="%s"/></td></tr>', __('Install W3studioCMS')) .
       '</table>' .
       '</form>';