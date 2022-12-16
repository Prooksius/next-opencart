<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('product', 'Product options') . ': ' . $product_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Products'), 'url' => ['/catalog/product']];
$this->params['breadcrumbs'][] = Yii::t('product', 'Product options');
?>
<div class="reviews-index">

  <p>
      <?= Html::a(Yii::t('product', 'Add product option'), ['create', 'product_id' => $product_id], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'class' => 'yii\grid\CheckboxColumn',
        'headerOptions' => ['style' => 'width: 1px'],
      ],
      [
        'attribute' => 'optionName',
        'value' => function ($model) {
          if (!in_array($model->optionType, ['select', 'radio', 'checkbox'])) {
            return Html::a($model->optionName, ['update', 'id' => $model->id]);
          } else {
            return $model->optionName;
          }
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'value', //optionType
        'label' => Yii::t('app', 'Value(s)'),
        'value' => function ($model) {
          if (in_array($model->optionType, ['select', 'radio', 'checkbox'])) {
            return Html::a(Yii::t('app', 'Open'), ['/catalog/product-option-value', 'product_option_id' => $model->id], ['class' => 'btn btn-primary btn-xs']);
          } else {
            return $model->value;
          }
        },
        'format' => 'raw',
      ],
      'optionType',
      [
        'attribute' => 'required',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($data) {
          $html  = '<span class="label label-' . ($data->required == 1 ? 'success' : 'danger') . '">' . ($data->required == 1 ? Yii::t('app', 'Yes') : Yii::t('app', 'No')) . '</span>';
          $html .= html::beginForm(Url::to(['catalog/product-option/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 1, 'data-off' => 0, 'data-texton' => Yii::t('app', 'Yes'), 'data-textoff' => Yii::t('app', 'No')]);
          $html .= html::activeCheckbox($data, 'required', ['class' => 'grid-editable', 'label' => false, 'value' => 1]);
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
        'buttons' => [
          'update' => function($url, $model, $key) {
            if (!in_array($model->optionType, ['select', 'radio', 'checkbox'])) {
              return Html::a('<span class="glyphicon glyphicon-pencil">',$url);
            } else {
              return '';
            }
          }
        ]
      ],
    ],
  ]); ?>
</div>
