<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ServiceSearch */
/* @var $form yii\widgets\ActiveForm */

$signs = ['=' => '=', '>' => '>', '<' => '<', '>=' => '>=', '<=' => '<=', '!=' => '<>'];

?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-lg-10 col-md-10 col-sm-9">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label>Популярность</label>
                    <div class="flex-cont">
                        <?= $form->field($model, 'popularity_sign')->dropDownList($signs)->label(false) ?>
                        <?= $form->field($model, 'popularity')->label(false) ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <?= $form->field($model, 'status')->dropDownList([0 => 'Отключена', 1 => 'Активна'], ['prompt' => '-- выберите --']) ?>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 text-right">
            <br />
            <div class="form-group">
                <?= Html::submitButton(YII::t('app', 'Filter'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(YII::t('app', 'Reset'), ['country/index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
