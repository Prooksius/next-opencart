<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_product_option".
 */
class ProductOption extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_product_option';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['product_id', 'option_id', 'required'], 'integer'],
      [['value'], 'string'],
    ];
  }
}
