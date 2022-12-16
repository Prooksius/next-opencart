<?php

namespace frontend\extensions\totals;

use Yii;

class Total
{
	public static function getTotal(array &$total, array $settings) {

		$total['totals'][] = array(
			'code'       => 'Total',
			'title'      => $settings['title'][Yii::$app->language],
			'value'      => max(0, $total['total']),
			'value_str'  => Yii::$app->currency->format((float)max(0, $total['total']), Yii::$app->currency->getCurrent()),
		);
	}
}