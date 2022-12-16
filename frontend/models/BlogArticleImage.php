<?php

namespace frontend\models;

use himiklab\thumbnail\EasyThumbnailImage;
use Yii;

/**
 * This is the model class for table "oc_newsblog_article_image".
 */
class BlogArticleImage extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_newsblog_article_image';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['article_id', 'sort_order'], 'integer'],
      [['image'], 'string'],
    ];
  }

  public static function getArticleImages(int $article_id, string $image_type = 'thumbs_blog')
  {
    $article_images = self::find()
      ->select([
        'id',
        'image',
      ])
      ->where(['article_id' => (int)$article_id])
      ->orderBy(['sort_order' => SORT_ASC])
      ->asArray()
      ->all();

    foreach ($article_images as &$article_image) {
      if ($image_type == 'thumbs_article') {
        $article_image['thumb_big'] = EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($article_image['image'] ? $article_image['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_big_width'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_big_height'),
          EasyThumbnailImage::THUMBNAIL_OUTBOUND
        );
        $article_image['thumb_gallery'] = EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($article_image['image'] ? $article_image['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_gallery_width'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_gallery_height'),
          EasyThumbnailImage::THUMBNAIL_OUTBOUND
        );
      } else {
        $article_image['thumb'] = EasyThumbnailImage::thumbnailFileUrl(
          '@root' . ($article_image['image'] ? $article_image['image'] : '/upload/img/banners/no_image.png'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_width'),
          (int)Yii::$app->shopConfig->getParam($image_type . '_height'),
          EasyThumbnailImage::THUMBNAIL_OUTBOUND
        );
      }
    }

    return $article_images;
  }
}
