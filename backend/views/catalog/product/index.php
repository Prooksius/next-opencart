<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('product', 'Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

  <?= $this->render('_search', ['model' => $searchModel]); ?>
  <p>
      <?= Html::a(YII::t('product', 'Add Product'), ['create'], ['class' => 'btn btn-success']) ?>
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
            40,
            40,
            EasyThumbnailImage::THUMBNAIL_INSET,
            ['class' => 'img-responsive thumbnail']
          ) : '<img class="thumbnail" src="/upload/image/placeholder.png" style="width: 60px" />', ['update', 'id' => $data->id]);
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'name',
        'value' => function ($data) {
          return html::a($data->name, ['update', 'id' => $data->id]);
        },
        'format' => 'raw',
      ],
      'model',
      [
        'label' => Yii::t('app', 'Params'),
        'headerOptions' => ['class' => 'text-left wrapped'],
        'contentOptions' => ['class' => 'text-left wrapped'],
        'value' => function ($data) {
          return 
          '<div class="dropdown">
            <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
              Yii::t('app', 'Open') . '
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu properties-list">
              <li>'.html::a(Yii::t('product', 'Product attributes') . ((int)$data->attribute_count > 0 ? '<span class="badge">' . $data->attribute_count . '</span>' : ''), ['catalog/product-attribute/index', 'product_id' => $data->id]).'</li>
              <li>'.html::a(Yii::t('product', 'Product options') . ((int)$data->option_count > 0 ? '<span class="badge">' . $data->option_count . '</span>' : ''), ['catalog/product-option/index', 'product_id' => $data->id]).'</li>
              <li>'.html::a(Yii::t('product', 'Product filters') . ((int)$data->filter_count > 0 ? '<span class="badge">' . $data->filter_count . '</span>' : ''), ['catalog/product-filter/index', 'product_id' => $data->id]).'</li>
              <li>'.html::a(Yii::t('product', 'Product images') . ((int)$data->image_count > 0 ? '<span class="badge">' . $data->image_count . '</span>' : ''), ['catalog/product-image/index', 'product_id' => $data->id]).'</li>
              <li>'.html::a(Yii::t('product', 'Product actions') . ((int)$data->special_count > 0 ? '<span class="badge">' . $data->special_count . '</span>' : ''), ['catalog/product-special/index', 'product_id' => $data->id]).'</li>
              <li>'.html::a(Yii::t('product', 'Product discounts') . ((int)$data->discount_count > 0 ? '<span class="badge">' . $data->discount_count . '</span>' : ''), ['catalog/product-discount/index', 'product_id' => $data->id]).'</li>
            </ul>
          </div>';
        },
        'format' => 'raw',
      ],
      'price',
      'quantity',
      [
        'attribute' => 'sort_order',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
          $html  = html::beginForm(Url::to(['catalog/product/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
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
          $html  = '<span class="label label-' . ($data->status == 1 ? 'success' : 'danger') . '">' . ($data->status == 1 ? Yii::t('app', 'Active') : Yii::t('app', 'Inactive')) . '</span>';
          $html .= html::beginForm(Url::to(['catalog/product/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 1, 'data-off' => 0, 'data-texton' => Yii::t('app', 'Active'), 'data-textoff' => Yii::t('app', 'Inactive')]);
          $html .= html::activeCheckbox($data, 'status', ['class' => 'grid-editable', 'label' => false, 'value' => 1]);
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
