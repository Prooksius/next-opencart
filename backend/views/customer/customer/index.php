<?php

use app\models\CustomerBot;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="customer-index">

  <?php echo $this->render('_search', ['model' => $searchModel, 'mode' => $mode]); ?>

  <p>
    <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success'], ['data-pjax' => 0]) ?>
  </p>

  <div style="overflow: auto; overflow-y: hidden;">
    <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'columns' => [
        [
          'label' => 'Фото',
          'headerOptions' => ['class' => 'text-left'],
          'contentOptions' => ['class' => 'text-left'],
          'value' => function ($data) {
            if ($data->picture) {
              return Html::a(
                EasyThumbnailImage::thumbnailImg(
                  '@root' . $data->picture,
                  25,
                  25,
                  EasyThumbnailImage::THUMBNAIL_INSET,
                  ['class' => 'img-responsive', 'style' => ['max-width' => '50px', 'border-radius' => '50%']]
                ),
                ['update', 'id' => $data->id],
                ['data-pjax' => 0]
              );
            } else {
              return Html::a(Html::img('/backend/components/no_avatar.png', ['class' => 'img-responsive', 'style' => ['max-width' => '25px', 'border-radius' => '50%']]), Url::to(['customer/update', 'id' => $data->id]), ['data-pjax' => 0]);
            }
          },
          'format' => 'raw',
        ],
        [
          'attribute' => 'created_at',
          'value' => function ($model) {
            return date('d.m.Y', $model->created_at);
          },
        ],
        [
          'attribute' => 'username',
          'value' => function ($data) {
            return html::a($data->username, ['update', 'id' => $data->id], ['data-pjax' => 0]);
          },
          'format' => 'raw',
        ],
        [
          'attribute' => 'email',
        ],
        [
          'attribute' => 'status',
          'headerOptions' => ['class' => 'text-center'],
          'contentOptions' => ['class' => 'text-center'],
          'value' => function ($data) {
            $html  = '<span class="label label-' . ($data->status == 10 ? 'success' : 'danger') . '">' . ($data->status == 10 ? 'Активен' : 'Отключен') . '</span>';
            $html .= html::beginForm(Url::to(['customer/customer/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;', 'data-on' => 10, 'data-off' => 0, 'data-texton' => 'Активен', 'data-textoff' => 'Отключен']);
            $html .= html::activeCheckbox($data, 'status', ['class' => 'grid-editable', 'label' => false, 'value' => 10]);
            $html .= html::endForm();
            return $html;
          },
          'format' => 'raw',
          'headerOptions' => ['class' => 'text-right'],
          'contentOptions' => ['class' => 'text-right'],
        ],
        [
          'attribute' => 'email_confirmed',
          'headerOptions' => ['class' => 'text-center'],
          'contentOptions' => ['class' => 'text-center'],
          'value' => function ($data) {
            return '<span class="label label-' . ($data->email_confirmed == 1 ? 'success' : 'danger') . '">' . ($data->email_confirmed == 1 ? 'Да' : 'Нет') . '</span>';
          },
          'format' => 'raw',
          'headerOptions' => ['class' => 'text-right'],
          'contentOptions' => ['class' => 'text-right'],
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'template' => '{update}&nbsp;&nbsp;&nbsp;{delete}',
          'headerOptions' => ['class' => 'text-right'],
          'contentOptions' => ['class' => 'text-right'],
        ]
      ],
    ]); ?>
  </div>
</div>