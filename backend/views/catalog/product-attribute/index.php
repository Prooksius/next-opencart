<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('product', 'Attributes for product') . ': ' . $product_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Products'), 'url' => ['/catalog/product']];
$this->params['breadcrumbs'][] = Yii::t('product', 'Attributes for product');
?>
<div class="reviews-index">

  <p>
      <?= Html::a(Yii::t('product', 'Add product attribute'), ['create', 'product_id' => $product_id], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'class' => 'yii\grid\CheckboxColumn',
        'headerOptions' => ['style' => 'width: 1px'],
      ],
      [
        'attribute' => 'attributeName',
        'headerOptions' => ['class' => 'text-left', 'style' => 'width: 100px'],
        'contentOptions' => ['class' => 'text-left', 'style' => 'width: 100px'],
        'value' => function ($data) {
          return html::a($data->attributeName, ['update', 'product_id' => $data->product_id, 'attribute_id' => $data->attribute_id, 'language_id' => $data->language_id]);
        },
        'format' => 'raw',
      ],
      'text',
      'alias',
      [
        'class' => 'yii\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
      ],
    ],
  ]); ?>
</div>
