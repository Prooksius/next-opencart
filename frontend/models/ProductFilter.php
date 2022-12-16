<?php

namespace frontend\models;

use Yii;
use common\models\ProductFilter as ModelsProductFilter;

/**
 * This is the model class for table "oc_product_filter".
 */
class ProductFilter extends ModelsProductFilter
{

  public $filter_group_id;
  public $icon;
  public $name;
  public $groupName;

  public static function getProductFilters($product_id, $page = false)
  {
    $product_filter_group_data = [];

    $product_filter_groups = self::find()
      ->alias('pf')
      ->select([
        'fg.id AS filter_group_id', 
        'fgd.name AS groupName'
      ])
      ->leftJoin('oc_filter f', 'f.id = pf.filter_id')
      ->leftJoin('oc_filter_group fg', 'fg.id = f.filter_group_id')
      ->leftJoin('oc_filter_group_description fgd', 'fgd.filter_group_id = fg.id')
      ->where([
        'pf.product_id' => (int)$product_id,
        'fgd.language_id' => Yii::$app->language,
      ])
      ->groupBy('fg.id')
      ->orderBy([
        'fg.sort_order' => SORT_ASC,
        'fgd.name' => SORT_ASC
      ])
      ->all();

    foreach ($product_filter_groups as $product_filter_group) {

      if ($page) {
        $product_filter_data = [];
      }

      $product_filters = self::find()
        ->alias('pf')
        ->select([
          'pf.filter_id',
          'f.image AS icon',
          'fd.name AS name',
        ])
        ->leftJoin('oc_filter f', 'f.id = pf.filter_id')
        ->leftJoin('oc_filter_description fd', 'fd.filter_id = f.id')
        ->where([
          'pf.product_id' => (int)$product_id,
          'f.filter_group_id' => (int)$product_filter_group->filter_group_id,
          'fd.language_id' => Yii::$app->language,
        ])
        ->orderBy([
          'f.sort_order' => SORT_ASC,
          'fd.name' => SORT_ASC
        ])
        ->all();
  
      foreach ($product_filters as $product_filter) {
        if ($page) {
          $product_filter_data[] = [
            'filter_id'    => $product_filter->filter_id,
            'name'         => $product_filter->name,
            'icon'         => $product_filter->icon,
          ];
        } else {
          $product_filter_group_data[] = [
            'filter_id'    => $product_filter->filter_id,
            'name'         => $product_filter->name,
            'icon'         => $product_filter->icon,
          ];
        }
      }

      if ($page) {
        $product_filter_group_data[] = [
          'filter_group_id' => $product_filter_group->filter_group_id,
          'name'            => $product_filter_group->groupName,
          'filter'          => $product_filter_data
        ];
      }
    }

    return $product_filter_group_data;
  }

}
