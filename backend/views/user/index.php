<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Аккаунт-менеджеры';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-index">

    <p>
        <?= Html::a('Создать аккаунт-менеджера', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div style="overflow: auto; overflow-y: hidden;">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'username',
                'value' => function ($data) {
                    return html::a($data->username, Url::to(['user/update', 'id' => $data->id]), ['data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'email',
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('d.m.Y г. в G:i', $model->created_at);
                },
            ],
            [
                'attribute' => 'status',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $html  = '<span class="label label-' . ($data->status == 10 ? 'success' : 'danger') . '">' . ($data->status == 10 ? 'Активен' : 'Отключен') . '</span>';
                    return $html;
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ]        ],
    ]); ?>
    </div>
</div>
