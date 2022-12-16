<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_filter_description".
 */
class FilterDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_filter_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['filter_id', 'language_id', 'name'], 'required'],
      [['filter_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 64],
//      [['filter_id', 'language_id'], 'unique', 'targetAttribute' => ['filter_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'filter_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}