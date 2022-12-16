<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use himiklab\yii2\recaptcha\ReCaptcha2;

$this->title = 'Contact | ' . \Yii::$app->name;
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="site-contact further-back-block" data-further="0" data-back="/blog">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1>Наши контакты</h1>

                    <p>
                        Если у вас есть вопросы, свяжитесь с нами, заполнив форму ниже. Спасибо.
                    </p>

                    <div class="row">
                        <div class="col-lg-5">
                            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                            <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>

                            <?= $form->field($model, 'email') ?>

                            <?= $form->field($model, 'subject') ?>

                            <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

                            <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha2::className())->label(false) ?>

                            <div class="form-group empty-right">
                                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<? $this->registerJsFile('js/my_scroll.js', ['depends' => 'yii\web\YiiAsset']); ?>