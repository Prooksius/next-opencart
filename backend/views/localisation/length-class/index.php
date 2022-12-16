<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('localisation', 'Length classes');;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reviews-index">

  <p>
    <?= Html::a(Yii::t('localisation', 'Add length class'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'attribute' => 'title',
        'headerOptions' => ['class' => 'text-left wrapped'],
        'contentOptions' => ['class' => 'text-left wrapped'],
        'value' => function ($data) {
          return html::a($data->title, ['update', 'id' => $data->id]);
        },
        'format' => 'raw',
      ],
      'unit',
      'value',
      [
        'class' => 'yii\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
      ],
    ],
  ]); ?>
</div>
