<?php

namespace frontend\extensions\modules;

use frontend\models\Product;
use Yii;
use yii\helpers\Json;

class LookedProducts
{

  public static function getContent($settings, $params = null)
  {
    $content = [
      'title' => $settings['title'][Yii::$app->language],
      'subtitle' => $settings['subtitle'][Yii::$app->language],
      'visible' => (int)$settings['visible'],
      'products' => [],
    ];

    $products = $params['looked'];
    if ($products && !is_array($products)) {
      $products = Json::decode($products);
    }
    if (!$products || !is_array($products)) {
      $products = [];
    }
    foreach ($products as $product_id) {

      $product = Product::getProduct($product_id);
      if ($product) {

        Product::fillProduct($product, true);

        $content['products'][] = $product;
      }
    }

    return $content;
  }
}