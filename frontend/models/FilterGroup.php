<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "oc_filter_group".
 */
class FilterGroup extends \yii\db\ActiveRecord
{

  public $name;

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_filter_group';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['sort_order', 'filter_sort_order', 'open_filter', 'product_show'], 'integer'],
      [['alias', 'icon'], 'string'],
    ];
  }

  public static function getAliases()
  {
    return Yii::$app->cache->getOrSet('filter_group_aliases', function () {
      return self::find()
        ->select('alias')
        ->column();
    }, 3600);
  }

  public static function getAllFilters()
  {
    $saved_items = Yii::$app->cache->getOrSet('filter_group_list', function () {
      return self::find()
        ->alias('fg')
        ->select([
          'fg.alias',
          'fg.icon',
          'fgd.name',
        ])
        ->leftJoin('oc_filter_group_description fgd', 'fgd.filter_group_id = fg.id')
        ->where(['fgd.language_id' => \Yii::$app->language])
        ->orderBy(['fgd.name' => SORT_ASC])
        ->all();
    }, 3600);

    $items = [];

    foreach ($saved_items as $item) {
      $items[] = [
        'alias' => $item->alias,
        'name'  => $item->name,
        'icon'  => $item->icon,
      ];
    }

    return $items;

  }

}
