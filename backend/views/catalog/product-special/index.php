<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('product', 'Special prices') . ': ' . $product_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Products'), 'url' => ['/catalog/product']];
$this->params['breadcrumbs'][] = Yii::t('product', 'Special prices');
?>
<div class="reviews-index">

  <p>
      <?= Html::a(Yii::t('product', 'Add special price'), ['create', 'product_id' => $product_id], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'class' => 'yii\grid\CheckboxColumn',
        'headerOptions' => ['style' => 'width: 1px'],
      ],
      [
        'attribute' => 'customer_group',
        'value' => function ($model) {
          return Html::a($model->customer_group, ['update', 'id' => $model->id]);
        },
        'format' => 'raw',
      ],
      'priority',
      [
        'attribute' => 'price',
        'value' => function ($model) {
          return $model->price;
        },
      ],
      [
        'attribute' => 'date_start',
        'value' => function ($model) {
          return (int)$model->date_start ? date('d.m.Y H:i', $model->date_start) : '';
        },
      ],
      [
        'attribute' => 'date_end',
        'value' => function ($model) {
          return (int)$model->date_end ? date('d.m.Y H:i', $model->date_end) : '';
        },
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
