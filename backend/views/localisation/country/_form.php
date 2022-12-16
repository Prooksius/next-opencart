<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="faq-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => YII::t('app', 'General Data'),
                'content' => $this->render('_form/_general', [
                    'model' => $model,
                    'form' => $form,
                ]),
                'active' => true,
            ],
            [
                'label' => YII::t('app', 'Language Data'),
                'content' => $this->render('_form/_language', [
                    'model' => $model,
                    'form' => $form,
                    'languages' => $languages,
                ]),
            ],
        ]
    ]);?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
