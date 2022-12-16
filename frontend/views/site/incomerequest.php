<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\components\ReCaptchaWidget;

$this->registerJs("$(\"input[type='tel']\").mask(\"+7(999)999-99-99\",{placeholder:\"+7(   )   -  -  \"});");
?>
<div class="site-login">
    <h2>Запрос консультации</h2>
    <h3>Мы расскажем, как наиболее эффективно вложить ваши средства</h3>
    <?php $form = ActiveForm::begin([
        'id' => 'form-testrequest',
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'popup1-form',
        ]
    ]); ?>
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'input', 'placeholder' => 'Имя*'])->label(false) ?>

        <?= $form->field($model, 'phone')->textInput(['type' => 'tel', 'class' => 'input', 'placeholder' => '+7(   )   -  -  '])->label(false) ?>
        <?= $form->field($model, 'email')->textInput(['type' => 'email', 'class' => 'input', 'placeholder' => 'Email'])->label(false) ?>

        <div class="form-group">
            <?= Html::Button('Отправить', ['class' => 'btn2 sitelogin-button', 'data-action' => 'testrequest', 'data-link' => '/site/income-request']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
