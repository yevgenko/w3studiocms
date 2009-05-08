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
  
  include_partial('styles');

  $canInstall = true;
  echo '<table>';
  echo sprintf('<tr><td colspan="2"><h1>%s</h1><h2>%s</h2></td></tr>', __('Welcome to W3studioCMS installation procedure'), __('Prerequisites'));
  foreach($prerequisites as $value)
  {
    if ($canInstall && !$value[1]) $canInstall = false;
    echo sprintf('<tr><td><b>%s</b></td><td>%s</td></tr>', $value[0], ($value[1]) ? sprintf('<span class="is_ok">%s</span>', $value[2][0]) : sprintf('<span class="is_not_ok">%s</span>', $value[2][1]));
  }
  if(!$canInstall)
  {
    echo sprintf('<tr><td colspan="2" height="30" valign="bottom"><h3>%s</h3></td></tr>', __('Warning: not all the prerequisites required to install W3studioCMS are satisfied. The installation cannot continue untill all the prerequisites are satisfied.'));
  }
  else
  {
    echo sprintf('<tr><td colspan="2" height="30" valign="bottom" align="center">%s</td></tr>', link_to(__('Start Install'), url_for('install/install')));
  }
  echo '</table>';
  