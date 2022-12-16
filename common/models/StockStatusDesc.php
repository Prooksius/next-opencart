<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_stock_status_description".
 */
class StockStatusDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_stock_status_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['stock_status_id', 'language_id', 'name'], 'required'],
      [['stock_status_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 32],
//      [['stock_status_id', 'language_id'], 'unique', 'targetAttribute' => ['stock_status_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'stock_status_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}