<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "oc_order_status".
 */
class OrderStatus extends MyActiveRecord
{

  protected $_desc_class = '\common\models\OrderStatusDesc';
  protected $_desc_id_name = 'order_status_id';
  protected $_desc_fields = ['name'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order_status';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['color'], 'string', 'max' => 10],
      [['languages'], 'safe'],
    ];
  }
}
