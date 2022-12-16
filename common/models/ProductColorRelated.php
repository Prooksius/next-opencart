<?php

namespace common\models;

use Yii;

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
      [['product_id', 'color_related_id'], 'integer'],
    ];
  }
}
