<?php

namespace app\models;

use common\models\OrderOption as ModelsOrderOption;
use Yii;
use yii\helpers\ArrayHelper;

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

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'order_id' => YII::t('order', 'Order'),
      'order_product_id' => YII::t('product', 'Product'),
      'product_option_id' => YII::t('product', 'Product option'),
      'product_option_value_id' => YII::t('product', 'Product option value'),
      'name' => YII::t('app', 'Name'),
      'value' => YII::t('product', 'Product option value'),
      'type' => YII::t('option', 'Option type'),
    ];
  }
}
