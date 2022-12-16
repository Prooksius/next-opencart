<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_product_image".
 */
class ProductImage extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_image';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'sort_order'], 'integer'],
      [['image'], 'string'],
    ];
  }
}
