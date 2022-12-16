<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Inflector;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('module', 'Payment');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

  <div class="table-responsive">
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'attribute' => 'name',
        'value' => function ($data) {
          return html::a(
            $data->name, 
            ['/extension/payment/' . Inflector::camel2id($data->code) . '/update']
          );
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'single',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($data) {
          return '<span class="label label-' . (!(bool)$data->single ? 'success' : 'warning') . '">' . ((bool)$data->single ? 'Нет' : 'Да') . '</span>';
        },
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
      ],
      [
        'attribute' => 'sort_order',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
          $html  = html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
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
          $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
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
        'template'=>'{update}',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
      ],
    ],
  ]); ?>
  </div>
</div>
