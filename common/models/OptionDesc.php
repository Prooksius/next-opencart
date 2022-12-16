<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_filter_description".
 */
class OptionDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_option_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['option_id', 'language_id', 'name'], 'required'],
      [['option_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 64],
      [['description'], 'string'],
//      [['option_id', 'language_id'], 'unique', 'targetAttribute' => ['option_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'option_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'description' => YII::t('app', 'Description'),
    ];
  }
}