<?php

namespace frontend\models;

use yii\helpers\Json;

/**
 * This is the model class for table "oc_module".
 */
class Module extends \yii\db\ActiveRecord
{

  public $moduleGroup;
  
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_module';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['module_group_id', 'name'], 'required'],
      [['module_group_id', 'status'], 'integer'],
      [['name'], 'string', 'max' => 100],
      [['settings'], 'string'],
      [['settingsArr'], 'safe'],
    ];
  }

  public function getSettingsArr()
  {
    return Json::decode($this->settings, true);
  }
}
