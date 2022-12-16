<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oc_layout_module".
 */
class LayoutModule extends \yii\db\ActiveRecord
{

  public $instances;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_layout_module';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['layout_id', 'module_id', 'sort_order'], 'integer'],
      [['position'], 'string', 'max' => 20],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'name' => YII::t('app', 'Name'),
      'code' => YII::t('app', 'Code'),
    ];
  }
}
