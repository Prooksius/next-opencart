<?php

namespace frontend\models;

use common\models\ProductAttribute as ModelsProductAttribute;
use common\models\Language;
use Yii;

/**
 * This is the model class for table "oc_product_attribute".
 */
class ProductAttribute extends ModelsProductAttribute
{
  public $attribute_group_id;
  public $icon;
  public $name;
  public $groupName;

  public static function getProductAttributes($product_id, $page = false)
  {
    $product_attribute_group_data = [];

    $product_attribute_groups = self::find()
      ->alias('pa')
      ->select([
        'ag.id AS attribute_group_id', 
        'agd.name AS groupName'
      ])
      ->leftJoin('oc_attribute a', 'a.id = pa.attribute_id')
      ->leftJoin('oc_attribute_group ag', 'ag.id = a.attribute_group_id')
      ->leftJoin('oc_attribute_group_description agd', 'agd.attribute_group_id = ag.id')
      ->where([
        'pa.product_id' => (int)$product_id,
        'agd.language_id' => Yii::$app->language,
      ])
      ->groupBy('ag.id')
      ->orderBy([
        'ag.sort_order' => SORT_ASC,
        'agd.name' => SORT_ASC
      ])
      ->all();

    foreach ($product_attribute_groups as $product_attribute_group) {

      $product_attribute_data = [];
      $filter_page = $page ? 1 : null;
      $filter_tile = $page ? null : 1;

      $product_attributes = self::find()
        ->alias('pa')
        ->select([
          'pa.attribute_id',
          'a.icon',
          'ad.name AS name',
          'pa.text'
        ])
        ->leftJoin('oc_attribute a', 'a.id = pa.attribute_id')
        ->leftJoin('oc_attribute_description ad', 'ad.attribute_id = a.id')
        ->where([
          'pa.product_id' => (int)$product_id,
          'a.attribute_group_id' => (int)$product_attribute_group->attribute_group_id,
          'ad.language_id' => Yii::$app->language,
          'pa.language_id' => Yii::$app->language,
        ])
        ->andFilterWhere([
          'a.show_product' => $filter_page,
          'a.show_product_tile' => $filter_tile
        ])
        ->orderBy([
          'a.sort_order' => SORT_ASC,
          'ad.name' => SORT_ASC
        ])
        ->all();
  
      foreach ($product_attributes as $product_attribute) {
				$product_attribute_data[] = [
					'attribute_id' => $product_attribute->attribute_id,
					'name'         => $product_attribute->name,
					'icon'         => $product_attribute->icon,
					'text'         => $product_attribute->text
        ];
      }

			$product_attribute_group_data[] = [
				'attribute_group_id' => $product_attribute_group->attribute_group_id,
				'name'               => $product_attribute_group->groupName,
				'attribute'          => $product_attribute_data
      ];
    }

    return $product_attribute_group_data;
  }

}
