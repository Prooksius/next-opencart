<?php

namespace frontend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "faq".
 */
class Cart extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_cart';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['session_id', 'product_id', 'quantity'], 'required'],
      [['customer_id', 'product_id', 'quantity'], 'integer'],
      [['session_id'], 'string', 'max' => 40],
      [['option'], 'string'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      TimestampBehavior::className(),
    ];
  }

}
