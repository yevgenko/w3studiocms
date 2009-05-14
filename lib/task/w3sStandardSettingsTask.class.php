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

/**
 * Loads the standard settings.yml file used by W3studioCMS
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sStandardSettingsTask
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 * @
 */
class w3sStandardSettingsTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'w3s';
    $this->name = 'standard-settings';

    $this->briefDescription = 'Loads the standard settings for W3studioCMS';

    $this->detailedDescription = <<<EOF
The [w3s:standard-settings|INFO] task will load the standard settings for W3studioCMS.

  [./symfony w3s:standard-settings|INFO]

EOF;
    $this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $sfAppConfigDir = sprintf("%2\$s%1\$s%3\$s%1\$sconfig%1\$ssettings.yml", DIRECTORY_SEPARATOR, sfConfig::get('sf_apps_dir'), $arguments['application']); //sfConfig::get('sf_apps_dir') . DIRECTORY_SEPARATOR . $arguments['application'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.yml';
    if (!is_file($sfAppConfigDir . '.OLD'))
    {
      rename($sfAppConfigDir, $sfAppConfigDir . '.OLD');
      copy(sprintf("%2\$s%1\$sw3sCmsPlugin%1\$sextra%1\$ssettings%1\$ssettings.yml", DIRECTORY_SEPARATOR, sfConfig::get('sf_plugins_dir')), $sfAppConfigDir);
      $this->logSection('W3studioCMS', 'Standard settings file has been installed');

      $cc = new sfCacheClearTask($this->dispatcher, $this->formatter);
      $cc->setCommandApplication($this->commandApplication);
      $ret = $cc->run();

      if ($ret)
      {
        return $ret;
      }
      
      $this->logSection('W3studioCMS', 'Cache cleared to let work the new settings.');
    }
    else
    {
      $this->logSection('W3studioCMS', 'Standard settings file exists. Skipped');
    }

    
  }
}
