<?php

namespace frontend\extensions\modules;

use frontend\models\Product;
use Yii;
use yii\helpers\Json;

class SpecialProducts
{

  public static function getContent($settings, $params = null)
  {
    $content = [
      'title' => $settings['title'][Yii::$app->language],
      'subtitle' => $settings['subtitle'][Yii::$app->language],
      'visible' => (int)$settings['visible'],
      'products' => [],
    ];

		$filter_data = [
			'sort'  => 'discount',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $settings['limit']
    ];

    $products = Product::getProductSpecials($filter_data);

    foreach ($products as $product) {
      Product::fillProduct($product, true);
      $content['products'][] = $product;
    }

    return $content;
  }
}