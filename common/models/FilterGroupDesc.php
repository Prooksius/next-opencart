<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_filter_group_description".
 */
class FilterGroupDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_filter_group_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['filter_group_id', 'language_id', 'name'], 'required'],
      [['filter_group_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 64],
      [['description'], 'string'],
//      [['filter_group_id', 'language_id'], 'unique', 'targetAttribute' => ['filter_group_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'filter_group_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'description' => YII::t('app', 'Description'),
    ];
  }
}