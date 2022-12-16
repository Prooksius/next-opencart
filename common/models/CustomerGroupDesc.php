<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_customer_group_description".
 */
class CustomerGroupDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_customer_group_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['customer_group_id', 'language_id', 'name'], 'required'],
      [['customer_group_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 32],
      [['description'], 'string'],
      [['customer_group_id', 'language_id'], 'unique', 'targetAttribute' => ['customer_group_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'customer_group_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'description' => YII::t('app', 'Description'),
    ];
  }
}