<?php

namespace frontend\extensions\payments;

use frontend\models\OrderHistory;
use Yii;

class Cod
{
  public static function getMethod(array $settings, array $address, float $total) {

    $status = true;

    $min_sum = (float)$settings['min_sum'];
		if ($min_sum > 0 && $min_sum > $total) {
			$status = false; 
    }

    $method_data = [];

    if ($status) {
      $method_data = [
        'code'       => 'Cod',
        'title'      => $settings['title'][Yii::$app->language],
        'terms'      => '',
      ];
    }

		return $method_data;
	}

  public static function payment(array $settings, array $order_data, array $totals) {

    $order_status_id = (int)$settings['order_status'];

    OrderHistory::addHistory($order_data, $order_status_id, $order_data['comment']);

    Yii::$app->cart->clear();

    return [
      'success' => 1,
    ];
    
  }
}