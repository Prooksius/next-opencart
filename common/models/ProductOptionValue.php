<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_product_option_value".
 */
class ProductOptionValue extends \yii\db\ActiveRecord
{
  
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_option_value';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_option_id', 'product_id', 'option_id', 'option_value_id', 'quantity', 'subtract', 'points'], 'integer'],
      [['price', 'weight'], 'number'],
      [['image', 'price_prefix', 'points_prefix', 'weight_prefix'], 'string'],
    ];
  }

  public function getActionsList()
  {
    return ['+' => '+', '-' => '-'];
  }
}
