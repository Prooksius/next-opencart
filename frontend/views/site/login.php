<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="site-login">
    <ul class="popup-list">
        <li>
            <a class="active">Вход</a>
        </li>
        <li>
            <a class="inactive" data-target="/site/signuppopup">Регистрация</a>
        </li>
    </ul>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'popup-form',
        ]
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'input', 'placeholder' => 'Логин или email*'])->label(false) ?>

        <?= $form->field($model, 'password')->passwordInput(['class' => 'input', 'placeholder' => 'Пароль'])->label(false) ?>

        <div class="popup-wrap1">
            <label>
                <input type="hidden" name="LoginForm[rememberMe]" value="0">
                <input type="checkbox" name="LoginForm[rememberMe]" value="1">
                <i></i>
                <span>Запомнить</span>
            </label>
            <?= Html::a('Забыли пароль?', ['site/request-password-reset']) ?>
        </div>

        <div class="form-group">
            <?= Html::Button('Войти', ['class' => 'btn2 sitelogin-button', 'data-link' => '/site/login', 'data-action' => 'login']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
