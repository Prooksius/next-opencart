<?php

namespace frontend\models;

use common\models\OrderTotal as ModelsOrderTotal;
use Yii;

/**
 * This is the model class for table "oc_order_total".
 */
class OrderTotal extends ModelsOrderTotal
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
