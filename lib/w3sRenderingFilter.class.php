<?php

/*
 * This file is part of the sfW3studioCms package library and it is distributed 
 * under the LGPL LICENSE Version 2.1. To use this library you must leave 
 * intact this copyright notice.
 *
 * (c) 2007-2009 Giansimon Diblas <giansimon.diblas@w3studiocms.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For extra documentation and help please visit http://www.w3studiocms.com
 */

/**
 * w3sRenderingFilter decorates sfResponse content with w3s template.
 *
 * This filter should be added to the application filters.yml file:
 *
 *    w3s_rendering:
 *      class: w3sRenderingFilter
 *
 * @package    sfW3studioCms
 * @subpackage plugin
 * @author     Yevgeniy Viktorov <wik@osmonitoring.com>
 * @version    SVN: $Id$
 */
class w3sRenderingFilter extends sfFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain The filter chain.
   */
  public function execute($filterChain)
  {
    // execute next filter
    $filterChain->execute();

    // check request type
    $action = $this->getContext()->getActionStack()->getLastEntry()->getActionInstance();
    if ($this->isFirstCall() && !$action->getRequest()->isXmlHttpRequest())
    {
      if ($w3s_module = DbFinder::from('W3sModule')->
        whereModuleName($this->getContext()->getModuleName())->
        findOne()
      )
      {
        $template = new w3sTemplateEngineFrontend('english', $w3s_module->getPageId());
        if ($template->getIdLanguage() != -1 && $template->getIdPage() != -1)
        {
          // Decorate the response with the page layout
          $response = $this->getContext()->getResponse();
          preg_match('/<body\b[^>]*>\s*(.*?)\s*<\/body>/s', $response->getContent(), $match);
          if (isset($match[1]))
          {
            $page = $template->renderPage();
            // check placeholder
            if (preg_match('/\<!-- w3s_content_start --\>/', $page, $r))
            {
              $this->handleStyles($template->retrieveTemplateStylesheets($template->getPageContents()));

              $content = preg_replace('/<!-- w3s_content_start(.*?)w3s_content_end --\>/s', $match[1], $page);
              $content = '<body>'.$content.'</body>';
              $content = preg_replace('/<body(.*?)\/body>/s', $content, $response->getContent());

              $response->setContent($content);
            }
          }
        }
      }
    }
  }

  /**
   * Adds stylesheets information in the sfResponse content.
   *
   * @param array $styles An array of stylesheets definitions
   */
  protected function handleStyles($styles)
  {
    $response = $this->context->getResponse();
    $content = $response->getContent();
    if (false !== ($pos = strpos($content, '</head>')))
    {
      $this->context->getConfiguration()->loadHelpers(array('Tag', 'Asset'));
      $html = '';
      $conditionalStylesheets = '';
      foreach($styles as $style)
      {
        if ($style['conditional'] == 0)
        {
          $media = ($style["media"] != "") ? array('media' => $style["media"]) : array();
          $html .= stylesheet_tag($style["href"], $media);
        }
        else
        {
          $html .= $style["href"];
        }
      }

      if ($html)
      {
        $response->setContent(substr($content, 0, $pos).$html.substr($content, $pos));
      }
    }
  }
}
