<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_order_status_description".
 */
class OrderStatusDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order_status_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['order_status_id', 'language_id', 'name'], 'required'],
      [['order_status_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 32],
//      [['order_status_id', 'language_id'], 'unique', 'targetAttribute' => ['order_status_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'order_status_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}