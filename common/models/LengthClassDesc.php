<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_length_class_description".
 */
class LengthClassDesc extends \yii\db\ActiveRecord
{

  

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_length_class_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['length_class_id', 'language_id', 'title', 'unit'], 'required'],
      [['length_class_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['title'], 'string', 'max' => 32],
      [['unit'], 'string', 'max' => 4],
//      [['length_class_id', 'language_id'], 'unique', 'targetAttribute' => ['length_class_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'length_class_id' => YII::t('app', 'ID'),
      'title' => YII::t('app', 'Title'),
      'unit' => YII::t('app', 'Unit'),
    ];
  }
}