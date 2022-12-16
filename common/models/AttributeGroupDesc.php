<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "oc_attribute_group_description".
 */
class AttributeGroupDesc extends \yii\db\ActiveRecord
{

  

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_attribute_group_description';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['attribute_group_id', 'language_id', 'name'], 'required'],
      [['attribute_group_id'], 'integer'],
      [['language_id'], 'string', 'max' => 10],
      [['name'], 'string', 'max' => 64],
//      [['attribute_group_id', 'language_id'], 'unique', 'targetAttribute' => ['attribute_group_id', 'language_id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'attribute_group_id' => YII::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}