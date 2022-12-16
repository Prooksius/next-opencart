<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('layout', 'Layouts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index">

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'name',
                'value' => function ($data) {
                    return html::a($data->name, ['update', 'id' => $data->id]);
                },
                'format' => 'raw',
            ],
            'instances'
        ],
    ]); ?>
    </div>
</div>
