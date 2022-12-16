<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_product_special".
 */
class ProductSpecial extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_special';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'customer_group_id', 'priority', 'date_start', 'date_end'], 'integer'],
      [['price'], 'number'],
    ];
  }
}
