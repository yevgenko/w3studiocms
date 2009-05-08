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

class w3sRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    $modules = sfConfig::get('sf_enabled_modules');
    
    // prepend our routes
    if (class_exists('sfRoute')) // Symfony 1.1 compatibility. Checks for sfRoute because it is not implemented in 1.1
    {
     
      if (in_array('webEditor', $modules))
      {
        $r->prependRoute('site', new sfRoute('/:lang/:page.html', array('module' => 'webSite', 'action' => 'index')));
        $r->prependRoute('editor', new sfRoute('/:W3StudioCMS/:lang/:page.html', array('module' => 'webEditor', 'action' => 'index')));
        $r->prependRoute('homepage:', new sfRoute('/', array('module' => 'webSite', 'action' => 'index')));
      }
      else
      {
        self::enableInstallModule($r);
      }
    }
    else
    {
      if (in_array('webEditor', $modules))
      {
        $r->prependRoute('site', '/:lang/:page.html', array('module' => 'webSite', 'action' => 'index'));
        $r->prependRoute('editor', '/:W3StudioCMS/:lang/:page.html', array('module' => 'webEditor', 'action' => 'index'));
        $r->prependRoute('homepage:', '/', array('module' => 'webSite', 'action' => 'index'));
      }
      else
      {
        self::enableInstallModule($r);
      }
    }
  }

  static protected function enableInstallModule($event)
  {
    // Forces symfony to believe that the install module is already installed
    sfConfig::set('sf_enabled_modules', array('default', 'install'));

    // Enables the install module
    $settingsFile = sfConfig::get('sf_app_config_dir') . DIRECTORY_SEPARATOR . 'settings.yml';
    if (!file_exists($settingsFile . '.TMP'))
    {
      copy($settingsFile, $settingsFile . '.TMP');
      $contents = w3sCommonFunctions::readFileContents($settingsFile);
      $contents .= "\nall:\n";
      $contents .= "  .settings:\n";
      $contents .= "    enabled_modules:        [default, install]\n";
      if (count($contents) > 0)
      {
        if (!w3sCommonFunctions::writeFileContents($settingsFile, $contents))
        {
          rename($settingsFile . '.TMP', $settingsFile);
          throw new RuntimeException('Cannot write settings.yml file. Probably you don\'t have the permissions on this file. Execute as root.');
        }
      }
    }

    if (class_exists('sfRoute'))
    {
      $event->prependRoute('homepage:', new sfRoute('/', array('module' => 'install', 'action' => 'index')));
    }
    else
    {
      $event->prependRoute('homepage:', '/', array('module' => 'install', 'action' => 'index'));
    }

    try
    {
      $cli = new sfFilesystem();
      $response = $cli->sh(sprintf('php %s%ssymfony cc', sfConfig::get('sf_root_dir'), DIRECTORY_SEPARATOR));
    }
    catch(Exception $e)
    {
      $result = 4;
    }
  }
}