<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('category', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

  <p>
      <?= Html::a(YII::t('category', 'Add Category'), ['create', 'parent_id' => $parent_id], ['class' => 'btn btn-success']) ?>
      <?php if ($up_level != -1) { ?>
        <?= Html::a(YII::t('category', 'Level Up'), ['index', 'parent_id' => $up_level], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(YII::t('category', 'Top level'), ['index'], ['class' => 'btn btn-warning']) ?>
      <?php } else { ?>
        <?= Html::a(YII::t('category', 'Repair Categories'), ['repair'], ['class' => 'btn btn-warning']) ?>
      <?php } ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
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
            ['class' => 'img-responsive']
          ) : '<img src="/upload/image/placeholder.png" style="width: 50px" />', ['update', 'id' => $data->id], ['title' => YII::t('category', 'Edit Category')]);
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'name',
        'value' => function ($data) {
          return html::a(
            $data->name . ' <i class="fa fa-level-down"></i>' , 
            ['index', 'parent_id' => $data->id], 
            ['title' => YII::t('category', 'Category Childs')]
          );
        },
        'format' => 'raw',
      ],
      'child_count',
      [
        'attribute' => 'sort_order',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function ($data) {
          $html  = html::beginForm(Url::to(['catalog/category/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
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
