<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('product', 'Additional images for product') . ': ' . $product_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('product', 'Products'), 'url' => ['/catalog/product']];
$this->params['breadcrumbs'][] = Yii::t('product', 'Additional images for product');
?>
<div class="reviews-index">

  <p>
      <?= Html::a(Yii::t('product', 'Add Image'), ['create', 'product_id' => $product_id], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'class' => 'yii\grid\CheckboxColumn',
        'headerOptions' => ['style' => 'width: 1px'],
      ],
      [
        'label' => YII::t('app', 'Image'),
        'headerOptions' => ['class' => 'text-left', 'style' => 'width: 100px'],
        'contentOptions' => ['class' => 'text-left', 'style' => 'width: 100px'],
        'value' => function ($data) {
          return html::a($data->image ? EasyThumbnailImage::thumbnailImg(
            '@root' . $data->image,
            50,
            50,
            EasyThumbnailImage::THUMBNAIL_INSET,
            ['class' => 'img-responsive thumbnail']
          ) : '<img class="thumbnail" src="/upload/image/placeholder.png" style="width: 60px" />', ['update', 'id' => $data->id]);
        },
        'format' => 'raw',
      ],
      [
          'attribute' => 'sort_order',
          'headerOptions' => ['class' => 'text-right'],
          'contentOptions' => ['class' => 'text-right'],
          'value' => function ($data) {
              $html  = html::beginForm(Url::to(['catalog/product-image/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
              $html .= html::activeTextInput($data, 'sort_order', ['type' => 'number', 'class' => 'form-control grid-editable', 'label' => false]);
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
