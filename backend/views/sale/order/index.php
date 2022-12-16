<?php

use app\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('order', 'Orders');
$this->params['breadcrumbs'][] = $this->title;

$jsCode = <<<JS
    $('.batch-delete').on('click', function () {
        let keys = $('#grid').yiiGridView('getSelectedRows').join(',');
        if (keys) { 
            yii.confirm('Вы уверены, что хотите удалить выбранные заказы?', function() {
                location.href = '/admin/order/batchdelete?selection='+keys;
            })
        }
    }); 
JS;
$this->registerJs($jsCode, View::POS_READY);

?>
<div class="speciality-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <hr />
    <p>
        <?php  $search = Yii::$app->request->get();
        $search = $search['OrderSearch'];
        $create_opts = ['create'];
        if (isset($search['order_status_id']) && $search['order_status_id']) {
            $create_opts['order_status_id'] = $search['order_status_id'];
        }
        if (isset($search['customer_id']) && $search['customer_id']) {
            $create_opts['customer_id'] = $search['customer_id'];
        }
        ?>
        <?//= Html::a(YII::t('app', 'Add new'), $create_opts, ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Delete selected'), false, ['class' => 'btn btn-warning batch-delete', 'data-pjax' => 0]) ?>
    </p>

    <div class="table-responsive">
    <?= GridView::widget([
        'id' => 'grid',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($data) {
                    return ['value' => $data->id];
                }
            ],
            'id',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return html::a(date('d.m.Y h:i', $model->created_at),['view', 'id' => $model->id], ['data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'products',
                'value' => function ($model) {
                    return $model->products . ' шт';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'total',
                'value' => function ($model) {
                    return $model->total;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'delivery_method',
                'value' => function ($model) {
                    return $model->delivery_method;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'payment_method',
                'value' => function ($model) {
                    return $model->payment_method;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'customer',
                'value' => function ($data) {
                    return html::a($data->customer, Url::to(['customer/update', 'id' => $data->customer_id]), ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                  $is_bright = Helper::isColorBright($data->status_color);
                  return '<span class="label" style="background-color: #' . $data->status_color . '; color: ' . ($is_bright ? '#222' : '#fff') . '">' . $data->status . '</span>';
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}&nbsp;&nbsp;&nbsp;{delete}',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
        ],
    ]); ?>
    </div>
</div>
