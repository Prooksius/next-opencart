<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $modelCustomer app\modules\yii2extensions\models\Customer */
/* @var $modelsAddress app\modules\yii2extensions\models\Address */

$js = <<<JS
    jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
            jQuery(this).html("Address: " + (index + 1))
        });
    });

    jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
        jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
            jQuery(this).html("Address: " + (index + 1))
        });
    });
    
    
    $(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
    });

    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        console.log("afterInsert");
    });
    
    $(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
        if (! confirm("Are you sure you want to delete this item?")) {
            return false;
        }
        return true;
    });
    
    $(".dynamicform_wrapper").on("afterDelete", function(e) {
        console.log("Deleted item!");
    });
    
    $(".dynamicform_wrapper").on("limitReached", function(e, item) {
        alert("Limit reached");
    });
JS;

$this->registerJs($js);
?>


<div class="payout-index">

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['class' => ($model->status == 0 ? 'oranged' : '')];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'percent',
                'value' => function ($model) {
                    return $model['percent'] . ' %';
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'percent_text',
            ],
            [
                'attribute' => 'sort',
            ],
            [
                'attribute' => 'created_at',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-trash',
                            'style' => 'font-size: 20px;',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить этот элемент ?',
                            ],
                        ]);
                    },

                ],
                'template' => '{delete}',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


<div class="customer-form">

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 1, // the maximum times, an element can be cloned (default 999)
        'min' => 0, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $modelsAddress[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'percent',
            'percent_text'
        ],
    ]); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-envelope"></i> Блок процентов
            <button type="button" class="pull-right add-item btn btn-success btn-xs"><i class="fa fa-plus"></i>
                Добавит блок процента
            </button>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body container-items">
            <?php foreach ($modelsAddress as $index => $modelAddress): ?>
                <div class="item panel panel-default">
                    <div class="panel-heading">
                        <button type="button" class="pull-right remove-item btn btn-danger btn-xs"><i
                                    class="fa fa-minus"></i></button>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$index}]percent")->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($modelAddress, "[{$index}]sort")->input('number',['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-8">
                                <?= $form->field($modelAddress, "[{$index}]percent_text")->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php DynamicFormWidget::end(); ?>
</div>