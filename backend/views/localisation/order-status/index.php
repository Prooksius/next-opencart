<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('localisation', 'Order statuses');;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

    <p>
        <?= Html::a(Yii::t('localisation', 'Add order status'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'color',
                'value' => function ($data) {
                    return html::tag('span', '', ['style' => 'display: block; width: 50px; height: 30px; background-color: #' . $data->color]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'text-left wrapped'],
                'contentOptions' => ['class' => 'text-left wrapped'],
                'value' => function ($data) {
                    return html::a($data->name . ($data->isDefault() ? ' (<b>' . Yii::t('app', 'Default') . '</b>)' : ''), ['update', 'id' => $data->id]);
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
        ],
    ]); ?>
</div>
