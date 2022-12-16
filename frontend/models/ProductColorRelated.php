<?php

namespace frontend\models;

use common\models\ProductColorRelated as ModelsProductColorRelated;
use himiklab\thumbnail\EasyThumbnailImage;
use Yii;

/**
 * This is the model class for table "oc_product_colors".
 */
class ProductColorRelated extends ModelsProductColorRelated
{

  public $color_image;
  public $product_image;
  public $product_slug;
  public $color_name;

  public static function getProductColors(int $product_id) 
  {
    $data = [];
    $colors = self::find()
      ->alias('pcl')
      ->select([
        'pcl.color_related_id',
        'cl.image AS color_image',
        'p.image AS product_image', 
        'p.alias AS product_slug', 
        'pcld.name AS color_name',
      ])
      ->leftJoin('oc_product p', 'p.id = pcl.color_related_id')
      ->leftJoin('oc_pcolor cl', 'cl.id = p.pcolor_id')
      ->leftJoin('oc_pcolor_description pcld', 'pcld.pcolor_id = p.pcolor_id AND pcld.language_id = "' . Yii::$app->language . '"')
      ->where(['pcl.product_id' => (int)$product_id, 'p.status' => 1])
      ->andWhere(['<=', 'p.date_available', time()])
      ->all();

    foreach ($colors as $color) {
      $product_thumb = EasyThumbnailImage::thumbnailFileUrl(
        '@root' . ($color->product_image ? $color->product_image : '/upload/img/banners/no_image.png'),
        (int)Yii::$app->shopConfig->getParam('thumbs_cart_width'),
        (int)Yii::$app->shopConfig->getParam('thumbs_cart_height'),
        EasyThumbnailImage::THUMBNAIL_INSET
      );

      $data[] = [
        'product_id' => $color->color_related_id,
        'product_slug' => $color->product_slug,
        'product_image' => $color->product_image,
        'product_thumb' => $product_thumb,
        'color_image' => $color->color_image,
        'color_name' => $color->color_name,
      ];
    }
    
    return $data;
  }
}
