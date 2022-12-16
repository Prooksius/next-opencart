<?php

namespace app\models;

use common\models\OrderTotal as ModelsOrderTotal;
use Yii;
use yii\helpers\ArrayHelper;

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

  public static function totalsList(int $id)
  {
    return self::find()
      ->where(['order_id' => $id])
      ->asArray()
      ->all();
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'order_id' => YII::t('order', 'Order'),
      'code' => YII::t('order', 'Code'),
      'title' => YII::t('order', 'Title'),
      'value' => YII::t('order', 'Value'),
      'sort_order' => YII::t('app', 'Sort order'),
    ];
  }
}
