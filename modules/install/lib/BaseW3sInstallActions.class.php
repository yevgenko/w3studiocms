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

class BaseW3sInstallActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->prerequisites = $this->prerequisites();
    if($this->checkPrerequisites($prerequisites)) $this->redirect('install/install');
  }

  public function executeInstall(sfWebRequest $request)
  {
    $canInstall = true;
    $prerequisites = $this->prerequisites();
    
    if (!$this->checkPrerequisites($prerequisites)) $this->redirect('install/index');

    $this->form = new w3sInstallForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('install'));

      if ($this->form->isValid())
      {
        $response = '';
        if(ini_get('max_execution_time') < 300) ini_set('max_execution_time', 300);
        $values = $this->form->getValues();
        $createDb = ($values['createDb'] == 'on') ? true : false;
        $dbConnection = $this->testDbConnection($values, $createDb);

        if($dbConnection == 1)
        {
          $symfony = sprintf('php %s%ssymfony ', sfConfig::get('sf_root_dir'), DIRECTORY_SEPARATOR);
          
          try
          {
            $cli = new sfFilesystem();
            $command = sprintf('w3s:configure-database --no-confirmation --database=%s --host=%s %s %s', $values["database"], $values["host"], $values["username"], $values["password"]);
            $response = $cli->sh($symfony . $command);

            $ss = ($values['ss'] == 'on') ? '--with-standard-settings ' : '';
            $command = 'w3s:setup --web-mode ' . $ss .sfConfig::get('sf_app');
            $response .= $cli->sh($symfony . $command);
            $result = (strpos($response, 'Warning:') === false) ? 1 : 4;
          }
          catch(Exception $e)
          {
            echo '<pre>' . $e->getMessage() . '</pre>';
            $result = 4;
          }
        }
        else
        {
          $result = $dbConnection ;
        }

        if ($result == 1)
        {
          return  $this->redirect('webSite/installComplete');
        }
        else
        {
          $this->result = $result;
          $this->responseMessage = $response;
          return  sfView::ERROR;
        }
      }
    }
  }

  private function checkPrerequisites($prerequisites)
  {
    $result = true;
    foreach($prerequisites as $value)
    {
      if (!$value[1])
      {
        $result = false;
        break;
      }
    }

    return $result;
  }


  private function prerequisites()
  {
    $plugins = scandir(sfConfig::get('sf_plugins_dir'));
    $prerequisites = array();
    $messages = array('Plugin installed', 'Plugin not installed. Fix it installing the latest version');
    $prerequisites[] = array('Plugin DbFinder', (in_array('DbFinderPlugin', $plugins)) ? true : false, $messages);
    $prerequisites[] = array('Plugin sfGuardPlugin', (in_array('sfGuardPlugin', $plugins)) ? true : false, $messages);
    $messages = array('Item writable', 'Item not writable. Fix it changing its permissions to writable');
    $prerequisites[] = array(sfConfig::get('sf_root_dir'), w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_config_dir'), w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_config_dir')  . DIRECTORY_SEPARATOR . 'database.yml', w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_config_dir')  . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_config_dir')  . DIRECTORY_SEPARATOR . 'propel.ini', w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_config_dir')  . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_cache_dir'), w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_log_dir'), w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_log_dir') . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_lib_dir'), w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $prerequisites[] = array(sfConfig::get('sf_data_dir'), w3sCommonFunctions::checkFolderWritable(sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $w3sFolder = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfW3studioCmsPlugin' . DIRECTORY_SEPARATOR;
    $prerequisites[] = array($w3sFolder . 'config', w3sCommonFunctions::checkFolderWritable($w3sFolder . 'config' . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);
    $sfGuardFolder = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . 'sfGuardPlugin' . DIRECTORY_SEPARATOR;
    $prerequisites[] = array($sfGuardFolder . 'config', w3sCommonFunctions::checkFolderWritable($sfGuardFolder . 'config' . DIRECTORY_SEPARATOR . 'w3stmp') ? true : false, $messages);

    return $prerequisites;
  }

  private function testDbConnection($values, $createDb = false)
  {
    if ($conn = @mysql_connect($values["host"], $values["username"], $values["password"]))
    {
      if (!mysql_select_db($values["database"], $conn) && $createDb)
      {
        $sql = 'CREATE DATABASE ' . $values["database"];
        mysql_query($sql, $conn);
      }

      if (mysql_select_db($values["database"], $conn))
      {
        $conn = null;
        return 1;
      }
      else
      {
        return 2;
      }
    }
    else
    {
      return 0;
    }
  }
}

