<?php
  
namespace frontend\components;

use common\models\ModuleGroup;
use yii\base\Component;
use yii\helpers\Json;

class AppCartTotal extends Component {
  
  private $methodSettings = [];
  
  public function Init()
  {
    $modules = ModuleGroup::find()
      ->where(['category' => 'total', 'status' => 1])
      ->orderBy(['sort_order' => SORT_ASC])
      ->all();
    
    foreach ($modules as $module) {
      $this->methodSettings[] = [
        'code' => $module->code,
        'moduleClass' => 'frontend\extensions\totals\\' . $module->code,
        'settings' => Json::decode($module->settings, true),
      ];
    }
  }

  public function calculate(array &$totalsArray)
  {
    foreach ($this->methodSettings as $module) {
      $moduleClass = $module['moduleClass'];
      $moduleClass::getTotal($totalsArray, $module['settings']);
    }
  }
}