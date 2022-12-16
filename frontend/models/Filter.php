<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_filter".
 */
class Filter extends \yii\db\ActiveRecord
{

  public $name;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['filter_group_id', 'sort_order'], 'integer'],
      [['alias', 'image'], 'string'],
    ];
  }

  public static function getSelectedFilters($filter_alias, $sel_filters)
  {
    $items = [];
    $filters = [];
    foreach ($sel_filters as $item) {
      $filters[] = Yii::$app->db->quoteValue($item);
    }
    $sel_filters = implode(", ", $filters);
    
    $selected = self::find()
      ->alias('f')
      ->select([
        'f.alias',
        'fd.name'
      ])
      ->leftJoin('oc_filter_description fd', 'f.id = fd.filter_id')
      ->leftJoin('oc_filter_group fg', 'fg.id = f.filter_group_id')
      ->where("fg.alias = " . Yii::$app->db->quoteValue($filter_alias) . " AND 
                          f.alias IN (" . $sel_filters . ") AND 
                          fd.language_id = '" . \Yii::$app->language . "'")
      ->all();
    
    foreach ($selected as $item) {
      $items[$item->alias] = $item->name;
    }

    return $items;
  }

}
