<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\components\ReCaptchaWidget;

$this->registerJs("$(\"input[type='tel']\").mask(\"+7(999)999-99-99\",{placeholder:\"+7(   )   -  -  \"});");
?>
<div class="price-wrap1">
    <p>Не знаете с какого стартовать? <br> Закажите консультацию</p>
    <?php $form = ActiveForm::begin([
        'action' => ['site/consult'],
        'id' => 'form-consult',
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'price-form',
        ]
    ]); ?>
    <?= $form->field($model, 'fio')->textInput(['class' => 'input', 'placeholder' => 'Имя'])->label(false) ?>
    <?= $form->field($model, 'phone')->textInput(['type' => 'tel', 'class' => 'input', 'placeholder' => '+7(   )   -  -  '])->label(false) ?>
    <?= $form->field($model, 'email')->textInput(['type' => 'email', 'class' => 'input', 'placeholder' => 'Email'])->label(false) ?>
    <?= $form->field($model, 'reCaptcha')->widget(ReCaptchaWidget::className())->label(false) ?>
    <div class="form-group">
        <?= Html::Button('Получить совет', ['type' => 'button', 'class' => 'btn2 sitelogin-button', 'data-action' => 'consultrequest', 'data-link' => '/site/consult', 'data-goal' => 'buttonClickTry']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
