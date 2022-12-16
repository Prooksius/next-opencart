<?php
  
namespace frontend\components;

use common\models\ModuleGroup;
use yii\base\Component;
use yii\helpers\Json;

class AppPayment extends Component {
  
  private $methodSettings = [];
  private $methods = [];
  private $methodsByCode = [];
  private $currentMethod = [];

  public function Init()
  {
    $this->methods = [];

    $modules = ModuleGroup::find()
      ->where(['category' => 'payment', 'status' => 1])
      ->orderBy(['sort_order' => SORT_ASC])
      ->all();
    
    foreach ($modules as $module) {
      $this->methodSettings[] = [
        'code' => $module->code,
        'moduleClass' => 'frontend\extensions\payments\\' . $module->code,
        'settings' => Json::decode($module->settings, true),
      ];
    }
  }

  public function methodByCode($code)
  {
    $module = false;
    foreach ($this->methodSettings as $module) {
      if ($module->code == $code) {
        break;
      }
    }
    return $module;
  }

  public function calculate($total)
  {
    $this->methods = [];

    foreach ($this->methodSettings as $module) {
      
      $moduleClass = $module['moduleClass'];

      $method = $moduleClass::getMethod($module['settings'], [], $total);

      if ($method) {
        $this->methods[] = $method;

        $method['moduleClass'] = $moduleClass;
        $method['settings'] = $module['settings'];
        $this->methodsByCode[$module['code']] = $method;
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

    if (!isset($this->methodsByCode[$this->currentMethod['code']])) {
      return false;
    }

    return $this->methodsByCode[$this->currentMethod['code']];
  }

  public function processPayment(array $order_data, array $totals)
  {
    $current = $this->getCurrent();

    if ($current) {

      $moduleClass = $current['moduleClass'];
      $settings = $current['settings'];
      
      return $moduleClass::payment($settings, $order_data, $totals);
    }

    return false;
  }

  public function setCurrent($code)
  {
    $this->currentMethod = [
      'code' => $code,
      'error' => $code == ''
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