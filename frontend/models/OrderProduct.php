<?php

namespace frontend\models;

use common\models\OrderProduct as ModelsOrderProduct;
use Yii;

/**
 * This is the model class for table "oc_order_product".
 */
class OrderProduct extends ModelsOrderProduct
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
