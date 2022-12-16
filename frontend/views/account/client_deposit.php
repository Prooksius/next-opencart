<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 27.04.2020
 * Time: 14:38
 */

use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Html;

?>
<? Pjax::begin([
    'enablePushState' => false,
    'id' => 'client-deposits',
]); ?>
<h2>История клиента</h2>
<br />
<br />
<?= GridView::widget([
    'dataProvider' => $deposits,
    'id' => 'client-deposits-grid',
    'tableOptions' => ['class' => 'table-3 smalltable-3'],
	'columns' => [
		[
			'attribute' => 'created_at',
			'value' => function ($model) {
				return date('d.m.Y г. в G:i', $model->created_at);
			},
			'format' => 'raw',
		],
		[
			'attribute' => 'botname',
			'label'	=> 'Продукт',
		],
		[
			'attribute' => 'amount',
			'label'	=> 'Сумма, USD',
			'value' => function ($model) {
				return number_format($model->amount, 0, '.', ' ');
			},
			'format' => 'raw',
		],
		[
			'attribute' => 'paybacksum',
			'label'	=> 'Моя прибыль (%)',
			'value' => function ($model) {
				if ((float)$model->paybacksum) {
					return number_format($model->paybacksum, 0, '.', ' ') . ' <span style="color:#828282; font-size: 75%">(' . $model->percent . '%)</span>';
				} else {
					return '-';
				}
			},
			'format' => 'raw',
		],
	],
    'pager' => [
        'nextPageLabel' => '<div class="privateOffice-nav-next"></div>', // стрелочка вправо
        'prevPageLabel' => '<div class="privateOffice-nav-prev"></div>', // стрелочка влево
    ],
]); ?>
<? Pjax::end(); ?>