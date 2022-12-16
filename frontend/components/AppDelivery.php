<?php
  
namespace frontend\components;

use common\models\ModuleGroup;
use yii\base\Component;
use yii\helpers\Json;

class AppDelivery extends Component {
  
  private $methodSettings = [];
  private $methods = [];
  private $methodsByCode = [];
  private $currentMethod = [];

  public function Init()
  {
    $this->methods = [];

    $modules = ModuleGroup::find()
      ->where(['category' => 'delivery', 'status' => 1])
      ->orderBy(['sort_order' => SORT_ASC])
      ->all();
    
    foreach ($modules as $module) {
      $this->methodSettings[] = [
        'code' => $module->code,
        'moduleClass' => 'frontend\extensions\deliveries\\' . $module->code,
        'settings' => Json::decode($module->settings, true),
      ];
    }
  }

  public function calculate()
  {
    $this->methods = [];

    foreach ($this->methodSettings as $module) {
      
      $moduleClass = $module['moduleClass'];

      $quote = $moduleClass::getQuote($module['settings'], []);
      
      if ($quote) {
        $this->methodsByCode[$module['code']] = $quote;
        $this->methods[] = $quote;
      }
    }
  }

  public function getMethods()
  {
    return !empty($this->methods) ? $this->methods : [];
  }

  public function getCurrent()
  {
    if (!$this->currentMethod['code'] || empty($this->methodsByCode)) {
      return false;
    }

    $method_arr = explode('.', $this->currentMethod['code']);

    if (!isset($this->methodsByCode[$method_arr[0]]['quote'][$method_arr[1]])) {
      return false;
    }

    return $this->methodsByCode[$method_arr[0]]['quote'][$method_arr[1]];
  }

  public function getCurrentCost()
  {
    $current = $this->getCurrent();
    if ($current && isset($current['cost'])) {
      return (float)$current['cost'];
    }

    return false;
  }

  public function setCurrent($code)
  {
    $this->currentMethod = [
      'code' => $code,
      'error' => $code != ''
    ];
  }

  public function getCurrentCode()
  {
    return $this->currentMethod['code'];
  }

  public function getCurrentFull()
  {
    return $this->currentMethod;
  }

}