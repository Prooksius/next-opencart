<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('module', 'Featured Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

  <p>
      <?= Html::a(YII::t('module', 'Add module'), ['create'], ['class' => 'btn btn-success']) ?>
  </p>

  <div class="table-responsive">
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'attribute' => 'name',
        'value' => function ($data) {
          return html::a(
            $data->name, 
            ['update', 'id' => $data->id]
          );
        },
        'format' => 'raw',
      ],
      [
        'attribute' => 'status',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($data) {
            $html  = '<span class="label label-' . ((bool)$data->status ? 'success' : 'danger') . '">' . ((bool)$data->status ? YII::t('app', 'Yes') : YII::t('app', 'No')) . '</span>';
            $html .= html::beginForm(Url::to(['update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
            $html .= html::activeCheckbox($data, 'status', ['class' => 'grid-editable', 'label' => false]);
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
</div>
