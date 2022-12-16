<?php

namespace frontend\models;

use common\models\OrderOption as ModelsOrderOption;
use Yii;

/**
 * This is the model class for table "oc_order_option".
 */
class OrderOption extends ModelsOrderOption
{

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    $rules = parent::rules();
    return $rules;
  }
}
