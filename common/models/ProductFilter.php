<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_product_filter".
 */
class ProductFilter extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
      return 'oc_product_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'filter_id'], 'integer'],
    ];
  }
}
