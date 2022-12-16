<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_order_option".
 */
class OrderOption extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order_option';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['order_product_id', 'order_id', 'product_option_id', 'quantity'], 'required'],
      [['order_id', 'order_product_id', 'product_option_id', 'product_option_value_id'], 'integer'],
      [['name'], 'string', 'max' => 255],
      [['value'], 'string'],
      [['type'], 'string', 'max' => 32],
      [['product_option_value_id'], 'default', 'value' => 0],
    ];
  }

}
