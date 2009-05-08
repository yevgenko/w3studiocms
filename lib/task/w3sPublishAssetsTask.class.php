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
 * Publishes Web Assets for third party themes
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage task
 * @author     Yevgeniy Viktorov <wik@osmonitoring.com>
 * @
 */
class w3sPublishAssetsTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('web_dir', null, sfCommandOption::PARAMETER_REQUIRED, 'path/to/web/dir', sfConfig::get('sf_web_dir')),
      new sfCommandOption('themes_dir', null, sfCommandOption::PARAMETER_REQUIRED, 'path/to/themes/dir', sfConfig::get('sf_themes_dir', sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'themes')),
    ));

    $this->namespace = 'w3s';
    $this->name = 'publish-assets';

    $this->briefDescription = 'Publishes web assets for all themes';

    $this->detailedDescription = <<<EOF
The [w3s:publish-assets|INFO] task will publish web assets from all themes.

  [./symfony w3s:publish-assets|INFO]

EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $themeImport = new w3sThemeImport();
    $events = $themeImport->publishAssets($arguments, $options);

    foreach($events as $event)
    {
      $this->logSection('W3studioCMS', $event);
      //$this->dispatcher->notify(new sfEvent($this, 'application.log', array($event)));
    }
  }
}
