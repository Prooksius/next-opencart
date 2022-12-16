<?php

namespace frontend\models;

use himiklab\thumbnail\EasyThumbnailImage;
use Yii;

/**
 * This is the model class for table "oc_banner".
 */
class Banner extends \yii\db\ActiveRecord
{
  
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_banner';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['status'], 'integer'],
      [['name'], 'string'],
    ];
  }

  /**
   * @return array
   */
  public static function getImages(int $banner_id)
  {
    $bannerImages = [];

    $images = BannerImage::find()
      ->alias('bi')
      ->select('bi.*')
      ->leftJoin('oc_banner b', 'b.id = bi.banner_id')
      ->where(['b.status' => 1, 'bi.banner_id' => $banner_id, 'language_id' => Yii::$app->language])
      ->orderBy(['sort_order' => SORT_ASC])
      ->all();

    foreach ($images as $image) {
      $bannerImages[] = [
        'id'    => $image->id,
        'title' => $image->title,
        'text1' => $image->text1,
        'text2' => $image->text2,
        'text3' => $image->text3,
        'image' => $image->image,
        'link'  => $image->link,
      ];
    }

    return $bannerImages;
  }
}
