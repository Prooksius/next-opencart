<?php

namespace common\models;

use Yii;
use common\components\MyActiveRecord; 

/**
 * This is the model class for table "oc_stock_status".
 */
class StockStatus extends MyActiveRecord
{

  protected $_desc_class = '\common\models\StockStatusDesc';
  protected $_desc_id_name = 'stock_status_id';
  protected $_desc_fields = ['name'];

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_stock_status';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['languages'], 'safe'],
    ];
  }
}
