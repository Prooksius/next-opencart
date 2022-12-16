<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_option".
 */
class Option extends \yii\db\ActiveRecord
{

  const TYPE_SELECT = 'select';
  const TYPE_RADIO = 'radio';
  const TYPE_CHECKBOX = 'checkbox';
  const TYPE_TEXT = 'text';
  const TYPE_TEXTAREA = 'textarea';
  const TYPE_DATE = 'date';
  const TYPE_TIME = 'time';
  const TYPE_DATETIME = 'datetime';

  public $name;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_option';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order', 'filter_sort_order', 'open_filter'], 'integer'],
      [['type', 'alias'], 'string'],
    ];
  }

  public static function getAliases()
  {
    return Yii::$app->cache->getOrSet('option_aliases', function () {
      return self::find()
        ->select('alias')
        ->column();
    }, 3600);
  }

  public static function getAllOptions() 
  {
    $saved_items = Yii::$app->cache->getOrSet('option_list', function () {
      return self::find()
        ->alias('o')
        ->select([
          'o.alias',
          'od.name',
        ])
        ->leftJoin('oc_option_description od', 'od.option_id = o.id')
        ->where(['od.language_id' => \Yii::$app->language])
        ->all();
    }, 3600);

    $items = [];

    foreach ($saved_items as $item) {
      $items[] = [
        'alias' => $item->alias,
        'name'  => $item->name,
      ];
    }

    return $items;
  }

  public static function getSelectedOptions($opt_alias, $sel_opts)
  {
    $items = [];
    $opts = [];
    foreach ($sel_opts as $item) {
      $opts[] = Yii::$app->db->quoteValue($item);
    }
    $sel_opts = implode(", ", $opts);
    
    $selected = OptionValue::find()
      ->alias('ov')
      ->select([
        'ov.alias',
        'ovd.name',
      ])
      ->leftJoin('oc_option_value_description ovd', 'ov.id = ovd.option_value_id')
      ->leftJoin('oc_option o', 'o.id = ov.option_id')
      ->where("o.alias = " . Yii::$app->db->quoteValue($opt_alias) . " AND 
                          ov.alias IN (" . $sel_opts . ") AND 
                          ovd.language_id = '" . \Yii::$app->language . "'")
      ->orderBy(['ovd.name' => SORT_ASC])                    
      ->all();
    
    foreach ($selected as $item) {
      $items[$item->alias] = $item->name;
    }

    return $items;
  }  
}
