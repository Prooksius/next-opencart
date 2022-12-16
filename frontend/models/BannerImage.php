<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_banner_image".
 */
class BannerImage extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_banner_image';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['banner_id', 'sort_order'], 'integer'],
      [['language_id', 'title', 'text1', 'text2' , 'text3', 'link', 'image'], 'string'],
    ];
  }
}
