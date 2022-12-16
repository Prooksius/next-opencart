<?php

namespace app\models;

use common\models\Language;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "oc_module".
 */
class Module extends \yii\db\ActiveRecord
{

  private static $_all_groups;

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
      [['module_group_id', 'sort_order', 'status'], 'integer'],
      [['name'], 'string', 'max' => 100],
      [['sort_order'], 'default', 'value' => 0],
      [['settings'], 'string'],
      [['settingsArr'], 'safe'],
    ];
  }

  public function getSettingsArr()
  {
    return Json::decode($this->settings, true);
  }

  public function setSettingsArr($value)
  {
    $this->settings = Json::encode($value);
  }

  public function getLanguagesList()
  {
    return Language::find()->all();
  }

  public static function getAllGroups()
  {
    if (!self::$_all_groups) {
      self::$_all_groups = ArrayHelper::map(
        self::find()
          ->alias('m')
          ->select([
            'm.id', 
            'm.name', 
            'mg.name AS moduleGroup'
          ])
          ->leftJoin('oc_module_group mg', 'mg.id = m.module_group_id')
          ->orderBy('mg.name ASC, m.name ASC')
          ->all(),
        'id', 'name', 'moduleGroup');
    }
    return self::$_all_groups;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'name' => YII::t('app', 'Name'),
      'status' => YII::t('app', 'Active?'),
    ];
  }
}
