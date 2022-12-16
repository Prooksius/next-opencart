<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_pcolor_description".
 */
class PcolorDesc extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_pcolor_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['pcolor_id', 'language_id', 'name'], 'required'],
      [['pcolor_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 255],
//    [['pcolor_id', 'language_id'], 'unique', 'targetAttribute' => ['pcolor_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'pcolor_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}