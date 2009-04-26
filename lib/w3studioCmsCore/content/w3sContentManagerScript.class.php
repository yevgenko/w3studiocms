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
 * w3sContentManagerScript extends the w3sContentManager to represent a script
 * content.
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sContentManagerScript
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 */
  
class w3sContentManagerScript extends w3sContentManager{
 
  /**
   * Returns the default text for the text content
   *   
   * @return string
   *
   */ 
  public function getDefaultText()
  {
  	return 'Insert here your script';           
  }
    
  /**
   * Returns the content formatted to be correctly displayed in the editor mode.
   *   
   * @return string
   *
   */ 
  public function getDisplayContentForEditorMode()
  {
  	return sprintf('<img src="%s" %s />', sfConfig::get('app_w3s_web_skin_images_dir') . '/structural/w3s_script.png', 'width="45" height="14"');           
  }
  
  /**
   * Returns the content formatted to be correctly displayed in the preview mode.
   *   
   * @return string
   *
   */ 
  public function getDisplayContentForPreviewMode()
  {
  	$content = stripslashes($this->content->getContent());
    if (strpos($content, '<?php') !== false){
      $file = sprintf('%s/tmpslot_%sphp', sfConfig::get('app_w3s_web_themes_dir'), sfContext::getInstance()->getUser()->getGuardUser()->getId());
      $fp = fopen ($file, "w");
      fwrite($fp, $content);
      fclose($fp);
      
      ob_start();
      include($file);
      $content = ob_get_clean();
      unlink($file);
    }     
    
    return $content;      
  }

 /**
  * Strip slashes before publishing content.
  *
  * @see w3sContentManager::getDisplayContentForPublishMode()
  */
  public function getDisplayContentForPublishMode()
  {
    return stripslashes(parent::getDisplayContentForPublishMode());
  }

 /**
  * Add backslash character (\) before every character that is among these:
  *
  *   . \ + * ? [ ^ ] ( $ )
  *
  * @see w3sContentManager::formatContent()
  */
  protected function formatContent($contentValues)
  {
    $contentValues['Content'] = quotemeta($contentValues['Content']);

    return $contentValues;
  }
}
