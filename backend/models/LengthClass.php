<?php

namespace app\models;

use common\models\LengthClass as ModelsLengthClass;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_stock_status".
 */
class LengthClass extends ModelsLengthClass
{
  private static $_all_classes;

  public $title; 
  public $unit; 

  public static function getAllClasses()
  {
    if (!self::$_all_classes) {
      self::$_all_classes = ArrayHelper::map(
        self::find()
          ->alias('lc')
          ->select([
            'lc.id', 
            'lcd.title'
          ])
          ->leftJoin('oc_length_class_description lcd', '(lcd.length_class_id = lc.id AND lcd.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('lcd.title ASC')
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
