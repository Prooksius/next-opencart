<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_order_total".
 */
class OrderTotal extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order_total';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['order_id', 'code', 'title'], 'required'],
      [['order_id', 'sort_order'], 'integer'],
      [['title'], 'string', 'max' => 255],
      [['code'], 'string', 'max' => 64],
      [['value'], 'number'],
      [['value'], 'default', 'value' => 0],
    ];
  }

}
