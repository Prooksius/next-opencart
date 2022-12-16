<?php

namespace frontend\extensions\deliveries;

use Yii;

class Pickup
{
  public static function getQuote(array $settings, array $address) {

    $quote_data = [];
    $quote_items = [];

    $quote_item = [
      'code'         => 'Pickup.Pickup',
      'title'        => $settings['description'][Yii::$app->language],
      'cost'         => 0.00,
      'text'         => Yii::$app->currency->format(0.00, Yii::$app->currency->getCurrent()),
    ];

    $quote_items[] = $quote_item;
    $quote_data['Pickup'] = $quote_item;

    $method_data = [
      'code'         => 'Pickup',
      'title'        => $settings['title'][Yii::$app->language],
      'quote'        => $quote_data,
      'quote_items'  => $quote_items,
      'error'        => false
    ];

		return $method_data;
	}
}