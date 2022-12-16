<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SefSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sef-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'link') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'link_sef') ?>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <br />
                <?= Html::submitButton('Фильтр', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
