<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользовательские сообщения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payout-index">

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php  $search = Yii::$app->request->get();
        $search = $search['MessageSearch'];
        $create_opts = ['create'];
        if (isset($search['customer_id']) && $search['customer_id']) {
            $create_opts['customer_id'] = $search['customer_id'];
        }
        ?>
        <?= Html::a('Создать сообщение', $create_opts, ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['class' => ($model->viewed == 0 ? 'oranged' : '')];
        },
        'columns' => [
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return Html::a(date('d.m.Y г. в G:i', $model->created_at), Url::to(['message/update', 'id' => $model->id]), ['data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'username',
                'value' => function ($model) {
                    return Html::a($model->username, Url::to(['customer/update', 'id' => $model->customer_id]), ['data-pjax' => 0, 'target' => '_blank']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'type',
                'value' => function ($data) {
                    return $data->typeslist[$data->type];
                },
                'format' => 'raw',
            ],
            'title',
            [
                'attribute' => 'viewed',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($data) {
                    $html  = '<span class="label label-' . ((bool)$data->viewed ? 'success' : 'danger') . '">' . ((bool)$data->viewed ? 'Да' : 'Нет') . '</span>';
                    $html .= html::beginForm(Url::to(['message/update', 'id' => $data->id]), 'POST', ['style' => 'display: inline-block;margin-left: 10px;vertical-align: middle;']);
                    $html .= html::activeCheckbox($data, 'viewed', ['class' => 'grid-editable', 'label' => false]);
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
