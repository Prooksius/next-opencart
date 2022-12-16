<?php

namespace frontend\models;

use himiklab\thumbnail\EasyThumbnailImage;
use Yii;
use common\models\ProductImage as ModelsProductImage;

/**
 * This is the model class for table "oc_product_image".
 */
class ProductImage extends ModelsProductImage
{
  
  public static function getProductImages($product_id, $image_type = 'thumbs_catalog')
  {
    $product_images = self::find()
      ->select([
        'id',
        'image',
        'sort_order'
      ])
      ->where(['product_id' => (int)$product_id])
      ->orderBy(['sort_order' => SORT_ASC])
      ->asArray()
      ->all();

    foreach ($product_images as &$product_image) {
      if ($image_type == 'thumbs_product') {
        $product_image['thumb_big'] = EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($product_image['image'] ? $product_image['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_big_width'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_big_height'),
          EasyThumbnailImage::THUMBNAIL_INSET_BOX
        );
        $product_image['thumb_gallery'] = EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($product_image['image'] ? $product_image['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_gallery_width'),
          (int)Yii::$app->shopConfig->getParam('thumbs_product_gallery_height'),
          EasyThumbnailImage::THUMBNAIL_INSET_BOX
        );
      } else {
        $product_image['thumb'] = EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($product_image['image'] ? $product_image['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_width'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_height'),
          EasyThumbnailImage::THUMBNAIL_INSET
        );
      }
    }

    return $product_images;
  }
}
