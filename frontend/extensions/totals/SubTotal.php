<?php

namespace frontend\extensions\totals;

use Yii;

class SubTotal
{
	public static function getTotal(array &$total, array $settings) {

		$total['totals'][] = array(
			'code'       => 'SubTotal',
			'title'      => sprintf($settings['title'][Yii::$app->language], $total['count']),
			'value'      => max(0, $total['total']),
			'value_str'  => Yii::$app->currency->format((float)max(0, $total['total']), Yii::$app->currency->getCurrent()),
		);
	}
}