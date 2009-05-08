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

class w3sInstallForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(      
      'host' => new sfWidgetFormInput(array(), array('value' => 'localhost')),
      'database' => new sfWidgetFormInput(),
      'username' => new sfWidgetFormInput(),
      'password' => new sfWidgetFormInput(array('type' => 'password')),
      'port' => new sfWidgetFormInput(array(), array('value' => '3306')),
      'createDb' => new sfWidgetFormInputCheckbox(),
      'ss' => new sfWidgetFormInputCheckbox(array(), array('checked' => true)),
    ));

    $this->setValidators(array(
      'host' => new sfValidatorString(),
      'database' => new sfValidatorString(),
      'username' => new sfValidatorString(),
      'password' => new sfValidatorString(),
      'port' => new sfValidatorString(),
      'createDb' => new sfValidatorString(array('required' => false)),
      'ss' => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setLabels(array(
      'createDb' => 'Create database',
      'ss' => 'With standard settings*',
    ));

    $this->widgetSchema->setNameFormat('install[%s]');
  }
}
