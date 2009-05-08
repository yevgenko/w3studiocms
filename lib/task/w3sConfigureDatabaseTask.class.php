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
 * Configures the databases.yml and propel.ini files
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sConfigureDatabaseTask
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 * @
 */
class w3sConfigureDatabaseTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    
    $this->addOptions(array(
      new sfCommandOption('database', null, sfCommandOption::PARAMETER_REQUIRED, 'mySql Database name', 'w3studiocms'),
      new sfCommandOption('host', null, sfCommandOption::PARAMETER_REQUIRED, 'mySql Host name', 'localhost'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Reconfigures without asking when a previous configuration exists'),
    ));

    $this->namespace = 'w3s';
    $this->name = 'configure-database';

    $this->briefDescription = 'Configures database.yml and propel.ini files.';

    $this->detailedDescription = <<<EOF
The [w3s:configure-database|INFO] task will configure database.yml and propel.ini files according with
data provided. Original files are renamed.

  [./symfony w3s:configure-database|INFO]

EOF;

    $this->addArgument('username', sfCommandArgument::REQUIRED, 'mySql User');
    $this->addArgument('password', sfCommandArgument::REQUIRED, 'mySql User\'s password');
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $result = true;
    if ($result && !@chmod(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml', 0777)) $result = false;
    if ($result && !@chmod(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini', 0777)) $result = false;
    if (!$result)
    {
      $this->logSection('Warning', 'Cannot change permissions to databases files.');
      //throw new RuntimeException('You don\'t have the permissions to use the installation folder. Execute as root');
    }
    
    if (file_exists(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml.OLD'))
    {
      if (!$options['no-confirmation'] && !$this->askConfirmation(array('I noticed you have already runned this task.', 'Do you want to reconfigure the database access? (y/N)'), null, false))
      {
        $this->logSection('W3studioCMS', 'Task aborted.');

        return 1;
      }
      else
      {
        unlink(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml');
        rename(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml.OLD', sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml');

        if (file_exists(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini.OLD'))
        { 
          unlink(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini');
          rename(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini.OLD', sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini');
        }
      }
    }

    $dbFile = "dev:\n";
    $dbFile .= "  propel:\n";
    $dbFile .= "    param:\n";
    $dbFile .= "      classname:  DebugPDO\n";
    $dbFile .= "\n";
    $dbFile .= "test:\n";
    $dbFile .= "  propel:\n";
    $dbFile .= "    param:\n";
    $dbFile .= "      classname:  DebugPDO\n";
    $dbFile .= "\n";
    $dbFile .= "all:\n";
    $dbFile .= "  propel:\n";
    $dbFile .= "    class:        sfPropelDatabase\n";
    $dbFile .= "    param:\n";
    $dbFile .= "      classname:  PropelPDO\n";
    $dbFile .= sprintf("      dsn:        mysql:dbname=%s;host=%s\n", $options["database"], $options["host"]);
    $dbFile .= sprintf("      username:   %s\n", $arguments["username"]);
    $dbFile .= sprintf("      password:   %s\n", $arguments["password"]);
    $dbFile .= "      encoding:   utf8\n";
    $dbFile .= "      persistent: true\n";
    $dbFile .= "      pooling:    true\n";

    if (!@rename(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml', sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml.OLD'))
    {
      throw new RuntimeException('Cannot rename databases.yml file. Probably you don\'t have the permissions on this file. Execute as root.');
    }
    
    if (!w3sCommonFunctions::writeFileContents(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'databases.yml', $dbFile))
    {
      throw new RuntimeException('Cannot write databases.yml file. Probably you don\'t have the permissions on this file. Execute as root.');
    }

    copy(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini', sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini.OLD');
    $propelIni = w3sCommonFunctions::readFileContents(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini');
    $propecolIni = preg_replace('/propel.database.url        = mysql:dbname=.*?;host=localhost/', sprintf('propel.database.url        = mysql:dbname=%s;host=%s', $options["database"], $options["host"]), $propelIni);
    $propelIni = str_replace('propel.database.user       = root', 'propel.database.user       = ' . $arguments["username"], $propelIni);
    $propelIni = str_replace('propel.database.password   = ', 'propel.database.password   = ' . $arguments["password"], $propelIni);
    if (count($propelIni) > 0)
    {
      if (!w3sCommonFunctions::writeFileContents(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini', $propelIni))
      {
        rename(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini.OLD', sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'propel.ini');
        throw new RuntimeException('Cannot write propel.ini file. Probably you don\'t have the permissions on this file. Execute as root.');
      }
    }
    
    $this->logSection('W3studioCMS', 'Database access correctly configured.');
  }
}
