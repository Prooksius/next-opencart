<?php

namespace frontend\extensions\modules;

use Yii;

class HtmlContent
{

  public static function getContent($settings, $params = null)
  {
    return $settings['html'][Yii::$app->language];
  }
}