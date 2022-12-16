<?php

namespace app\models;

use common\models\Language;
use common\models\OrderStatus as ModelsOrderStatus;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_order_status".
 */
class OrderStatus extends ModelsOrderStatus
{
  private static $_all_statuses;

  public $name; 

  public function getLanguagesList()
  {
    return Language::find()->all();
  }
  
  public static function getAllStatuses()
  {
    if (!self::$_all_statuses) {
      self::$_all_statuses = ArrayHelper::map(
        self::find()
          ->alias('os')
          ->select([
            'os.id', 
            'osd.name'
          ])
          ->leftJoin('oc_order_status_description osd', '(osd.order_status_id = os.id AND osd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('osd.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_statuses;
  }

  public function isDefault()
  {
    return (int)Yii::$app->shopConfig->getParam('order_status_id') == $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'color' => YII::t('app', 'Color'),
      'name' => YII::t('app', 'Name'),
    ];
  }
}
