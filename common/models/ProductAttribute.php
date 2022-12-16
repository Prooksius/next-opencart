<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_product_image".
 */
class ProductAttribute extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_attribute';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'attribute_id'], 'integer'],
      [['alias', 'language_id', 'text'], 'string'],
    ];
  }
}
