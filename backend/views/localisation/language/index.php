<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('app', 'Languages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

    <p>
        <?= Html::a(YII::t('app', 'Add language'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'value' => function ($data) {
                    return html::a(Html::tag('span', '', ['class' => 'flag inline flag-' . $data->code]) . ' ' . 
                      $data->name . ($data->isDefault() ? ' (<b>' . Yii::t('app', 'Default') . '</b>)' : ''), ['update', 'id' => $data->id]);
                },
                'format' => 'raw',
            ],
            'locale',
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
