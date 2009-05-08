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
 * Installs W3studioCMS
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sSetupTask
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 * @
 */
class w3sSetupTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('web-mode', null, sfCommandOption::PARAMETER_NONE, 'Skips certain controls already made that may fail when in web-mode'),
      new sfCommandOption('with-standard-settings', null, sfCommandOption::PARAMETER_NONE, 'Forces to copy the standard settings.yml file used by W3studioCMS'),
    ));

    $this->namespace = 'w3s';
    $this->name = 'setup';

    $this->briefDescription = 'Builds the database for W3studioCMSPlugin';

    $this->detailedDescription = <<<EOF
The [w3s:build-database|INFO] task will build the model, will create the database and will load
the data into the db too, according with databases.yml file.

  [./symfony w3s:build-database|INFO]

EOF;

    $this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!$options['web-mode'])
    {
      $result = true;
      if ($result && !@chmod(sfConfig::get('sf_root_dir'), 0777)) $result = false;
      if ($result && !@chmod(sfConfig::get('sf_config_dir'), 0777)) $result = false;
      if ($result && !@chmod(sfConfig::get('sf_cache_dir'), 0777)) $result = false;
      if ($result && !@chmod(sfConfig::get('sf_log_dir'), 0777)) $result = false;
      if ($result && !@chmod(sfConfig::get('sf_lib_dir'), 0777)) $result = false;
      if ($result && !@chmod(sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfW3studioCmsPlugin' . DIRECTORY_SEPARATOR . 'config', 0777)) $result = false;
      if ($result && !@chmod(sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfGuardPlugin' . DIRECTORY_SEPARATOR . 'config', 0777)) $result = false;
      if (!$result)
      {
        throw new RuntimeException('You don\'t have the permissions to use the installation folder. Execute as root');
      }
    }
    else
    {
      $tmpSettings = sprintf("%2\$s%1\$s%3\$s%1\$sconfig%1\$ssettings.yml.TMP", DIRECTORY_SEPARATOR, sfConfig::get('sf_apps_dir'), $arguments['application']);
      if (file_exists($tmpSettings))
      {
        unlink($tmpSettings);
      }
    }
    
    $task = new w3sConfigureEnviromentTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $ret = $task->run(array('application' => $arguments['application']));
    if ($ret)
    {
      return $ret;
    }

    $task = new sfPluginPublishAssetsTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $ret = $task->run();
    if ($ret)
    {
      return $ret;
    }

    $task = new w3sPublishAssetsTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $ret = $task->run();
    if ($ret)
    {
      return $ret;
    }

    $task = new w3sBuildDatabaseTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $ret = $task->run();
    if ($ret)
    {
      return $ret;
    }

    if ($options['with-standard-settings'])
    {
      $task = new w3sStandardSettingsTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $ret = $task->run(array('application' => $arguments['application']));
      if ($ret)
      {
        return $ret;
      }
    }
    else
    {
      $this->logSection('W3studioCMS', 'You have to enable all the modules used by W3studioCMS. See /plugins/sfW3studioCmsPlugin/extra/settings/settings.yml file.');
    }

    $this->logSection('W3studioCMS', 'W3studioCMS has been configured and it\'s now ready to be used');
   
  }
}
