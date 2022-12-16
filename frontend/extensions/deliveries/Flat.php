<?php

namespace frontend\extensions\deliveries;

use Yii;

class Flat
{
  public static function getQuote(array $settings, array $address) {

    $quote_data = [];
    $quote_items = [];
    
    $quote_item = [
      'code'         => 'Flat.Flat',
      'title'        => $settings['description'][Yii::$app->language],
      'cost'         => (float)$settings['price'],
      'text'         => Yii::$app->currency->format((float)$settings['price'], Yii::$app->currency->getCurrent()),
    ];

    $quote_items[] = $quote_item;
    $quote_data['Flat'] = $quote_item;

    $method_data = [
      'code'         => 'Flat',
      'title'        => $settings['title'][Yii::$app->language],
      'quote'        => $quote_data,
      'quote_items'  => $quote_items,
      'error'        => false
    ];

		return $method_data;
	}
}