<?php
  
namespace common\components;

use common\models\Settings;
use yii\base\Component;
  
class AppConfig extends Component {
  
  private $config = [];

  public function Init()
  {
    $this->config = Settings::readAllSettings('general');
  }

  public function getParam($param)
  {
    return $this->config[$param];
  }

  public function getParams()
  {
    return $this->config;
  }

  public function setParam($param, $value)
  {
    $this->config[$param] = $value;
  }

}