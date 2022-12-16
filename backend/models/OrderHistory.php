<?php

namespace app\models;

use common\models\OrderHistory as ModelsOrderHistory;
use Yii;
use yii\helpers\ArrayHelper;

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

  public static function historyList(int $id)
  {
    return self::find()
      ->where(['order_id' => $id])
      ->orderBy(['created_at' => SORT_ASC])
      ->asArray()
      ->all();
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => YII::t('app', 'ID'),
      'created_at' => YII::t('order', 'Create Date'),
      'created' => YII::t('order', 'Create Date'),
      'order_id' => YII::t('order', 'Order'),
      'order_status_id' => YII::t('order', 'Order Status'),
      'comment' => YII::t('order', 'Comment'),
      'notify' => YII::t('order', 'Notify'),
    ];
  }
}
