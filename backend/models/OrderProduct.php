<?php

namespace app\models;

use common\models\OrderProduct as ModelsOrderProduct;
use Yii;
use yii\helpers\ArrayHelper;

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

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'order_id' => YII::t('order', 'Order'),
      'product_id' => YII::t('product', 'Product'),
      'name' => YII::t('app', 'Name'),
      'model' => YII::t('product', 'Model'),
      'quantity' => YII::t('product', 'Quantity'),
      'price' => YII::t('product', 'Price'),
      'total' => YII::t('order', 'Total'),
    ];
  }
}
