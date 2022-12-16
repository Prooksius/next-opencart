<?php

namespace app\models;

use common\models\StockStatus as ModelsStockStatus;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_stock_status".
 */
class StockStatus extends ModelsStockStatus
{
  private static $_all_statuses;

  public $name; 

  public static function getAllStatuses()
  {
    if (!self::$_all_statuses) {
      self::$_all_statuses = ArrayHelper::map(
        self::find()
          ->alias('ss')
          ->select([
            'ss.id', 
            'ssd.name'
          ])
          ->leftJoin('oc_stock_status_description ssd', '(ssd.stock_status_id = ss.id AND ssd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('ssd.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_statuses;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}
