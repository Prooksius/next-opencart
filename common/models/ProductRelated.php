<?php

namespace common\models;

/**
 * This is the model class for table "oc_product_related".
 */
class ProductRelated extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_related';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'related_id'], 'integer'],
    ];
  }
}
