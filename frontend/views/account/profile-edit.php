<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJs("$(\"input[type='tel']\").mask(\"+7(999)999-99-99\",{placeholder:\"+7(   )   -  -  \"});");

?>
<div class="site-login">
    <h2>Изменить профиль</h2>
    <?php $form = ActiveForm::begin([
        'id' => 'form-profile-edit',
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'popup-form',
        ]
    ]); ?>
    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'input']) ?>

    <?= $form->field($model, 'first_name')->textInput(['class' => 'input']) ?>

    <?= $form->field($model, 'email')->textInput(['type' => 'email', 'class' => 'input']) ?>

    <?= $form->field($model, 'phone')->textInput(['type' => 'tel', 'class' => 'input', 'placeholder' => '+7(   )   -  -  ']) ?>

    <?= $form->field($model, 'telegram')->textInput(['class' => 'input']) ?>

    <?= $form->field($model, 'oldPassword')->passwordInput(['class' => 'input', 'placeholder' => 'Старый пароль'])->label(false) ?>

    <?= $form->field($model, 'newPassword')->passwordInput(['class' => 'input', 'placeholder' => 'Новый пароль'])->label(false) ?>

    <?= $form->field($model, 'newPasswordRepeat')->passwordInput(['class' => 'input', 'placeholder' => 'Повторите пароль'])->label(false) ?>

    <div class="form-group">
        <?= Html::Button('Изменить', ['class' => 'btn2 sitelogin-button', 'data-action' => 'profileedit', 'data-link' => '/account/profile-edit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
