<?php

namespace app\models;

use common\models\OptionValue as ModelsOptionValue;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "option".
 */
class OptionValue extends ModelsOptionValue
{
  private static $_all_option_values;

  public $name; 
  public $option_name; 

  public static function getAllOptionValues($option_id)
  {
    if (!self::$_all_option_values) {
      self::$_all_option_values = ArrayHelper::map(
        self::find()
          ->alias('ov')
          ->select([
            'ov.id', 
            'ovd.name AS name'
          ])
          ->leftJoin('oc_option_value_description ovd', '(ovd.option_value_id = ov.id AND ovd.language_id = "' . \Yii::$app->language . '")')
          ->where(['option_id' => $option_id])
          ->orderBy('ovd.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_option_values;
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'name' => YII::t('app', 'Name'),
      'option_id' => YII::t('option', 'Option'),
      'sort_order' => Yii::t('app', 'Sort order'),
      'filter_sort_order' => Yii::t('app', 'Filter sort order'),
      'alias' => Yii::t('app', 'Alias'),
      'open_filter' => Yii::t('app', 'Open in filter?'),
      'image' => Yii::t('app', 'Image'),
    ];
  }
}
