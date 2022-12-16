<?php

namespace app\models;

/**
 * This is the model class for table "oc_product_colors".
 */
class ProductColorRelated extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_colors';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'color_related_id'], 'unique'],
      [['product_id', 'color_related_id'], 'required'],
      [['product_id', 'color_related_id'], 'integer'],
    ];
  }
}
