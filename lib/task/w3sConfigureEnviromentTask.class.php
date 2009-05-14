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
 * Configures the enviroment, creating the symlinks to themes, published folders.
 * Installs the sfGuardPlugin and tiny-mce.
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sConfigureEnviromentTask
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 * @
 */
class w3sConfigureEnviromentTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'w3s';
    $this->name = 'configure-env';

    $this->briefDescription = 'Configures the enviroment for w3studioCMS plugin';

    $this->detailedDescription = <<<EOF
The [w3s:configure-env|INFO] task will configure the enviroment for w3studioCMS plugin.

  [./symfony w3s:configure-env|INFO]

EOF;
    $this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $filesystem = new sfFilesystem();
    $themesDir = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'themes';
    $publishedDir = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'published';
    $sfAppDir = sfConfig::get('sf_apps_dir') . DIRECTORY_SEPARATOR . $arguments['application'] . DIRECTORY_SEPARATOR;
    $sfGuardDir = $sfAppDir . 'modules' . DIRECTORY_SEPARATOR . 'sfGuardAuth';
    $w3sDir = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'w3sCmsPlugin' . DIRECTORY_SEPARATOR;
    $w3sExtraDir = $w3sDir . 'extra' . DIRECTORY_SEPARATOR;

    if (!is_dir($themesDir))
    {
      mkdir($themesDir);
      w3sCommonFunctions::copyDirectory($w3sExtraDir . 'themes' . DIRECTORY_SEPARATOR . 'w3sJet30Theme', $themesDir . DIRECTORY_SEPARATOR . 'w3sJet30Theme');

      w3sCommonFunctions::copyDirectory($w3sExtraDir . 'themes' . DIRECTORY_SEPARATOR . 'standardThemeImages', sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'assets');
      $this->logSection('W3studioCMS', 'Standard theme has been installed');
    }
    else
    {
      $this->logSection('Skipped', 'Themes folder exists.');
    }

    if (!is_dir($publishedDir)){
      mkdir($publishedDir);
      w3sCommonFunctions::copyDirectory($w3sExtraDir . 'published', $publishedDir);      
    }
    else
    {
      $this->logSection('Skipped', 'Published folder exists.');
    }


    $myUserFile = $sfAppDir . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'myUser.class.php';
    if (!is_file($myUserFile . '.OLD'))
    {
      $myUserFileContents = w3sCommonFunctions::readFileContents($myUserFile);
      rename($myUserFile, $myUserFile . '.OLD');
      $myUserFileContents = str_replace('sfBasicSecurityUser', 'sfGuardSecurityUser', $myUserFileContents);
      if (count($myUserFileContents) > 0) w3sCommonFunctions::writeFileContents($myUserFile, $myUserFileContents);
      $this->logSection('W3studioCMS', 'sfGuardSecurity configured.');
    }
    else
    {
      $this->logSection('Skipped', 'sfGuardSecurity already configured.');
    }

    if(!is_dir($sfGuardDir))
    {
      $filesystem->symlink($w3sExtraDir  . 'sfGuardAuth',
                           $sfGuardDir, true);
      $this->logSection('W3studioCMS', 'Modified sfGuardAuth module installed.');
    }
    else
    {
      $this->logSection('Skipped', 'sfGuardAuth already exists.');
    }

    if (is_dir($w3sExtraDir . 'tiny_mce'))
    {
      if (!is_dir(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'tiny_mce'))
      {
        $filesystem->symlink($w3sExtraDir . 'tiny_mce',
                             sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'tiny_mce', true);
        $this->logSection('W3studioCMS', 'TinyMCE webeditor has been installed.');
      }
      else
      {
        $this->logSection('Skipped', 'TinyMCE webeditor already installed.');
      }
    }
  }
}
