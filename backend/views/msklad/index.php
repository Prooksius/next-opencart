<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Товары Моего Склада';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bot-index">

    <p>
        <?= Html::a('Создать товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group left_catalog">
                <label style="font-size: 12px;">Фильтр по группе товаров:</label>
                <?= Html::a('Все товары <span class="pull-right slide_arrow"><i class="fa fa-angle-right opened"></i></span>', ['index'], ['class' => 'list-group-item header children opened selected']) ?>
                <div class="child_items">
                    <? foreach ($groups as $key => $category) { ?>
                        <? if ($key == 'href') { continue; } ?>
                        <? $active = ((isset($path_arr[0]) && $path_arr[0] == $key) ? ' selected' : ''); ?>
                        <? if (count($category)) { ?>
                            <? $opened =  ((isset($path_arr[0]) && $path_arr[0] == $key) ? ' opened' : ''); ?>
                            <?= Html::a($key.'<span class="pull-right slide_arrow"><i class="fa fa-angle-right' . $opened . '"></i></span>', ['index', 'group_filter' => $key], ['class' => 'list-group-item level_1 children' . $opened . $active]) ?>
                        <? } else { ?>
                            <?= Html::a($key, ['index', 'group_filter' => $key], ['class' => 'list-group-item level_1']) ?>
                        <? } ?>
                        <? if (count($category)) { ?>
                            <div class="child_items" <?php echo ((isset($path_arr[0]) && $path_arr[0] == $key) == 1 ? '' : 'style="display:none;"'); ?>>
                                <? foreach ($category as $key2 => $child) { ?>
                                    <? if ($key2 == 'href') { continue; } ?>
                                    <? $active2 =  ((isset($path_arr[1]) && $path_arr[0] == $key && $path_arr[1] == $key2) ? ' selected' : ''); ?>
                                    <? if (count($child) > 1) { ?>
                                        <? $opened2 =  ((isset($path_arr[1]) && $path_arr[0] == $key && $path_arr[1] == $key2) ? ' opened' : ''); ?>
                                        <?= Html::a($key2.'<span class="pull-right slide_arrow"><i class="fa fa-angle-right' . $opened2 . '"></i></span>', ['index', 'group_filter' => $key . '/' . $key2], ['class' => 'list-group-item level_2 children' . $opened2 . $active2]) ?>
                                    <? } else { ?>
                                        <?= Html::a($key2, ['index', 'group_filter' => $key . '/' . $key2], ['class' => 'list-group-item level_2']) ?>
                                    <? } ?>
                                    <? if (count($child) > 1) { ?>
                                        <div class="child_items" <?php echo ((isset($path_arr[1]) && $path_arr[0] == $key && $path_arr[1] == $key2) ? '' : 'style="display:none;"'); ?>>
                                            <? foreach ($child as $key3 => $child2) { ?>
                                                <? if ($key3 == 'href') { continue; } ?>
                                                <? $active3 =  ((isset($path_arr[2]) && $path_arr[0] == $key && $path_arr[1] == $key2 && $path_arr[2] == $key3) ? ' selected' : ''); ?>
                                                <?= Html::a($key3, ['index', 'group_filter' => $key . '/' . $key2 . '/' . $key3], ['class' => 'list-group-item level_3' . $active3]) ?>
                                            <? } ?>
                                        </div>
                                    <? } ?>
                                <? } ?>
                            </div>
                        <? } ?>
                    <? } ?>
                </div></a>
            </div>
        </div>
        <div class="col-sm-9">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'miniature',
                        'value' => function ($data) {
                            return Html::img($data['miniature']);
                        },
                        'format' => 'raw',
                    ],
                    'id',
                    'name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
