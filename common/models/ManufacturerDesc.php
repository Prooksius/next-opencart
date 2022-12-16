<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_manufacturer_description".
 */
class ManufacturerDesc extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_manufacturer_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['manufacturer_id', 'language_id', 'name'], 'required'],
      [['manufacturer_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 64],
      [['meta_title', 'meta_h1', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
      [['description'], 'string'],
//      [['manufacturer_id', 'language_id'], 'unique', 'targetAttribute' => ['manufacturer_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'manufacturer_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'description' => YII::t('app', 'Description'),
      'meta_title' => YII::t('app', 'Meta Title'),
      'meta_h1' => YII::t('app', 'Meta H1'),
      'meta_description' => YII::t('app', 'Meta Description'),
      'meta_keyword' => YII::t('app', 'Meta Keywords'),
    ];
  }
}