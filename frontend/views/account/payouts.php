<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 27.04.2020
 * Time: 14:38
 */

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;

$this->title = 'Выводы | Личный кабинет | ' . $seo['meta_title'];

$balans = $totals['paybacksum']- $totals['payoutsum'];
?>

<section class="privateOffice<?= ($is_partner ? ' privateOffice1' : '') ?>">
    <div class="container">
        <?= $this->render('account-menu'); ?>
        <div class="balans">
            <div class="balans-container">
                <div class="balans-wrap1">
                    <div class="balans-info">
                        <div class="balans-info-wrap">
                            <span>Баланс</span>
                            <b><?= number_format($balans, 0, '.', ' ') ?> USD</b>
                        </div>
                        <? if ((float)$balans) { ?>
                        <?= Html::button('Запрос на выплату', ['class' => 'btn2 form-popup', 'data-target' => '/account/payout-request-popup'])?>
                        <? } ?>
                    </div>
                </div>
                <div class="balans-wrap2">

                    <?php Pjax::begin(['enablePushState' => false]); ?>
                    <?= GridView::widget([
                        'dataProvider' => $payouts,
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
                                'attribute' => 'amount',
                                'value' => function ($model) {
                                    return number_format($model->amount, 0, '.', ' ');
                                },
                                'format' => 'raw',
								'contentOptions' => ['data-label' => 'Сумма, USD'],
                            ],
                            [
                                'attribute' => 'status',
                                'headerOptions' => ['class' => 'text-right'],
                                'contentOptions' => ['class' => 'text-right'],
                                'value' => function ($data) {
                                    $html  = '<span style="color: ' . ($data->status == 1 ? 'green' : ($data->status == 2 ? 'red' : 'orange')) . '">' . ($data->status == 1 ? 'Выполнено' : ($data->status == 2 ? 'Отказано' : 'На рассмотрении')) . '</span>';
                                    return $html;
                                },
                                'format' => 'raw',
								'contentOptions' => ['data-label' => 'Статус'],
                            ],
                        ],
                        'pager' => [
                            'nextPageLabel' => '<div class="privateOffice-nav-next"></div>', // стрелочка вправо
                            'prevPageLabel' => '<div class="privateOffice-nav-prev"></div>', // стрелочка влево
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
