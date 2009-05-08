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
 * Builds the database model and loads the data
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sBuildDatabaseTask
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 * @
 */
class w3sBuildDatabaseTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'w3s';
    $this->name = 'build-database';

    $this->briefDescription = 'Builds the database for W3studioCMSPlugin';

    $this->detailedDescription = <<<EOF
The [w3s:build-database|INFO] task will build the model, will create the database and will load
the data into the db too, according with databases.yml file.

  [./symfony w3s:build-database|INFO]

EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $cc = new sfCacheClearTask($this->dispatcher, $this->formatter);
    $cc->setCommandApplication($this->commandApplication);
    $ret = $cc->run();

    $buildModel = new sfPropelBuildAllTask($this->dispatcher, $this->formatter);
    $buildModel->setCommandApplication($this->commandApplication);
    $ret = $buildModel->run(array(), array('0' => 'no-confirmation'));
    
    $data = new sfPropelLoadDataTask($this->dispatcher, $this->formatter);
    $data->setCommandApplication($this->commandApplication);
    $ret = $data->run();

    $cc = new sfCacheClearTask($this->dispatcher, $this->formatter);
    $cc->setCommandApplication($this->commandApplication);
    $ret = $cc->run();
  }
}
