<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('blog', 'Blog Articles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

  <?= $this->render('_search', ['model' => $searchModel]); ?>
  <p>
      <?= Html::a(YII::t('blog', 'Add Blog Article'), ['create'], ['class' => 'btn btn-success']) ?>
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
              <li>'.html::a(Yii::t('blog', 'Article images') . ((int)$data->image_count > 0 ? '<span class="badge">' . $data->image_count . '</span>' : ''), ['catalog/blog-article-image/index', 'article_id' => $data->id]).'</li>
            </ul>
          </div>';
        },
        'format' => 'raw',
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
        'attribute' => 'featured',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($data) {
          $html  = '<span class="label label-' . ($data->featured == 1 ? 'success' : 'danger') . '">' . ($data->featured == 1 ? Yii::t('app', 'Yes') : Yii::t('app', 'No')) . '</span>';
          $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 1, 'data-off' => 0, 'data-texton' => Yii::t('app', 'Yes'), 'data-textoff' => Yii::t('app', 'No')]);
          $html .= html::activeCheckbox($data, 'featured', ['class' => 'grid-editable', 'label' => false, 'value' => 1]);
          $html .= html::endForm();
          return $html;
        },
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
      ],
      [
        'attribute' => 'status',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($data) {
          $html  = '<span class="label label-' . ($data->status == 1 ? 'success' : 'danger') . '">' . ($data->status == 1 ? Yii::t('app', 'Active') : Yii::t('app', 'Inactive')) . '</span>';
          $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 1, 'data-off' => 0, 'data-texton' => Yii::t('app', 'Active'), 'data-textoff' => Yii::t('app', 'Inactive')]);
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
