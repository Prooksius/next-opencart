<?php

namespace app\models;

use common\models\Language;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "oc_module_group".
 */
class ModuleGroup extends \yii\db\ActiveRecord
{

  public $instances;
  public $module_id;
  public $module_sort_order;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_module_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['category', 'name', 'code'], 'string'],
      [['category', 'code'], 'unique', 'targetAttribute' => ['category', 'code']],
      [['sort_order', 'status', 'single'], 'integer'],
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

  public function getOrderStatusesList()
  {
    return OrderStatus::getAllStatuses();
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'name' => YII::t('app', 'Name'),
      'instances' => YII::t('module', 'Instances'),
      'settings' => YII::t('module', 'settings'),
      'settingsArr' => YII::t('module', 'settings'),
      'sort_order' => YII::t('app', 'Sort Order'),
      'single' => YII::t('module', 'Single'),
      'status' => YII::t('app', 'Active?'),
    ];
  }
}
