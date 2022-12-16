<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\components\MyHtml;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

$valueClass = $model->scenario == 'create' ? 'inactive form-control' : 'form-control';

?>


<div class="attribute-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'option_id')
      ->dropDownList($allOptions, ['class' => $model->scenario == 'update' ? 'inactive form-control' : 'form-control']) ?>
    <?php if ($model->scenario == 'update' && $optionType == 'textarea') { ?>
      <?= $form->field($model, 'value')->textarea(['maxlength' => true, 'rows' => 7]) ?>
    <?php } elseif ($model->scenario == 'update' && $optionType == 'datetime') { ?>
      <?= $form->field($model, 'value')->textInput(['type' => 'datetime-local']) ?>
    <?php } elseif ($model->scenario == 'update' && $optionType == 'date') { ?>
      <?= $form->field($model, 'value')->textInput(['type' => 'date']) ?>
    <?php } elseif ($model->scenario == 'update' && $optionType == 'time') { ?>
      <?= $form->field($model, 'value')->textInput(['type' => 'time']) ?>
    <?php } else { ?>
      <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'class' => $valueClass]) ?>
    <?php } ?>
    
    <?= $form->field($model, 'required')->checkbox(); ?>
    <?= $form->field($model, 'product_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
