<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('option', 'Options');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

  <p>
    <?= Html::a(Yii::t('option', 'Add Option'), ['create'], ['class' => 'btn btn-success']) ?>
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
        'label' => Yii::t('option', 'Option Values'),
          'headerOptions' => ['class' => 'text-left wrapped'],
          'contentOptions' => ['class' => 'text-left wrapped'],
          'value' => function ($data) {
            if (in_array($data->type, ['select', 'radio', 'checkbox'])) {
              return html::a(Yii::t('app', 'Open'), ['catalog/option-value/index', 'option_id' => $data->id], ['class' => 'btn btn-primary btn-xs']);
            } else {
              return '';
            }
          },
          'format' => 'raw',
      ],
      [
        'attribute' => 'sort_order',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
          $html  = html::beginForm(Url::to(['catalog/option/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
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
          $html  = html::beginForm(Url::to(['catalog/option/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
          $html .= html::activeTextInput($data, 'filter_sort_order', ['type' => 'number', 'class' => 'form-control grid-editable', 'label' => false]);
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
