<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Раздел FAQ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

    <p>
        <?= Html::a('Добавить вопрос', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'text-left wrapped'],
                'contentOptions' => ['class' => 'text-left wrapped'],
                'value' => function ($data) {
                    return html::a($data->name, ['update', 'id' => $data->id]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'sort_order',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    $html  = html::beginForm(Url::to(['catalog/faq/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeTextInput($data, 'sort_order', ['type' => 'number', 'class' => 'form-control grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $html  = '<span class="label label-' . ((bool)$data->status ? 'success' : 'danger') . '">' . ((bool)$data->status ? 'Да' : 'Нет') . '</span>';
                    $html .= html::beginForm(Url::to(['catalog/faq/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeCheckbox($data, 'status', ['class' => 'grid-editable', 'label' => false]);
                    $html .= html::endForm();
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
            ],
        ],
    ]); ?>
</div>
