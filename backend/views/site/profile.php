<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form ActiveForm */

$this->title = "Профиль администратора";

?>
<div class="site-admin-profile">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label('Новый пароль') ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-admin-profile -->
