<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
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
        <div class="col-md-4 col-sm-4">
            <?= $form->field($model, 'translate_group_id')->dropDownList($model->translateGroupsList, ['prompt' =>  YII::t('app', '--choose value--')])?>
        </div>
        <div class="col-md-4 col-sm-4">
            <?= $form->field($model, 'phrase')->textInput(); ?>
        </div>
        <div class="col-md-4 col-sm-4">
            <?= $form->field($model, 'translate')->textInput(); ?>
        </div>
        <div class="col-md-12 col-sm-12 text-right">
            <br />
            <div class="form-group">
                <?= Html::submitButton(YII::t('app', 'Filter'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(YII::t('app', 'Reset'), Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
