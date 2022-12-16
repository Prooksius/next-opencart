<?php

namespace app\models;

use common\models\Option as ModelsOption;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "attribute_grou".
 */
class Option extends ModelsOption
{
  private static $_all_options;

  public $name; 

  public static function getAllOptions()
  {
    if (!self::$_all_options) {
      self::$_all_options = ArrayHelper::map(
        self::find()
          ->alias('o')
          ->select([
            'o.id', 
            'od.name'
          ])
          ->leftJoin('oc_option_description od', '(od.option_id = o.id AND od.language_id = "' . \Yii::$app->language . '")')
          ->orderBy('od.name ASC')
          ->all(),
        'id', 'name');
    }
    return self::$_all_options;
  }

  public function getTypesList()
  {
    return [
      self::TYPE_SELECT => YII::t('app', 'Select'),
      self::TYPE_CHECKBOX => YII::t('app', 'Checkbox'),
      self::TYPE_RADIO => YII::t('app', 'Radio'),
      self::TYPE_TEXT => YII::t('app', 'Text'),
      self::TYPE_TEXTAREA => YII::t('app', 'Textarea'),
      self::TYPE_DATE => YII::t('app', 'Date'),
      self::TYPE_TIME => YII::t('app', 'Time'),
      self::TYPE_DATETIME => YII::t('app', 'Datetime'),
    ];
  }  

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => Yii::t('app', 'ID'),
      'type' => YII::t('option', 'Option type'),
      'name' => YII::t('app', 'Name'),
      'description' => YII::t('app', 'Description'),
      'alias' => YII::t('app', 'Alias'),
      'open_filter' => YII::t('app', 'Open in filter?'),
      'sort_order' => YII::t('app', 'Sort Order'),
      'filter_sort_order' => YII::t('app', 'Filter sort order'),
    ];
  }
}
