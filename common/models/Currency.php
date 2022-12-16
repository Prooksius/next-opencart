<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "language".
 */
class Currency extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_currency';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['title', 'code'], 'required'],
      [['title'], 'string', 'max' => 32],
      [['code'], 'string', 'max' => 3],
      [['decimal_place'], 'string', 'max' => 1],
      [['value'], 'number'],
      [['status'], 'integer'],
      [['symbol_left', 'symbol_right'], 'string', 'max' => 12],
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

  public function getCode()
  {
      return strtolower(explode('-', $this->locale)[1]);
  }

  public function isDefault()
  {
    return Yii::$app->shopConfig->getParam('currency') == $this->code;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'title' => YII::t('currency', 'Currency name'),
      'code' => YII::t('currency', 'ISO Code'),
      'decimal_place' => YII::t('currency', 'Decimal place'),
      'symbol_left' => YII::t('currency', 'Symbol left'),
      'symbol_right' => YII::t('currency', 'Symbol right'),
      'value' => YII::t('app', 'Value'),
      'status' => YII::t('app', 'Status'),
    ];
  }
}
