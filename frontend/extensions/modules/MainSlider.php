<?php

namespace frontend\extensions\modules;

use frontend\models\Banner;
use himiklab\thumbnail\EasyThumbnailImage;
use Yii;

class MainSlider
{

  public static function getContent($settings, $params = null)
  {
    $content = [
      'visible' => (int)$settings['visible'],
      'images' => [],
      'width' => (int)$settings['width'],
      'height' => (int)$settings['height']
    ];

    $images = Banner::getImages((int)$settings['banner_id']);

    foreach ($images as &$image) {

      $thumb = EasyThumbnailImage::thumbnailFileUrl(
        '@root' . ($image['image'] ? $image['image'] : '/upload/img/banners/no_image.png'),
        (int)$settings['width'],
        (int)$settings['height'],
        EasyThumbnailImage::THUMBNAIL_INSET
      );

      $image['thumb'] = $thumb;
      $content['images'][] = $image;
    }

    return $content;
  }
}