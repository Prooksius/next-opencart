<?php

namespace frontend\models;

use common\models\OrderHistory as ModelsOrderHistory;
use GuzzleHttp\Handler\Proxy;
use Yii;

/**
 * This is the model class for table "oc_order_history".
 */
class OrderHistory extends ModelsOrderHistory
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
