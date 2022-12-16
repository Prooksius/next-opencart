<?php

namespace app\models;

use common\models\WeightClass as ModelsWeightClass;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_stock_status".
 */
class WeightClass extends ModelsWeightClass
{
  private static $_all_classes;

  public $title; 
  public $unit; 

  public static function getAllClasses()
  {
    if (!self::$_all_classes) {
      self::$_all_classes = ArrayHelper::map(
        self::find()
          ->alias('wc')
          ->select([
            'wc.id', 
            'wcd.title'
          ])
          ->leftJoin('oc_weight_class_description wcd', '(wcd.weight_class_id = wc.id AND wcd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('wcd.title ASC')
          ->all(),
        'id', 'title');
    }
    return self::$_all_classes;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'title' => YII::t('app', 'Title'),
    ];
  }
}
