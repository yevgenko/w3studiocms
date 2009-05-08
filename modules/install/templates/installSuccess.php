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
  echo sprintf('<h1>%s</h1>', __('Welcome to W3studioCMS installation procedure'));
  echo sprintf('<p>%s</p>', __('Fill the form below with the data required to access your database.'));
 
  include_partial('renderForm', array('form' => $form));
  echo sprintf('<p>%s</p>', __('Notice that the installation process will take some time. Please don\'t stop the procedure while is working'));
  echo sprintf('<p>%s</p>', __('* This options lets W3studioCMS to use its standard settings. If you are not familiar with symfony or you are installing a fresh symfony\'s project, leave this option checked, otherwise your installation might not work properly.'));