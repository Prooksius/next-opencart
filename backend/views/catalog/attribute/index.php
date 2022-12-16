<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('attribute', 'Attributes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

    <p>
        <?= Html::a(Yii::t('attribute', 'Add attribute'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'group_name',
                'headerOptions' => ['class' => 'text-left wrapped'],
                'contentOptions' => ['class' => 'text-left wrapped'],
                'value' => function ($data) {
                    return html::a($data->group_name, ['catalog/attribute-group/update', 'id' => $data->attribute_group_id]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'sort_order',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    $html  = html::beginForm(['update', 'id' => $data->id], 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeTextInput($data, 'sort_order', ['type' => 'number', 'class' => 'form-control grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'filter_sort_order',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($data) {
                    $html  = html::beginForm(['update', 'id' => $data->id], 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeTextInput($data, 'filter_sort_order', ['type' => 'number', 'class' => 'form-control grid-editable', 'label' => false]);
                    $html .= html::endForm();
                    return $html;
                },
                'format' => 'raw',
            ],
            [
              'attribute' => 'show_filter',
              'headerOptions' => ['class' => 'text-center'],
              'contentOptions' => ['class' => 'text-center'],
              'value' => function ($data) {
                $html  = '<span class="label label-' . ($data->show_filter == 1 ? 'success' : 'danger') . '">' . ($data->show_filter == 1 ? 'Да' : 'Нет') . '</span>';
                $html .= html::beginForm(Url::to(['ctalog/attribute/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 1, 'data-off' => 0, 'data-texton' => 'Да', 'data-textoff' => 'Нет']);
                $html .= html::activeCheckbox($data, 'show_filter', ['class' => 'grid-editable', 'label' => false, 'value' => 1]);
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
