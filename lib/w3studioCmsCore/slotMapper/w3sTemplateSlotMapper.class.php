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
 * w3sTemplateEngineSlotMapper renders the template, displaying the slots and 
 * make them choosable to be combined to build a bridge between templates. In this 
 * way contents can be ported from a template to a new one.
 *
 * @package    sfW3studioCMSPlugin
 * @subpackage w3sTemplateEngineSlotMapper
 * @author     Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 */
class w3sTemplateEngineSlotMapper extends w3sTemplateEngine
{

  /**
   * Overrides the standard w3sTemplateEngine constructor to render only the
   * a template without contents.
   * 
   * @param The id of the template to render
   * 
   */
  public function __construct($idTemplate)
  {
    
    $this->idTemplate = $idTemplate;
    $template = DbFinder::from('W3sTemplate')->
                          with('W3sProject')->
                          findPK($idTemplate);
	  $this->templateName = $template->getTemplateName();
	  $this->projectName = $template->getW3sProject()->getProjectName();
    
    $this->pageContents = w3sCommonFunctions::readFileContents(self::getTemplateFile($this->projectName, $this->templateName));
  }

  /*
   * Overrides the standard renderPage method
   * 
   */
  public function renderPage()
  {
    $slots = DbFinder::from('W3sSlot')->
                      where('TemplateId', $this->idTemplate)->
                      find(); 
    foreach ($slots as $slot){
      $contents = $this->drawSlot($slot);
      $this->pageContents = preg_replace('/\<\?php.*?include_slot\(\'' . $slot->getSlotName() . '\'\).*?\?\>/', $contents, $this->pageContents);
    }

    // Renders the W3StudioCMS Copyright button. Please do not remove. See the function to
    // learn the best way to implement it in your web site. Thank you
    $this->pageContents = $this->renderCopyright($this->pageContents);

    return $this->pageContents;
  }

  /** 
   * Draws the slot.
   * 
   * @param object   A slot object
   * 
   * @return string  A clickable div element that represents the slot.
   * 
   */
  public function drawSlot($slot)
  {
    
    // Checks if a map exists for the current slot
    $slotMapper = DbFinder::from('W3sSlotMapper')->
                      where('SlotIdSource', $slot->getId())->
                      findOne();
    if ($slotMapper == null)
    {
      $slotMapper = DbFinder::from('W3sSlotMapper')->
                      where('SlotIdDestination', $slot->getId())->
                      findOne();
    }

    // Colors the slot according if is has already been mapped
    $class = ($slotMapper == null) ? 'slotNotSelected' : 'slotSelected';
    
    return sprintf('<a href="#" onclick="W3sSlotMapper.selectSlot(%s, \'%s\')"><div id="%s" class="%s">%s</div></a>', $slot->getId(), $slot->getSlotName(), 'w3sSlotItem_' . $slot->getId(), $class, $slot->getSlotName());
  }
}