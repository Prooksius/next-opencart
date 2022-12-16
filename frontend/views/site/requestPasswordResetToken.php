<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Запрос сброса пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <div class="container">
        <br />
        <br />
        <h1><?= Html::encode($this->title) ?></h1>
        <br />
        <br />
        <p>Пожалуйста, введите ваш Email. На него будет послана ссылка на смену пароля.</p>
        <br />
        <br />
        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                    <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'class' => 'input']) ?>
                    <br />
                    <br />
                    <div class="form-group">
                        <?= Html::submitButton('Отправить', ['class' => 'btn2']) ?>
                        <?= Html::a('Домой', ['/'], ['class' => 'btn1']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <br />
        <br />
        <br />
        <br />
    </div>
</div>
