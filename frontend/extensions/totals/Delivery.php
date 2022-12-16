<?php

namespace frontend\extensions\totals;

use Yii;

class Delivery
{
	public static function getTotal(array &$total, array $settings) {

    $cost = Yii::$app->delivery->getCurrentCost();

    if ($cost !== false && $total['shipping']) {

      $total['totals'][] = array(
        'code'       => 'Delivery',
        'title'      => sprintf($settings['title'][Yii::$app->language], $total['count']),
        'value'      => $cost,
        'value_str'  => Yii::$app->currency->format($cost, Yii::$app->currency->getCurrent()),
      );

      $total['total'] += $cost;
    }
	}
}