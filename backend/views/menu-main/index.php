<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Главное меню';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="menu-about-index">

    <p>
        <?= Html::a('Добавить новый пункт меню', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'title',
                'value' => function ($data) {
                  return html::a($data->name, ['update', 'id' => $data->id]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'link',
                'value' => function ($data) {
                    $html  = html::beginForm(['update', 'id' => $data->id]);
                    $html .= html::activeTextInput($data, 'link', ['class' => 'form-control grid-editable', 'label' => false]);
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
                    $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeCheckbox($data, 'status', ['class' => 'grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'top_status',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $html  = '<span class="label label-' . ((bool)$data->top_status ? 'success' : 'danger') . '">' . ((bool)$data->top_status ? 'Да' : 'Нет') . '</span>';
                    $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeCheckbox($data, 'top_status', ['class' => 'grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'bottom_status',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $html  = '<span class="label label-' . ((bool)$data->bottom_status ? 'success' : 'danger') . '">' . ((bool)$data->bottom_status ? 'Да' : 'Нет') . '</span>';
                    $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeCheckbox($data, 'bottom_status', ['class' => 'grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'sort_order',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    $html  = html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeTextInput($data, 'sort_order', ['class' => 'form-control grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
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
</div>
