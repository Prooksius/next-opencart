<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('product', 'Product option values');
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Products'), 'url' => ['/catalog/product']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Product options'), 'url' => ['/catalog/product-option', 'product_id' => $product_id]];
$this->params['breadcrumbs'][] = Yii::t('product', 'Product option values');
?>
<div class="reviews-index">

  <p><b><?= Yii::t('product', 'Product') ?></b>: <?= $product_name ?></p>
  <p><b><?= Yii::t('option', 'Option') ?></b>: <?= $option_name ?></p>
  <p>
      <?= Html::a(Yii::t('product', 'Add product option value'), ['create', 'product_option_id' => $product_option_id], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'grid-view table-responsive'],
    'columns' => [
      [
        'class' => 'yii\grid\CheckboxColumn',
        'headerOptions' => ['style' => 'width: 1px'],
      ],
      'id',
      [
        'attribute' => 'optionValueName',
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'value' => function ($data) {
          $html = html::beginForm(Url::to(['catalog/product-option-value/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;vertical-align: middle;', 'data-reload' => '0']);
          $html .= html::activeDropDownList($data, 'option_value_id', $data->allOptionValues, ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'min-width: 50px']);
          $html .= html::endForm();
          return $html;
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'price',
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'value' => function ($data) {
          $html = html::beginForm(Url::to(['catalog/product-option-value/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;vertical-align: middle;', 'data-reload' => '0']);
          $html .= '<div class="input-group">';
          $html .= ' <span class="input-group-addon" style="padding: 0; border: none;">';
          $html .= html::activeDropDownList($data, 'price_prefix', $data->actionsList, ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'min-width: 50px']);
          $html .= ' </span>';
          $html .= html::activeInput('text', $data, 'price', ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'max-width: 120px; min-width: 80px']);
          $html .= '</div>';
          $html .= html::endForm();
          return $html;
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'weight',
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'value' => function ($data) {
          $html = html::beginForm(Url::to(['catalog/product-option-value/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;vertical-align: middle;', 'data-reload' => '0']);
          $html .= '<div class="input-group">';
          $html .= ' <span class="input-group-addon" style="padding: 0; border: none;">';
          $html .= html::activeDropDownList($data, 'weight_prefix', $data->actionsList, ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'min-width: 50px']);
          $html .= ' </span>';
          $html .= html::activeInput('text', $data, 'weight', ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'max-width: 120px; min-width: 80px']);
          $html .= '</div>';
          $html .= html::endForm();
          return $html;
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'points',
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'value' => function ($data) {
          $html = html::beginForm(Url::to(['catalog/product-option-value/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;vertical-align: middle;', 'data-reload' => '0']);
          $html .= '<div class="input-group">';
          $html .= ' <span class="input-group-addon" style="padding: 0; border: none;">';
          $html .= html::activeDropDownList($data, 'points_prefix', $data->actionsList, ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'min-width: 50px']);
          $html .= ' </span>';
          $html .= html::activeInput('text', $data, 'points', ['class' => 'form-control grid-editable', 'label' => false, 'style' => 'max-width: 120px; min-width: 80px']);
          $html .= '</div>';
          $html .= html::endForm();
          return $html;
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'quantity',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
          $html  = html::beginForm(Url::to(['catalog/product-option-value/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
          $html .= html::activeTextInput($data, 'quantity', ['type' => 'number', 'class' => 'form-control grid-editable', 'label' => false, 'style' => 'max-width: 100px']);
          $html .= html::endForm();
          return $html;
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'subtract',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($data) {
          $html  = '<span class="label label-' . ($data->subtract == 1 ? 'success' : 'danger') . '">' . ($data->subtract == 1 ? Yii::t('app', 'Yes') : Yii::t('app', 'No')) . '</span>';
          $html .= html::beginForm(Url::to(['catalog/product-option-value/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 1, 'data-off' => 0, 'data-texton' => Yii::t('app', 'Yes'), 'data-textoff' => Yii::t('app', 'No')]);
          $html .= html::activeCheckbox($data, 'subtract', ['class' => 'grid-editable', 'label' => false, 'value' => 1]);
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
