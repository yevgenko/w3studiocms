<?php
  use_helper('I18N', 'Javascript');
  echo $content->redraw(ESC_RAW);
  echo javascript_tag($content->getSortables(ESC_RAW) . $content->getInteractiveMenuEvents(ESC_RAW));
