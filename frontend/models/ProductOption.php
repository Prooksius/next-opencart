<?php

namespace frontend\models;

use Yii;
use common\models\ProductOption as ModelsProductOption;

/**
 * This is the model class for table "oc_product_option".
 */
class ProductOption extends ModelsProductOption
{

  public $optionName;
  public $optionType;
  public $optionAlias;
  
  public static function getProductOptions($product_id)
  {
    $product_option_data = [];
    
    $product_options = self::find()
      ->alias('po')
      ->select([
        'po.*',
        'o.type AS optionType',
        'o.alias AS optionAlias',
        'od.name AS optionName'
      ])
      ->leftJoin('oc_option o', 'o.id = po.option_id')
      ->leftJoin('oc_option_description od', 'od.option_id = o.id')
      ->where([
        'po.product_id' => (int)$product_id,
        'od.language_id' => Yii::$app->language,
      ])
      ->orderBy([
        'o.sort_order' => SORT_ASC
      ])
      ->all();

    foreach ($product_options as $product_option) {

      $product_option_value_data = [];

      $product_option_values = ProductOptionValue::find()
        ->alias('pov')
        ->select([
          'pov.*',
          'ov.color AS optionValueColor',
          'ov.alias AS optionValueAlias',
          'ovd.name AS optionValueName'
        ])
        ->leftJoin('oc_option_value ov', 'ov.id = pov.option_value_id')
        ->leftJoin('oc_option_value_description ovd', 'ovd.option_value_id = ov.id')
        ->where([
          'pov.product_id' => (int)$product_id,
          'pov.product_option_id' => $product_option->id,
          'ovd.language_id' => Yii::$app->language,
        ])
        ->orderBy([
          'ov.sort_order' => SORT_ASC
        ])
        ->all();
      
      foreach ($product_option_values as $product_option_value) {
        $product_option_value_data[] = [
					'product_option_value_id' => $product_option_value->id,
					'option_value_id'         => $product_option_value->option_value_id,
					'name'                    => $product_option_value->optionValueName,
					'image'                   => $product_option_value->image,
					'color'                   => $product_option_value->optionValueColor,
					'quantity'                => $product_option_value->quantity,
					'subtract'                => $product_option_value->subtract,
					'price'                   => $product_option_value->price,
					'price_prefix'            => $product_option_value->price_prefix,
					'weight'                  => $product_option_value->weight,
					'weight_prefix'           => $product_option_value->weight_prefix,
					'alias'           		    => $product_option_value->optionValueAlias,
					'is_selected'             => false
        ];
      }
      $product_option_data[] = [
        'product_option_id'    => $product_option->id,
        'product_option_value' => $product_option_value_data,
        'option_id'            => $product_option->option_id,
        'name'                 => $product_option->optionName,
        'type'                 => $product_option->optionType,
        'value'                => $product_option->value,
        'required'             => (bool)(int)$product_option->required,
		    'error'				         => '',
        'alias'           	   => $product_option->optionAlias,
      ];
    }

    return $product_option_data;
  }
}
