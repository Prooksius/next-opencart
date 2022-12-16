<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_attribute".
 */
class Attribute extends \yii\db\ActiveRecord
{
   const VALUE_TYPE_CHECKBOX = 0;
   const VALUE_TYPE_RANGE = 1;
   const VALUE_TYPE_RADIO = 2;

   const VALUE_SORT_NUMBER = 0;
   const VALUE_SORT_STRING = 1;

   public $name;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_attribute';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['attribute_group_id', 'sort_order', 'filter_sort_order', 'value_type', 'value_sort', 'show_filter', 'show_product', 'show_product_tile', 'open_filter'], 'integer'],
      [['alias', 'icon'], 'string'],
    ];
  }

  public static function getAliases()
  {
    return Yii::$app->cache->getOrSet('attribute_aliases', function () {
      return self::find()
        ->select('alias')
        ->column();
    }, 3600);
  }

  public static function getAllAttributes()
  {
    $saved_items = Yii::$app->cache->getOrSet('attribute_list', function () {
      return self::find()
        ->alias('a')
        ->select([
          'a.alias',
          'a.icon',
          'ad.name',
          'a.value_type'
        ])
        ->leftJoin('oc_attribute_description ad', 'ad.attribute_id = a.id')
        ->where(['ad.language_id' => \Yii::$app->language])
        ->all();
    }, 3600);

    $items = [];

    foreach ($saved_items as $item) {
      $items[] = [
        'alias' 	    => $item->alias,
        'name'  	    => $item->name,
        'value_type'  => $item->value_type,
        'icon' 		    => $item->icon,
      ];
    }

    return $items;
  }

  public static function getSelectedAttributes($attr_alias, $sel_attrs)
  {
    $items = [];
    $attrs = [];
    foreach ($sel_attrs as $item) {
      $attrs[] = Yii::$app->db->quoteValue($item);
    }
    $sel_attrs = implode(", ", $attrs);
    
    $selected = ProductAttribute::find()
      ->alias('pa')
      ->select([
        'pa.alias',
        'pa.text'
      ])
      ->leftJoin('oc_attribute a', 'a.id = pa.attribute_id')
      ->where("a.alias = " . Yii::$app->db->quoteValue($attr_alias) . " AND 
                          pa.alias IN (" . $sel_attrs . ") AND 
                          pa.language_id = '" . \Yii::$app->language . "'")
      ->orderBy(['pa.text' => SORT_ASC])                          
      ->all();
    
    foreach ($selected as $item) {
      $items[$item->alias] = $item->text;
    }

    return $items;
  }

}
