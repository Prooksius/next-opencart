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

$this->title = 'Платежи | Личный кабинет | ' . $seo['meta_title'];

?>
<section class="privateOffice<?= ($is_partner ? ' privateOffice1' : '') ?>">
    <div class="container">
        <?= $this->render('account-menu'); ?>
        <div class="accruals">
            <div class="table3-container">
                <? Pjax::begin(['enablePushState' => false]); ?>
                <?= $this->render('_search', ['model' => $clientsSearchModel, 'levels' => $levels, 'payments' => $payments, 'sum' => $sum, 'payoutsum' => $payoutsum, 'paybacksum' => $paybacksum]); ?>
                <div class="accruals-wrap333">
                    <?= GridView::widget([
                        'dataProvider' => $payments,
						'tableOptions' => ['class' => 'table-3 smalltable-3'],
                        'columns' => [
                            [
                                'attribute' => 'created_at',
                                'value' => function ($model) {
                                    return date('d.m.Y г. в G:i', $model->created_at);
                                },
                                'format' => 'raw',
								'contentOptions' => ['data-label' => 'Дата платежа'],
                            ],
                            [
                                'attribute' => 'username',
                                'value' => function ($model) {
                                    return ($model->customer_id > 0 ? $model->username : ($model->cust_name ? $model->cust_name : '<span style="color: grey">(не задано)</span>'));
                                },
                                'format' => 'raw',
								'contentOptions' => ['data-label' => 'Пользователь'],
                            ],
                            [
								'attribute' => 'botname',
								'contentOptions' => ['data-label' => 'Счет'],
                            ],
                            [
								'attribute' => 'level',
								'contentOptions' => ['data-label' => 'Уровень'],
                            ],
                            [
                                'attribute' => 'amount',
                                'value' => function ($model) {
                                    return number_format($model->amount, 0, '.', ' ');
                                },
                                'format' => 'raw',
								'contentOptions' => ['data-label' => 'Сумма, USD'],
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
								'contentOptions' => ['data-label' => 'Моя прибыль (%)'],
                            ],
                        ],
                        'pager' => [
                            'nextPageLabel' => '<div class="privateOffice-nav-next"></div>', // стрелочка вправо
                            'prevPageLabel' => '<div class="privateOffice-nav-prev"></div>', // стрелочка влево
                        ],
                    ]); ?>
                </div>
                <? Pjax::end(); ?>
            </div>
        </div>
    </div>
</section>

