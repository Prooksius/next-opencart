<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_option_value_description".
 */
class OptionValueDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_option_value_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['option_value_id', 'language_id', 'name'], 'required'],
      [['option_value_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 64],
//      [['option_value_id', 'language_id'], 'unique', 'targetAttribute' => ['option_value_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'option_value_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}