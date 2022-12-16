<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Reviews */
/* @var $form yii\widgets\ActiveForm */

$general_active = true;
$languages_active = false;

$errors = $model->getFirstErrors();
if (isset($errors['languages'])) {
  $general_active = false;
  $languages_active = true;
}

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
                'active' => $general_active,
            ],
            [
                'label' => YII::t('app', 'Language Data'),
                'content' => $this->render('_form/_language', [
                    'model' => $model,
                    'form' => $form,
                ]),
                'active' => $languages_active,
            ],
            [
                'label' => YII::t('product', 'Dimensions'),
                'content' => $this->render('_form/_dimensions', [
                    'model' => $model,
                    'form' => $form,
                ]),
            ],
            [
                'label' => YII::t('category', 'Categories'),
                'content' => $this->render('_form/_category', [
                    'model' => $model,
                    'form' => $form,
                ]),
            ],
            [
                'label' => YII::t('product', 'Related products'),
                'content' => $this->render('_form/_related', [
                    'model' => $model,
                    'form' => $form,
                ]),
            ],
            [
                'label' => YII::t('product', 'Other Color Products'),
                'content' => $this->render('_form/_colorRelated', [
                    'model' => $model,
                    'form' => $form,
                ]),
            ],
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(YII::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(YII::t('app', 'Cancel'), Yii::$app->request->referrer , ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
